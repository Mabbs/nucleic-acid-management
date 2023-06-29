import { login } from '@/api/user'
import {
  getToken,
  setToken,
  removeToken,
  getUserName,
  getAvatar,
  setAvatar,
  setUserName,
  setUserId,
  setUniqid
} from '@/utils/auth'
import { resetRouter } from '@/router'

const getDefaultState = () => {
  return {
    token: getToken(),
    name: getUserName(),
    avatar: getAvatar()
  }
}

const state = getDefaultState()

const mutations = {
  RESET_STATE: (state) => {
    Object.assign(state, getDefaultState())
  },
  SET_TOKEN: (state, token) => {
    state.token = token
  },
  SET_NAME: (state, name) => {
    state.name = name
  },
  SET_AVATAR: (state, avatar) => {
    state.avatar = avatar
  },
  SET_USERID: (state, id) => {
    state.id = id
  }
}

const actions = {
  // user login
  login({ commit }, userInfo) {
    const { uniqid, password } = userInfo
    console.log(userInfo)
    return new Promise((resolve, reject) => {
      login({ uniqid: uniqid.trim(), password: password }).then(response => {
        const { data } = response
        commit('SET_TOKEN', data.gid)
        commit('SET_NAME', data.name)
        commit('SET_USERID', data.id)
        setUserName(data.name)
        setAvatar('https://img2.baidu.com/it/u=1035356506,3713698341&fm=253&app=138&size=w931&n=0&f=JPEG&fmt=auto?sec=1677862800&t=f9b9e4988c7a0aaf0cf41f0bfb3085a1')
        commit('SET_AVATAR', 'https://img2.baidu.com/it/u=1035356506,3713698341&fm=253&app=138&size=w931&n=0&f=JPEG&fmt=auto?sec=1677862800&t=f9b9e4988c7a0aaf0cf41f0bfb3085a1')
        setToken(data.gid)
        setUserId(data.id)
        setUniqid(data.uniqid)
        resolve()
      }).catch(error => {
        reject(error)
      })
    })
  },

  // // get user info
  // getInfo({ commit, state }) {
  //   return new Promise((resolve, reject) => {
  //     getInfo(state.token).then(response => {
  //       const { data } = response
  //
  //       if (!data) {
  //         return reject('Verification failed, please Login again.')
  //       }
  //
  //       const { name, avatar } = data
  //
  //       commit('SET_NAME', name)
  //       commit('SET_AVATAR', avatar)
  //       resolve(data)
  //     }).catch(error => {
  //       reject(error)
  //     })
  //   })
  // },

  // user logout
  logout({ commit }) {
    removeToken() // must remove  token  first
    resetRouter()
    commit('RESET_STATE')
  },

  // remove token
  resetToken({ commit }) {
    return new Promise(resolve => {
      removeToken() // must remove  token  first
      commit('RESET_STATE')
      resolve()
    })
  }
}

export default {
  namespaced: true,
  state,
  mutations,
  actions
}

