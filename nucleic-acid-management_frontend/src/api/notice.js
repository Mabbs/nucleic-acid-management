import request from '@/utils/request'

export function getList() {
  return request({
    url: '/manage/notice/read',
    method: 'get'
  })
}

export function deleteNotice(id) {
  return request({
    url: '/manage/notice/delete/id/' + id,
    method: 'get'
  })
}

export function uploadFile(data) {
  return request({
    url: '/manage/notice/upload',
    method: 'post',
    data
  })
}

export function updateNotice(id, data) {
  return request({
    url: '/manage/notice/update/id/' + id,
    method: 'post',
    data
  })
}

export function addNotice(data) {
  return request({
    url: '/manage/notice/create',
    method: 'post',
    data
  })
}
