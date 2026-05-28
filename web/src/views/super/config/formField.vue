<template>
  <div v-loading="loading" class="dashboard-container">
    <div class="search-block">
      <el-form :inline="true" :model="search_form">
        <el-form-item>
          <el-date-picker v-model="search_form.time" type="datetimerange" value-format="timestamp" range-separator="至"
            start-placeholder="开始日期" end-placeholder="结束日期" />
        </el-form-item>
        <el-form-item>
          <el-input v-model="search_form.name" placeholder="搜索名称" />
        </el-form-item>
        <el-form-item>
          <el-input v-model="search_form.key" placeholder="搜索唯一标识" />
        </el-form-item>
        <el-form-item>
          <el-button type="primary" @click="submit_search">查询</el-button>
          <el-button @click="addVisible = true">新增</el-button>
          <el-button type="danger" @click="del">删除</el-button>
        </el-form-item>
      </el-form>
    </div>

    <div class="data-list">
      <template>
        <el-table v-loading="loading" :data="items" style="width: 100%" empty-text="暂无数据"
          @selection-change="handleSelectionChange">
          <el-table-column type="selection" width="55" />
          <el-table-column prop="id" label="ID" width="80" />
          <el-table-column prop="name" label="名称" />
          <el-table-column prop="key" label="唯一标识" />
          <el-table-column label="创建时间" width="160" align="center">
            <template slot-scope="scope">
              <div>{{ scope.row.create_time * 1000 | formatDate }}</div>
            </template>
          </el-table-column>
          <el-table-column label="操作" width="120" fixed="right">
            <template slot-scope="scope">
              <el-button type="text" size="small" @click="bind_item_edit(scope.row)">编辑</el-button>
              <el-button type="text" size="small" @click="bind_item_field(scope.row)">字段</el-button>
            </template>
          </el-table-column>
        </el-table>
      </template>
    </div>
    <div class="page-block" style="text-align: center;margin: 10px 0;">
      <el-pagination background layout="prev, pager, next" :total="total" :small="true" :hide-on-single-page="true"
        :page-size="search_form.pagesize" @current-change="current_change" />
    </div>
    <el-dialog v-loading="loading" title="添加" :visible.sync="addVisible">
      <DynamicForm ref="myForm" v-model="formData" :fields="formFields" :label-width="'120px'" :show-actions="true"
        :actions="actionsConfig" @submit="handleSubmit" @change="handleChange" @reset="handleReset" />
    </el-dialog>

    <el-dialog v-loading="loading" title="编辑" :visible.sync="editVisible">
      <el-form ref="edit_form" :model="edit_form">
        <el-form-item label="唯一标识" label-width="80px">
          <el-input v-model="edit_form.key" autocomplete="off" placeholder="请输入唯一标识" />
        </el-form-item>
        <el-form-item label="名称" label-width="80px">
          <el-input v-model="edit_form.name" autocomplete="off" placeholder="请输入名称" />
        </el-form-item>
      </el-form>
      <div slot="footer" class="dialog-footer">
        <el-button @click="editVisible = false">取 消</el-button>
        <el-button type="primary" @click="submit_edit">确 定</el-button>
      </div>
    </el-dialog>

    <el-dialog v-loading="loading" title="字段" :visible.sync="fieldVisible">
      <el-form ref="edit_form" :model="edit_form">
        <el-form-item label="唯一标识" label-width="80px">
          <el-input v-model="field_form.key" autocomplete="off" placeholder="请输入唯一标识" />
        </el-form-item>
        <el-form-item label="名称" label-width="80px">
          <el-input v-model="field_form.name" autocomplete="off" placeholder="请输入名称" />
        </el-form-item>


        <el-row :gutter="20">
          <el-col :span="6">
            <el-form-item label="类型" label-width="80px">
              <el-select v-model="field_template.stypetatus" placeholder="请选择">
                <el-option label="文本型" value="text" />
                <el-option label="下拉选项" value="select" />
              </el-select>
            </el-form-item>
          </el-col>
          <el-col :span="6">
            <el-form-item label="字段" label-width="80px">
              <el-input v-model="field_template.key" autocomplete="off" placeholder="仅支持字母" />
            </el-form-item>
          </el-col>
          <el-col :span="6">
            <el-form-item label="标题" label-width="80px">
              <el-input v-model="field_template.title" autocomplete="off" placeholder="前端展示" />
            </el-form-item>
          </el-col>
          <el-col :span="6">
            <el-form-item label="描述" label-width="80px">
              <el-input v-model="field_template.placeholder" autocomplete="off" placeholder="提示语，可空" />
            </el-form-item>
          </el-col>
        </el-row>
      </el-form>
      <div slot="footer" class="dialog-footer">
        <el-button @click="fieldVisible = false">取 消</el-button>
        <el-button type="primary" @click="submit_field">确 定</el-button>
      </div>
    </el-dialog>
  </div>
</template>

<script>
import setting from '@/settings.js'
import DynamicForm from '@/components/DynamicForm.vue';

export default {
  components: {
    DynamicForm
  },
  data() {
    return {
      search_form: {
        keyword: '',
        time: '',
        page: 1,
        pagesize: 20
      },
      items: [],
      total: 0, // 总条数
      loading: false,
      addVisible: false,
      add_form: {
        name: '',
        key: '',
      },
      editVisible: false,
      edit_form: {
        id: '',
        name: '',
        key: '',
      },
      fieldVisible: false,
      field_form: {
        key: '',
        field_json: {},
      },
      multipleSelection: [], // 选中数据
      roles: {},
      field_template: {
        type: 'text',
        key: '',
        name: '',
        placeholder: ''
      },



      formData: {
        username: '',
        age: 18,
        gender: 'male',
        hobbies: ['reading'],
        status: true,
        birthday: '',
        avatar: ''
      },
      formFields: [
        {
          prop: 'username',
          label: '用户名',
          type: 'input',
          placeholder: '请输入用户名',
          required: true,
          maxlength: 20,
          showWordLimit: true,
          rules: [
            { min: 3, max: 20, message: '长度在 3 到 20 个字符', trigger: 'blur' },
            { required: true, message: '请输入用户名', trigger: 'blur' },
          ]
        },
        {
          prop: 'bio',
          label: '个人简介',
          type: 'textarea',
          rows: 4,
          maxlength: 200,
          showWordLimit: true
        },
        {
          prop: 'age',
          label: '年龄',
          type: 'number',
          min: 0,
          max: 150,
          required: true
        },
        {
          prop: 'gender',
          label: '性别',
          type: 'radio',
          defaultValue: 'male',
          options: [
            { label: '男', value: 'male' },
            { label: '女', value: 'female' }
          ]
        },
        {
          prop: 'hobbies',
          label: '兴趣爱好',
          type: 'checkbox',
          options: [
            { label: '阅读', value: 'reading' },
            { label: '音乐', value: 'music' },
            { label: '运动', value: 'sports' },
            { label: '旅行', value: 'travel' }
          ]
        },
        {
          prop: 'city',
          label: '城市',
          type: 'select',
          placeholder: '请选择城市',
          clearable: true,
          filterable: true,
          options: [
            { label: '北京', value: 'beijing' },
            { label: '上海', value: 'shanghai' },
            { label: '广州', value: 'guangzhou' },
            { label: '深圳', value: 'shenzhen' }
          ]
        },
        {
          prop: 'status',
          label: '启用状态',
          type: 'switch',
          activeText: '启用',
          inactiveText: '禁用',
          defaultValue: true
        },
        {
          prop: 'birthday',
          label: '出生日期',
          type: 'date',
          dateType: 'date',
          format: 'yyyy-MM-dd',
          valueFormat: 'timestamp',
          placeholder: '请选择出生日期'
        },
        {
          prop: 'avatar',
          label: '头像上传',
          type: 'inputImageUpload',
          placeholder: '请选择头像图片'
        }
      ],
      actionsConfig: {
        submitText: '保存',
        submitType: 'primary',
        resetText: '重置',
        cancelText: '返回',
        cancelType: 'default'
      }

    }
  },
  created() {
    this.load_items()
    this.roles = setting.roles
  },
  methods: {
    submit_search() {
      this.search_form.page = 1
      this.load_items()
      return false
    },
    load_items() {
      this.loading = true
      this.$store
        .dispatch('super/formField/items', this.search_form)
        .then(res => {
          const count = res.count
          const items = res.items

          this.items = items
          this.total = count

          this.loading = false
        })
        .catch(() => {
          this.loading = false
        })
    },
    current_change(page) {
      this.search_form.page = page
      this.load_items()
    },
    submit_add() {
      this.loading = true
      this.$store
        .dispatch('super/formField/add', this.add_form)
        .then(res => {
          this.$message({
            message: res,
            type: 'success'
          })
          this.loading = false
          this.addVisible = false
          this.load_items()
        })
        .catch(() => {
          this.loading = false
        })
    },
    bind_item_edit(item) {
      this.edit_form = item
      this.editVisible = true
    },
    submit_edit() {
      this.loading = true
      this.$store
        .dispatch('super/formField/edit', this.edit_form)
        .then(res => {
          this.$message({
            message: res,
            type: 'success'
          })
          this.loading = false
          this.editVisible = false
          this.load_items()
        })
        .catch(() => {
          this.loading = false
        })
    },
    handleSelectionChange(val) {
      this.multipleSelection = val
    },
    del() {
      var items = this.multipleSelection
      if (items.length <= 0) {
        this.$message({
          message: '至少选中一个',
          type: 'error'
        })
        return
      }
      this.$confirm('此操作将永久删除选中数据, 是否继续?', '提示', {
        confirmButtonText: '确定',
        cancelButtonText: '取消',
        type: 'warning'
      })
        .then(() => {
          var ids = []
          items.forEach(item => {
            ids.push(item.id)
          })
          this.loading = true
          this.$store
            .dispatch('super/formField/del', { ids: ids })
            .then(res => {
              this.$message({
                message: res,
                type: 'success'
              })
              this.loading = false
              this.load_items()
            })
            .catch(() => {
              this.loading = false
            })
        })
        .catch(() => { })
    },
    bind_item_field(item) {
      this.field_form = item
      this.fieldVisible = true
    },
    submit_field() {
      this.fieldVisible = false
    },
    handleSubmit(data) {
      console.log('提交数据:', data)
      // 调用API保存数据
      this.$message.success('保存成功')
    },
    handleChange({ field, value, formData }) {
      console.log(`${field.label} 改变为:`, value)
    },
    handleReset(formData) {
      console.log('表单已重置', formData)
    },
    // 外部调用验证
    validateForm() {
      this.$refs.myForm.validate().then(() => {
        console.log('表单验证通过')
      }).catch(() => {
        console.log('表单验证失败')
      })
    }
  }
}
</script>

<style lang="scss" scoped>
.dashboard {
  &-container {
    margin: 30px;
  }

  &-text {
    font-size: 30px;
    line-height: 46px;
  }
}
</style>
