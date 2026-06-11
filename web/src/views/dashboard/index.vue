<template>
  <div class="dashboard-container">
    <el-card class="box-card">
      <div slot="header" class="clearfix">
        <span>公告</span>
      </div>
      <div v-html="notice" />
    </el-card>
  </div>
</template>

<script>
import { mapGetters } from 'vuex'

export default {
  computed: {
    ...mapGetters(['userinfo'])
  },
  data() {
    return {
      notice: ''
    }
  },
  created() {
    this.load_notice()
  },
  methods: {
    load_notice: function () {
      var _self = this
      this.$store
        .dispatch('user/notice')
        .then((res) => {
          _self.notice = res['content']
        })
        .catch(() => { })
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
