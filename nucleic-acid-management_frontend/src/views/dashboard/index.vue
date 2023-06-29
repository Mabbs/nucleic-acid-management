<template>
  <div class="dashboard-container">
<!--    <div class="dashboard-text">name: {{ name }}</div>-->
    <el-row>
      <el-col :span="12" style="margin-bottom: 20px">
        <el-card style="margin-bottom: 20px">
          <div style="padding: 14px;">
            <span class="font-m">微信注册人数</span><span class="font-size" style="color: #20a0ff">{{ dataList.WechatUserCount }}</span>
          </div>
        </el-card>
        <el-card >
          <div style="padding: 14px;">
            <span class="font-m">试管的统计数</span><span class="font-size" style="color: #20a0ff">{{ dataList.KitCount }}</span>
          </div>
        </el-card>
      </el-col>
      <el-col :span="12">
        <el-card style="margin-bottom: 20px">
          <div style="padding: 14px;">
            <span class="font-m">今天注册的人数</span><span class="font-size" style="color: #20a0ff">{{ dataList.TodayRegisterCount }}</span>
          </div>
        </el-card>
        <el-card >
          <div style="padding: 14px;">
            <span class="font-m">未检测试管比例</span><span class="font-size" style="color: red">{{ dataList.CheckedRatio }}%</span>
          </div>
        </el-card>
      </el-col>
    </el-row>
    <el-row>
      <el-col>
        <el-card class="box-card">
          <div slot="header" class="clearfix">
            <p class="font-center">历史每天注册用户统计数</p>
          </div>
          <div v-for="o in list" :key="o.time" class="text item">
            <el-card style="margin-bottom: 20px">
              <div style="padding: 14px;">
                <span class="font-m">{{ o.time }}</span><span class="font-size" style="color: #20a0ff">{{ o.usercount }}</span>
              </div>
            </el-card>
          </div>
        </el-card>
      </el-col>
    </el-row>
    <el-calendar v-model="value">
    </el-calendar>
  </div>
</template>

<script>
import { mapGetters } from 'vuex'
import { getList } from '@/api/index'

export default {
  name: 'Dashboard',
  data() {
    return {
      currentDate: new Date(),
      value: new Date(),
      dataList: null,
      list: null,
      listLoading: true,
      dialogFormVisible: false,
      addUserDialogFormVisible: false
    }
  },
  computed: {
    ...mapGetters([
      'name'
    ])
  },
  created() {
    this.fetchData()
  },
  methods: {
    fetchData() {
      this.listLoading = true
      getList().then(response => {
        console.log(response)
        this.dataList = response.data
        const a = Math.round(response.data.CheckedRatio * 100)
        this.dataList.CheckedRatio = a
        this.list = response.data.HistoryRegisterCount
        this.listLoading = false
      })
    }
  }
}
</script>

<style lang="scss" scoped>
.font-center{
  text-align: center;
}
.font-m{
  margin-right: 60%;
}
.font-size{
  font-size: 48px;
}
.time {
  font-size: 13px;
  color: #999;
}

.bottom {
  margin-top: 13px;
  line-height: 12px;
}

.image {
  width: 100%;
  display: block;
}

.clearfix:before,
.clearfix:after {
  display: table;
  content: "";
}

.clearfix:after {
  clear: both
}

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
