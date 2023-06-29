import request from '@/utils/request'

export function getList() {
  return request({
    url: '/manage/status/read',
    method: 'get'
  })
}
