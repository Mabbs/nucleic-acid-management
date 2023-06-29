import request from '@/utils/request'

export function getList(name = '', offset = 0) {
  return request({
    url: '/manage/user/readPending/' + '?name=' + name + '&offset=' + offset + '&limit=10',
    method: 'get'
  })
}

18

export function updateStatus(id) {
  return request({
    url: '/manage/user/passPending/id/' + id,
    method: 'get'
  })
}
