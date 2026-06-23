<template>
    <el-upload ref="upload" :on-success="handleImageSuccess" :multiple="false" :action="upload.action"
        :headers="upload.headers" name="image" :show-file-list="false">
        <el-button type="primary" size="small">上传<i class="el-icon-upload el-icon--right"></i></el-button>
    </el-upload>
</template>

<script>
export default {
    name: 'BtnImageUpload',
    props: {
    },
    data() {
        return {
            upload: {}
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
            if (response.code === 1) {
                this.emitInput(response.data.url)
            } else {
                this.$refs.upload.clearFiles()
                this.$message(response.msg)
                return
            }
        }
    }
}
</script>
