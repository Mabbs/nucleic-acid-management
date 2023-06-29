<template>
  <div class="app-container">

    <div class="el-row--flex">
      <el-form :inline="true" class="demo-form-inline">
        <el-button @click="openEdit" class="mini" type="primary" style="margin-right: 30px">添加管试剂</el-button>
        <el-form-item label="编号">
          <el-input v-model="queryName" placeholder="编号"></el-input>
        </el-form-item>
        <el-form-item label="状态">
          <el-select v-model="queryStatus" placeholder="请选择状态">
            <el-option label="" value=""></el-option>
            <el-option label="未检出" value="0"></el-option>
            <el-option label="阴性" value="1"></el-option>
            <el-option label="阳性" value="2"></el-option>
          </el-select>
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
      <el-table-column align="center" label="ID" width="200">
        <template slot-scope="scope">
          {{ scope.row.id }}
        </template>
      </el-table-column>
      <el-table-column label="厂家" width="200" >
        <template slot-scope="scope">
          {{ scope.row.name }}
        </template>
      </el-table-column>
      <el-table-column label="编号" width="200" align="center">
        <template slot-scope="scope">
          {{ scope.row.serial }}
        </template>
      </el-table-column>
      <el-table-column label="状态" width="110" align="center">
        <template slot-scope="scope">
          <span>{{scope.row.status == 0?'未检出':''}}</span>
          <span>{{scope.row.status == 1?'阴性':''}}</span>
          <span>{{scope.row.status == 2?'阳性':''}}</span>
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

      <el-table-column align="center" prop="created_at" label="操作" >
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
      :total="total"
      @current-change="handleCurrentChange">
    </el-pagination>
    <el-dialog title="新增管试剂" :visible.sync="addUserDialogFormVisible">
      <el-form :model="addUser">
        <el-form-item label="编号" :label-width="formLabelWidth">
          <el-input  v-model="addUser.serial" autocomplete="off"></el-input>
        </el-form-item>
      </el-form>
      <div slot="footer" class="dialog-footer">
        <el-button @click="addUserDialogFormVisible = false">取 消</el-button>
        <el-button type="primary" @click="addUserMethod">确 定</el-button>
      </div>
    </el-dialog>

    <el-dialog title="管试剂修改" :visible.sync="dialogFormVisible">
      <el-form :model="editUser">
        <el-form-item label="厂家" :label-width="formLabelWidth">
          <el-input disabled v-model="editUser.name" autocomplete="off"></el-input>
        </el-form-item>
        <el-form-item label="编号" :label-width="formLabelWidth">
          <el-input disabled v-model="editUser.serial" autocomplete="off"></el-input>
        </el-form-item>
        <el-form-item label="状态" :label-width="formLabelWidth">
          <el-select v-model="editUser.status" placeholder="请选择状态">
            <el-option label="未检出" value="0"></el-option>
            <el-option label="阴性" value="1"></el-option>
            <el-option label="阳性" value="2"></el-option>
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
import { getList, deleteMedicin, updateMedicine, addMedicine } from '@/api/medicine'

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
      queryStatus: '',
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
      addMedicine({ serial: this.addUser.serial }).then(res => {
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
      updateMedicine(this.editUser.serial, {
        'status': this.editUser.status
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
        deleteMedicin(data.id).then(res => {
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
      getList(this.queryName, this.queryStatus, (this.currentPage - 1) * 10).then(response => {
        console.log(response)
        this.list = response.data
        this.listLoading = false
      })
    }
  }
}
</script>
