import request from '@/utils/request'

export function getList(name = '', offset = 0) {
  return request({
    url: '/manage/user/read/' + '?name=' + name + '&offset=' + offset + '&limit=10',
    method: 'get'
  })
}

export function deleteUser(id) {
  return request({
    url: '/manage/user/delete/id/' + id,
    method: 'get'
  })
}

export function updateUser(id, data) {
  return request({
    url: '/manage/user/update/id/' + id,
    method: 'post',
    data
  })
}

export function addUser(data) {
  return request({
    url: '/manage/user/create/',
    method: 'post',
    data
  })
}
