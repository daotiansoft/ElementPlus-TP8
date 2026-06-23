<template>
    <div class="upload-container">
        <el-upload ref="upload" :on-success="handleImageSuccess" :multiple="false" :action="upload.action"
            :headers="upload.headers" name="image" :show-file-list="false">
            <i class="el-icon-plus avatar-uploader-icon"></i>
        </el-upload>
    </div>
</template>

<script>
export default {
    name: 'InputImageUpload',
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
