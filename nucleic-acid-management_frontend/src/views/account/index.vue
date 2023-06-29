<template>
  <div class="app-container">
    <div class="el-row--flex">
      <el-form :inline="true" class="demo-form-inline">
        <el-form-item label="姓名">
          <el-input placeholder="姓名" v-model="queryName"></el-input>
        </el-form-item>
        <el-form-item>
          <el-button type="primary" @click="fetchData">查询</el-button>
        </el-form-item>
      </el-form>
    </div>

    <el-table
      v-loading="listLoading"
      :data="list"
      element-loading-text="Loading"
      border
      fit
      highlight-current-row
    >
      <el-table-column align="center" label="ID" width="95">
        <template slot-scope="scope">
          {{ scope.row.id }}
        </template>
      </el-table-column>
      <el-table-column label="姓名"  >
        <template slot-scope="scope">
          {{ scope.row.name }}
        </template>
      </el-table-column>
      <el-table-column label="角色" width="110" align="center">
        <template slot-scope="scope">
          <span>{{scope.row.gid == 1?'普通用户':''}}</span>
          <span>{{scope.row.gid == 2?'扫码人员':''}}</span>
          <span>{{scope.row.gid == 3?'厂家':''}}</span>
          <span>{{scope.row.gid == 4?'超管':''}}</span>
        </template>
      </el-table-column>
      <el-table-column label="身份证号/工号" width="110" align="center">
        <template slot-scope="scope">
          {{ scope.row.uniqid }}
        </template>
      </el-table-column>
      <el-table-column class-name="status-col" label="创建时间" width="200" align="center">
        <template slot-scope="scope">
          <i class="el-icon-time" />
          <span>{{ scope.row.create_time }}</span>
        </template>
      </el-table-column>
      <el-table-column align="center" prop="created_at" label="更新时间" width="200">
        <template slot-scope="scope">
          <i class="el-icon-time" />
          <span>{{ scope.row.update_time }}</span>
        </template>
      </el-table-column>

      <el-table-column align="center" prop="created_at" label="操作" width="200">
        <template slot-scope="scope">
          <el-button
            size="mini"
            @click="handleEdit(scope.$index, scope.row)">审批通过</el-button>
        </template>
      </el-table-column>
    </el-table>

    <el-pagination
      small
      layout="prev, pager, next"
      :current-page="currentPage"
      @current-change="handleCurrentChange"
      :total="total">
    </el-pagination>
  </div>
</template>

<script>
import { getList, updateStatus } from '@/api/account'

export default {
  filters: {
    statusFilter(status) {
      const statusMap = {
        published: 'success',
        draft: 'gray',
        deleted: 'danger'
      }
      return statusMap[status]
    }
  },
  data() {
    return {
      queryName: '',
      list: null,
      listLoading: true,
      dialogFormVisible: false,
      addUserDialogFormVisible: false,
      formLabelWidth: '120px',
      editUser: {},
      addUser: {},
      total: 0,
      currentPage: 1
    }
  },
  created() {
    this.fetchData()
  },
  methods: {
    openEdit() {
      this.addUser = {}
      this.addUserDialogFormVisible = true
    },
    handleEdit(index, data) {
      this.editUser = data
      updateStatus(data.id).then(
        res => {
          console.log(res)
          this.$router.go(0)
          this.$message({
            type: 'success',
            message: '审核通过!'
          })
        }
      )
    },
    handleCurrentChange(val) {
      this.currentPage = val
      this.fetchData()
    },
    fetchData() {
      this.listLoading = true
      console.log(this.currentPage)
      getList(this.queryName, (this.currentPage - 1) * 10).then(response => {
        this.list = response.data
        this.total = response.count
        this.listLoading = false
      })
    }
  }
}
</script>
