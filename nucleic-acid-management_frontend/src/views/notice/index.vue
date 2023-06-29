<template>
  <div class="app-container">
    <el-button @click="openEdit" type="primary">添加公告</el-button>
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
      <el-table-column label="公告标题" width="200">
        <template slot-scope="scope">
          {{ scope.row.title }}
        </template>
      </el-table-column>
      <el-table-column label="公告内容" width="200" align="center">
        <template slot-scope="scope">
          {{ scope.row.content }}
        </template>
      </el-table-column>
      <el-table-column label="文件" width="110" align="center">
        <template slot-scope="scope">
          <div v-if="scope.row.media_url != null">
            <el-link type="primary" :href="baseUrl + scope.row.media_url">下载</el-link>
          </div>
        </template>
      </el-table-column>

      <el-table-column class-name="status-col" label="创建时间" width="200" align="center">
        <template slot-scope="scope">
          <i class="el-icon-time"/>
          <span>{{ scope.row.create_time }}</span>
        </template>
      </el-table-column>
      <el-table-column align="center" prop="created_at" label="更新时间" width="200">
        <template slot-scope="scope">
          <i class="el-icon-time"/>
          <span>{{ scope.row.update_time }}</span>
        </template>
      </el-table-column>

      <el-table-column align="center" prop="created_at" label="操作">
        <template slot-scope="scope">
          <el-button
            size="mini"
            @click="handleEdit(scope.$index, scope.row)">编辑
          </el-button>
          <el-button
            size="mini"
            type="danger"
            @click="handleDelete(scope.$index, scope.row)">删除
          </el-button>
        </template>
      </el-table-column>
    </el-table>
    <el-dialog title="新增公告" :visible.sync="addUserDialogFormVisible">
      <el-form :model="addUser">
        <el-form-item label="标题" :label-width="formLabelWidth">
          <el-input v-model="addUser.title" autocomplete="off"></el-input>
        </el-form-item>
        <el-form-item label="内容" :label-width="formLabelWidth">
          <el-input v-model="addUser.content" type="textarea" autocomplete="off"></el-input>
        </el-form-item>
        <el-form-item label="文件" :label-width="formLabelWidth">
          <template>
            <el-upload
              action="#"
              :limit="1"
              :multiple="false"
              :http-request="headAddUpload">
              <el-button size="small" type="primary">点击上传</el-button>
            </el-upload>
          </template>
        </el-form-item>
      </el-form>
      <div slot="footer" class="dialog-footer">
        <el-button @click="addUserDialogFormVisible = false">取 消</el-button>
        <el-button type="primary" @click="addUserMethod">确 定</el-button>
      </div>
    </el-dialog>

    <el-dialog title="修改" :visible.sync="dialogFormVisible">
      <el-form :model="editUser">
        <el-form-item label="标题" :label-width="formLabelWidth">
          <el-input v-model="editUser.title" autocomplete="off"></el-input>
        </el-form-item>
        <el-form-item label="内容" :label-width="formLabelWidth">
          <el-input v-model="editUser.content" type="textarea" autocomplete="off"></el-input>
        </el-form-item>
        <el-form-item label="附件" :label-width="formLabelWidth">
          <template>
            <el-link v-if="editUser.media_url != null" type="primary" @click="download(editUser.media_url)">下载</el-link>
            <el-upload
              action="#"
              :limit="1"
              :multiple="false"
              :http-request="headAddUpload1">
              <el-button size="small" type="primary">点击上传</el-button>
            </el-upload>
          </template>
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
import { getList, deleteNotice, updateNotice, uploadFile, addNotice } from '@/api/notice'

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
      baseUrl: 'http://8.130.44.188',
      list: null,
      listLoading: true,
      dialogFormVisible: false,
      addUserDialogFormVisible: false,
      formLabelWidth: '120px',
      editUser: {},
      addUser: {},
      fileDate: {}
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
    download(url) {
      // window.open(url, '_self');
      window.location.href = this.baseUrl + url
    },
    addUserMethod() {
      addNotice(this.addUser).then(res => {
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
    headAddUpload(params) {
      const param = new FormData()
      param.append('file', params.file)
      uploadFile(param).then(res => {
        this.addUser.media_url = res.data.media_url
      })
    },
    headAddUpload1(params) {
      const param = new FormData()
      param.append('file', params.file)
      uploadFile(param).then(res => {
        this.editUser.media_url = res.data.media_url
      })
    },
    updateUser() {
      updateNotice(this.editUser.id, this.editUser).then(() => {
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
        deleteNotice(data.id).then(res => {
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
    fetchData() {
      this.listLoading = true
      getList().then(response => {
        console.log(response)
        this.list = response.data
        this.listLoading = false
      })
    }
  }
}
</script>
