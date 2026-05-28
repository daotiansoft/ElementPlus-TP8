<!-- 动态表单 -->
<template>
  <el-form ref="dynamicForm" :model="formData" :rules="formRules" :label-width="labelWidth" :size="size">
    <el-form-item v-for="(field, index) in fields" :key="field.prop || index" :label="field.label" :prop="field.prop"
      :required="field.required">
      <!-- input 输入框 -->
      <el-input v-if="field.type === 'input'" v-model="formData[field.prop]"
        :placeholder="field.placeholder || `请输入${field.label}`" :disabled="field.disabled"
        :clearable="field.clearable !== false" :show-password="field.showPassword" :maxlength="field.maxlength"
        :show-word-limit="field.showWordLimit" @change="handleChange(field, $event)" />

      <!-- textarea 文本域 -->
      <el-input v-else-if="field.type === 'textarea'" v-model="formData[field.prop]" type="textarea"
        :rows="field.rows || 3" :placeholder="field.placeholder || `请输入${field.label}`" :disabled="field.disabled"
        :maxlength="field.maxlength" :show-word-limit="field.showWordLimit" @change="handleChange(field, $event)" />

      <!-- number 数字输入 -->
      <el-input-number v-else-if="field.type === 'number'" v-model="formData[field.prop]" :min="field.min"
        :max="field.max" :step="field.step || 1" :disabled="field.disabled" @change="handleChange(field, $event)" />

      <!-- select 下拉选择 -->
      <el-select v-else-if="field.type === 'select'" v-model="formData[field.prop]"
        :placeholder="field.placeholder || `请选择${field.label}`" :disabled="field.disabled"
        :clearable="field.clearable !== false" :filterable="field.filterable" :multiple="field.multiple"
        @change="handleChange(field, $event)">
        <el-option v-for="option in field.options" :key="option.value" :label="option.label" :value="option.value"
          :disabled="option.disabled" />
      </el-select>

      <!-- radio 单选框组 -->
      <el-radio-group v-else-if="field.type === 'radio'" v-model="formData[field.prop]" :disabled="field.disabled"
        @change="handleChange(field, $event)">
        <el-radio v-for="option in field.options" :key="option.value" :label="option.value" :disabled="option.disabled">
          {{ option.label }}
        </el-radio>
      </el-radio-group>

      <!-- checkbox 复选框组 -->
      <el-checkbox-group v-else-if="field.type === 'checkbox'" v-model="formData[field.prop]" :disabled="field.disabled"
        @change="handleChange(field, $event)">
        <el-checkbox v-for="option in field.options" :key="option.value" :label="option.value"
          :disabled="option.disabled">
          {{ option.label }}
        </el-checkbox>
      </el-checkbox-group>

      <!-- switch 开关 -->
      <el-switch v-else-if="field.type === 'switch'" v-model="formData[field.prop]" :disabled="field.disabled"
        :active-text="field.activeText" :inactive-text="field.inactiveText" @change="handleChange(field, $event)" />

      <!-- date 日期选择器 -->
      <el-date-picker v-else-if="field.type === 'date'" v-model="formData[field.prop]" :type="field.dateType || 'date'"
        :placeholder="field.placeholder || `请选择${field.label}`" :disabled="field.disabled"
        :clearable="field.clearable !== false" :format="field.format" :value-format="field.valueFormat"
        @change="handleChange(field, $event)" />

      <!-- upload 上传组件 -->
      <div v-else-if="field.type === 'inputImageUpload'"">
        <el-input
            v-model="formData[field.prop]" :placeholder="field.placeholder || `请输入${field.label}`"
        :disabled="field.disabled" :clearable="field.clearable !== false">
        <el-upload slot="append" :on-success="(res, file, fileList) => handleUploadSuccess(field, res, file, fileList)"
          :multiple="false" :action="uploadConfig.action || ''" :headers="uploadConfig.headers" name="image"
          :show-file-list="false">
          <i class="el-icon-plus avatar-uploader-icon"></i>
        </el-upload>
        </el-input>
      </div>

      <!-- 自定义插槽 -->
      <slot v-else-if="field.type === 'slot'" :name="field.slotName" :field="field" :value="formData[field.prop]" />

      <!-- 错误提示 -->
      <div v-if="field.type === 'upload'" class="upload-error-tip">
        <span v-if="uploadErrors[field.prop]" class="error-text">{{ uploadErrors[field.prop] }}</span>
      </div>
    </el-form-item>

    <!-- 表单操作按钮 -->
    <el-form-item v-if="showActions">
      <el-button v-if="actions.cancel !== false" :type="actions.cancelType || 'default'" @click="handleCancel">
        {{ actions.cancelText || '取消' }}
      </el-button>
      <el-button v-if="actions.submit !== false" :type="actions.submitType || 'primary'" :loading="submitLoading"
        @click="handleSubmit">
        {{ actions.submitText || '提交' }}
      </el-button>
      <el-button v-if="actions.reset !== false" :type="actions.resetType || 'default'" @click="handleReset">
        {{ actions.resetText || '重置' }}
      </el-button>
    </el-form-item>
  </el-form>
</template>

<script>
export default {
  name: 'DynamicForm',
  props: {
    // 表单字段配置
    fields: {
      type: Array,
      required: true,
      validator: (value) => {
        if (!Array.isArray(value)) return false
        return value.every(field => field.prop && field.type)
      }
    },
    // 表单数据
    value: {
      type: Object,
      default: () => ({})
    },
    // 标签宽度
    labelWidth: {
      type: String,
      default: '100px'
    },
    // 组件尺寸
    size: {
      type: String,
      default: 'medium',
      validator: (value) => ['large', 'medium', 'small', 'mini'].includes(value)
    },
    // 是否显示操作按钮
    showActions: {
      type: Boolean,
      default: true
    },
    // 按钮配置
    actions: {
      type: Object,
      default: () => ({})
    }
  },
  data() {
    return {
      formData: {},
      uploadErrors: {},
      submitLoading: false,
      customValidators: {},
      uploadConfig: {}
    }
  },
  computed: {
    // 动态生成表单校验规则
    formRules() {
      const rules = {}
      this.fields.forEach(field => {
        if (field.rules && field.rules.length) {
          rules[field.prop] = field.rules
        } else if (field.required) {
          rules[field.prop] = [
            { required: true, message: `${field.label}不能为空`, trigger: 'blur' }
          ]
        }

        // 添加自定义校验
        if (field.validator && typeof field.validator === 'function') {
          if (!rules[field.prop]) rules[field.prop] = []
          rules[field.prop].push({ validator: field.validator, trigger: field.trigger || 'blur' })
        }
      })
      return rules
    }
  },
  watch: {
    value: {
      handler(newVal) {
        this.formData = { ...this.formData, ...newVal }
      },
      deep: true,
      immediate: true
    },
    fields: {
      handler() {
        this.initFormData()
      },
      immediate: true
    }
  },
  mounted() {
    this.$store
      .dispatch('user/uploadinfo')
      .then((res) => {
        this.uploadConfig = res
      })
      .catch(() => { })
  },
  methods: {
    // 初始化表单数据
    initFormData() {
      const defaultData = {}
      this.fields.forEach(field => {
        if (this.value[field.prop] !== undefined) {
          defaultData[field.prop] = this.value[field.prop]
        } else if (field.defaultValue !== undefined) {
          defaultData[field.prop] = field.defaultValue
        } else if (field.type === 'checkbox' && field.multiple !== false) {
          defaultData[field.prop] = []
        } else {
          defaultData[field.prop] = ''
        }
      })
      this.formData = defaultData
    },

    // 字段变化事件
    handleChange(field, value) {
      this.$emit('change', { field, value, formData: this.formData })
      if (field.onChange && typeof field.onChange === 'function') {
        field.onChange(value, this.formData)
      }
    },

    handleUploadSuccess(field, response, file, fileList) {
      if (response.code === 1) {
        this.setFieldValue(field.prop, response.data)
      } else {
        this.$message(response.msg)
        return
      }
    },

    // 提交表单
    handleSubmit() {
      this.$refs.dynamicForm.validate((valid) => {
        if (valid) {
          this.submitLoading = true

          // 处理上传组件的文件URL
          const submitData = { ...this.formData }
          this.$emit('submit', submitData, this.formData)

          // 如果不需要外部处理loading，可以在这里关闭
          setTimeout(() => {
            this.submitLoading = false
          }, 500)
        } else {
          this.$emit('validate-error')
          return false
        }
      })
    },

    // 重置表单
    handleReset() {
      this.initFormData()
      this.initUploadErrors()
      this.$refs.dynamicForm.clearValidate()
      this.$emit('reset', this.formData)
    },

    // 取消
    handleCancel() {
      this.$emit('cancel')
    },

    // 外部调用验证
    validate() {
      return this.$refs.dynamicForm.validate()
    },

    // 外部调用重置字段验证
    clearValidate(props) {
      this.$refs.dynamicForm.clearValidate(props)
    },

    // 设置字段值
    setFieldValue(prop, value) {
      this.$set(this.formData, prop, value)
      this.$emit('input', this.formData)
    },

    // 获取字段值
    getFieldValue(prop) {
      return this.formData[prop]
    }
  }
}
</script>

<style scoped>
.error-text {
  font-size: 12px;
  color: #f56c6c;
}

.drag-area {
  min-height: 120px;
}
</style>