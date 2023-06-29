<template>
  <div class="app-container">
    <div class="el-row--flex">
      <el-form :inline="true" class="demo-form-inline">
        <el-button @click="openEdit" class="mini" type="primary" style="margin-right: 30px">添加用户</el-button>
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
            @click="handleEdit(scope.$index, scope.row)">编辑</el-button>
          <el-button
            size="mini"
            type="danger"
            @click="handleDelete(scope.$index, scope.row)">删除</el-button>
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
    <el-dialog title="新增用户" :visible.sync="addUserDialogFormVisible">
      <el-form :model="addUser">
        <el-form-item label="姓名" :label-width="formLabelWidth">
          <el-input v-model="addUser.name" autocomplete="off"></el-input>
        </el-form-item>
        <el-form-item label="身份证号/工号" :label-width="formLabelWidth">
          <el-input v-model="addUser.uniqid" autocomplete="off"></el-input>
        </el-form-item>
        <el-form-item label="密码" :label-width="formLabelWidth">
          <el-input v-model="addUser.password" type="password" autocomplete="off"></el-input>
        </el-form-item>
        <el-form-item label="角色" :label-width="formLabelWidth">
          <el-select v-model="addUser.gid" placeholder="请选择角色">
            <el-option label="普通用户" value="1"></el-option>
            <el-option label="扫码人员" value="2"></el-option>
            <el-option label="厂家" value="3"></el-option>
            <el-option label="超管" value="4"></el-option>
          </el-select>
        </el-form-item>
      </el-form>
      <div slot="footer" class="dialog-footer">
        <el-button @click="addUserDialogFormVisible = false">取 消</el-button>
        <el-button type="primary" @click="addUserMethod">确 定</el-button>
      </div>
    </el-dialog>

    <el-dialog title="用户修改" :visible.sync="dialogFormVisible">
      <el-form :model="editUser">
        <el-form-item label="姓名" :label-width="formLabelWidth">
          <el-input v-model="editUser.name" autocomplete="off"></el-input>
        </el-form-item>
        <el-form-item label="身份证号/工号" :label-width="formLabelWidth">
          <el-input v-model="editUser.uniqid" autocomplete="off"></el-input>
        </el-form-item>
        <el-form-item label="角色" :label-width="formLabelWidth">
          <el-select v-model="editUser.gid" placeholder="请选择角色">
            <el-option label="普通用户" value="1"></el-option>
            <el-option label="扫码人员" value="2"></el-option>
            <el-option label="厂家" value="3"></el-option>
            <el-option label="超管" value="4"></el-option>
          </el-select>
        </el-form-item>
      </el-form>
      <div slot="footer" class="dialog-footer">
        <el-button @click="dialogFormVisible = false">取 消</el-button>
        <el-button type="primary" @click="updateUser">确 定</el-button>
      </div>
    </el-dialog>
  </div>
</template>

<script>
import { getList, deleteUser, updateUser, addUser } from '@/api/table'

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
    addUserMethod() {
      addUser({ ...this.addUser }).then(res => {
        this.addUserDialogFormVisible = false
        this.$router.go(0)
        this.$message({
          type: 'success',
          message: '新增成功!'
        })
        this.addUser = {}
      })
    },
    handleEdit(index, data) {
      this.editUser = data
      this.dialogFormVisible = true
    },
    updateUser() {
      updateUser(this.editUser.id, {
        'name': this.editUser.name,
        'uniqid': this.editUser.uniqid,
        'gid': this.editUser.gid
      }).then(() => {
        this.dialogFormVisible = false
        this.$message({
          type: 'success',
          message: '更新成功!'
        })
        this.editUser = {}
      })
    },
    handleDelete(index, data) {
      this.$confirm('此操作将删除该用户, 是否继续?', '提示', {
        confirmButtonText: '确定',
        cancelButtonText: '取消',
        type: 'warning'
      }).then(() => {
        deleteUser(data.id).then(res => {
          this.$router.go(0)
          this.$message({
            type: 'success',
            message: '删除成功!'
          })
        })
      }).catch(() => {
        this.$message({
          type: 'info',
          message: '已取消删除'
        })
      })
      console.log(index)
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
