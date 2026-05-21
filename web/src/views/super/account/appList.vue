<template>
  <div v-loading="loading" class="dashboard-container">
    <div class="search-block">
      <el-form :inline="true" :model="search_form">
        <el-form-item>
          <el-date-picker v-model="search_form.time" type="datetimerange" value-format="timestamp" range-separator="至"
            start-placeholder="开始日期" end-placeholder="结束日期" />
        </el-form-item>
        <el-form-item>
          <el-input v-model="search_form.username" placeholder="搜索账号" />
        </el-form-item>
        <el-form-item>
          <el-input v-model="search_form.appid" placeholder="APPID" />
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
          <el-table-column prop="username" label="账号" />
          <el-table-column prop="appid" label="APPID" />
          <el-table-column prop="secret" label="SECRET" />
          <el-table-column label="创建时间" width="160" align="center">
            <template slot-scope="scope">
              <div>{{ scope.row.create_time * 1000 | formatDate }}</div>
            </template>
          </el-table-column>
          <el-table-column label="状态" width="80" align="center">
            <template slot-scope="scope">
              <el-tag v-if="scope.row.status === 1" disable-transitions>正常</el-tag>
              <el-tag v-if="scope.row.status === -1" disable-transitions type="info">禁用</el-tag>
            </template>
          </el-table-column>
          <el-table-column label="操作" width="80" fixed="right">
            <template slot-scope="scope">
              <el-button type="text" size="small" @click="bind_item_edit(scope.row)">编辑</el-button>
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
      <el-form ref="add_form" :model="add_form">
        <el-form-item label="账号" label-width="80px">
          <el-input v-model="add_form.username" autocomplete="off" placeholder="请输入账号" />
        </el-form-item>
        <el-form-item label="APPID" label-width="80px">
          <el-input placeholder="32个字符以内" v-model="add_form.appid">
            <el-button slot="append" @click="()=>{add_form.appid = generateRandomString()}">随机生成</el-button>
          </el-input>
        </el-form-item>
        <el-form-item label="SECRET" label-width="80px">
          <el-input placeholder="32个字符以内" v-model="add_form.secret">
            <el-button slot="append" @click="()=>{add_form.secret = generateRandomString()}">随机生成</el-button>
          </el-input>
        </el-form-item>
        <el-form-item label="状态" label-width="80px">
          <el-select v-model="add_form.status" placeholder="请选择">
            <el-option label="启用" :value="1" />
            <el-option label="停用" :value="-1" />
          </el-select>
        </el-form-item>
      </el-form>
      <div slot="footer" class="dialog-footer">
        <el-button @click="addVisible = false">取 消</el-button>
        <el-button type="primary" @click="submit_add">确 定</el-button>
      </div>
    </el-dialog>

    <el-dialog v-loading="loading" title="编辑" :visible.sync="editVisible">
      <el-form ref="edit_form" :model="edit_form">
        <el-form-item label="账号" label-width="80px">
          <el-input v-model="edit_form.username" autocomplete="off" placeholder="请输入账号" />
        </el-form-item>
        <el-form-item label="APPID" label-width="80px">
          <el-input placeholder="32个字符以内" v-model="edit_form.appid">
            <el-button slot="append" @click="()=>{edit_form.appid = generateRandomString()}">随机生成</el-button>
          </el-input>
        </el-form-item>
        <el-form-item label="SECRET" label-width="80px">
          <el-input placeholder="32个字符以内" v-model="edit_form.secret">
            <el-button slot="append" @click="()=>{edit_form.secret = generateRandomString()}">随机生成</el-button>
          </el-input>
        </el-form-item>
        <el-form-item label="状态" label-width="80px">
          <el-select v-model="edit_form.status" placeholder="请选择">
            <el-option label="启用" :value="1" />
            <el-option label="停用" :value="-1" />
          </el-select>
        </el-form-item>
      </el-form>
      <div slot="footer" class="dialog-footer">
        <el-button @click="editVisible = false">取 消</el-button>
        <el-button type="primary" @click="submit_edit">确 定</el-button>
      </div>
    </el-dialog>
  </div>
</template>

<script>
import setting from '@/settings.js'
export default {
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
        username: '',
        appid: '',
        secret: '',
        status: 1
      },
      editVisible: false,
      edit_form: {
        id: '',
        username: '',
        appid: '',
        secret: '',
        status: 1
      },
      multipleSelection: [], // 选中数据
      roles: {}
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
        .dispatch('super/appList/items', this.search_form)
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
        .dispatch('super/appList/add', this.add_form)
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
        .dispatch('super/appList/edit', this.edit_form)
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
            .dispatch('super/appList/del', { ids: ids })
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
	generateRandomString(){
      return Math.random().toString(36).substring(2, 15) + 
         Math.random().toString(36).substring(2, 15);
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
