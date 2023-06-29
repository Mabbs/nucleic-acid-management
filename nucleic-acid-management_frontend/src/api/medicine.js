import request from '@/utils/request'

export function getList(serial = '', status = '', offset = 0) {
  return request({
    url: '/manage/kit/read' + '?serial=' + serial + '&status=' + status + '&offset=' + offset + '&limit=10',
    method: 'get'
  })
}

export function deleteMedicin(id) {
  return request({
    url: '/manage/kit/delete/id/' + id,
    method: 'get'
  })
}

export function updateMedicine(id, data) {
  return request({
    url: '/manage/kit/update/serial/' + id,
    method: 'post',
    data
  })
}

export function addMedicine(data) {
  return request({
    url: '/manage/kit/add',
    method: 'post',
    data
  })
}
