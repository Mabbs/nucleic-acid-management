<template>
  <div>
    <el-dialog title="新增公告" :visible.sync="dialogFormVisible" :before-close="handleClose">
      <el-form :model="updateUser">
        <el-form-item label="新密码" :label-width="formLabelWidth">
          <el-input v-model="updateUser.password" type="password" autocomplete="off" />
        </el-form-item>
        <el-form-item label="再次输入密码" :label-width="formLabelWidth">
          <el-input v-model="updateUser.password1" type="password" autocomplete="off" />
        </el-form-item>
      </el-form>
      <div slot="footer" class="dialog-footer">
        <el-button @click="handleClose">取 消</el-button>
        <el-button type="primary" @click="updateUserMethod">确 定</el-button>
      </div>
    </el-dialog>
  </div>

</template>

<script>
import { getToken, getUniqid, getUserId, getUserName } from '@/utils/auth'
import { updateUser } from '@/api/table'

export default {
  data() {
    return {
      updateUser: {},
      dialogFormVisible: true
    }
  },
  methods: {
    handleClose() {
      this.$router.push({ path: this.redirect || '/' })
    },
    updateUserMethod() {
      if (this.updateUser.password !== this.updateUser.password1) {
        this.$message({
          type: 'error',
          message: '两次密码不一样'
        })
        return
      }
      this.updateUser.gid = getToken()
      this.updateUser.name = getUserName()
      this.updateUser.uniqid = getUniqid()
      const userId = getUserId()
      const param = new FormData()
      param.append('name', this.updateUser.name)
      param.append('gid', this.updateUser.gid)
      param.append('password', this.updateUser.password)
      updateUser(userId, this.updateUser).then(async res => {
        this.$message({
          type: 'success',
          message: '修改成功'
        })
        await this.$store.dispatch('user/logout')
        this.$router.push(`/login?redirect=${this.$route.fullPath}`)
      })
    }
  }
}
</script>

<style scoped>
</style>
