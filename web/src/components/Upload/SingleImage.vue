<template>
  <div class="upload-container">
    <el-button type="primary" size="mini" @click="dialogVisible = true">上传图片</el-button>
    <el-dialog title="上传图片" :visible.sync="dialogVisible" :modal="modal">
      <div>
        <el-upload ref="upload" :on-success="handleImageSuccess" drag :multiple="false" :action="upload.action"
          :headers="upload.headers" name="image" :show-file-list="false">

          <img v-if="tempUrl" :src="tempUrl" style="height:100%;width:auto;">
          <div v-else>
            <i class="el-icon-upload" />
            <div class="el-upload__text">将图片拖到此处，或<em>点击上传</em></div>
          </div>
        </el-upload>
      </div>
      <span slot="footer" class="dialog-footer">
        <el-button @click="dialogVisible = false">取 消</el-button>
        <el-button type="primary" style="clear: both" @click="confim">确 定</el-button>
      </span>
    </el-dialog>
  </div>
</template>

<script>
export default {
  name: 'SingleImageUpload',
  props: {
    modal: {
      type: Boolean,
      default: true
    }
  },
  data() {
    return {
      tempUrl: '',
      upload: {},
      dialogVisible: false
    }
  },
  computed: {},
  mounted() {
    this.$store
      .dispatch('user/uploadinfo')
      .then((res) => {
        this.upload = res
      })
      .catch(() => { })
  },
  methods: {
    emitInput(val) {
      this.$emit('input', val)
    },
    handleImageSuccess(response, file) {
      if (response.code == 1) {
        this.tempUrl = response.data.url
      } else {
        this.$refs.upload.clearFiles()
        this.$message(response.msg)
        return
      }
    },
    confim() {
      if (this.tempUrl != '') {
        this.emitInput(this.tempUrl)
      }
      this.dialogVisible = false
    }
  }
}
</script>
