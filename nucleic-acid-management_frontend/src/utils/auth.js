
const TokenKey = 'vue_admin_template_token'

export function getToken() {
  return window.localStorage.getItem(TokenKey)
}

export function setToken(token) {
  return window.localStorage.setItem(TokenKey, token)
}

export function removeToken() {
  return window.localStorage.removeItem(TokenKey)
}

export function getUniqid() {
  return window.localStorage.getItem('uniqid')
}

export function setUniqid(token) {
  return window.localStorage.setItem('uniqid', token)
}

export function removeUniqid() {
  return window.localStorage.removeItem('uniqid')
}

export function getUserId() {
  return window.localStorage.getItem('user_id')
}

export function setUserId(token) {
  return window.localStorage.setItem('user_id', token)
}

export function removeUserId() {
  return window.localStorage.removeItem('user_id')
}

export function getUserName() {
  return window.localStorage.getItem('username')
}

export function setUserName(username) {
  return window.localStorage.setItem('username', username)
}

export function removeUserName() {
  return window.localStorage.removeItem('username')
}
export function getAvatar() {
  return window.localStorage.getItem('avatar')
}

export function setAvatar(username) {
  return window.localStorage.setItem('avatar', username)
}

export function removeAvatar() {
  return window.localStorage.removeItem('avatar')
}
