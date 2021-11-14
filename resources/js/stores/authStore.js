import axios from 'axios'
import { backendUrl } from '../helpers/backendUrl.js'

const state = {
  loginStatus: false,
  userData: null
};

const getters = {
  getLoginStatus: (state) => state.loginStatus,
  getUserData: (state) => state.userData
};

const actions = {
  async loginUser( context, data ) {
    return await axios.post(`${backendUrl()}/loginUser`, data).then(response => {
      if (response.data) {
        if (response.data.status === 'success') {
          context.commit('setUserData', response.data.userData)
          context.commit('setLoggedInStatus', true)
        }
      }

      return response.data
    })
  },

  async logoutUser( context, data ) {
    return await axios.post(`${backendUrl()}/logout`, data).then(response => {
      if (response.data) {
        if (response.data.status === 'success') {
          context.commit('clearUserData')
        }
      }

      return response.data
    })
  },

  async authenticateUser( context, data ) {
    return await axios.post(`${backendUrl()}/authenticateUser`, data,
      {
        headers: { 'Authorization': "Bearer " +  $cookies.get('userToken') }
      }).then(response => {
        if (response.data) {
          if (response.data.status === 'success') {
            context.commit('setUserData', response.data.userData)
            context.commit('setLoggedInStatus', true)
          }
        }

        return response.data
    })
  },

  async validateToken (context, data) {
    return await axios.post(`${backendUrl()}/validateToken`, data).then(response => {

      return response.data

    })
  },

  async forgotPassword( context, data ) {
    return await axios.post(`${backendUrl()}/forgotPassword`, data).then(response => {
      return response.data
    })
  },

  async resetPassword( context, data ) {
    return await axios.get(`${backendUrl()}/resetPassword/` + data.userId + '/' + data.userToken).then(response => {
      return response.data
    })
  },

  async setNewPassword( context, data ) {
    return await axios.post(`${backendUrl()}/setNewPassword`, data).then(response => {
      return response.data
    })
  },

  async registerNewUser( context, data ) {
    return await axios.post(`${backendUrl()}/registerNewUser`, data).then(response => {
      return response.data
    })
  }
};

const mutations = {
  setLoggedInStatus: (state, status) => (state.loginStatus = status),

  setUserData (state, userData) {
    state.userData = userData.user
    $cookies.set('userData', JSON.stringify(userData.user))
    $cookies.set('userToken', userData.token)
    axios.defaults.headers.common.Authorization = `Bearer ${userData.token}`

    const app_status = process.env.MIX_FRONTEND_ENV !== undefined ? process.env.MIX_FRONTEND_ENV : 'production';

    if( app_status === 'localhost')
    {
      axios.defaults.baseURL = 'http://localhost:8000/api';
    } else if( app_status === 'production' ) {
      axios.defaults.baseURL = 'http://website.almono.info/api';
    }

  },

  setUserToken(state, newToken) {
    axios.defaults.headers.common.Authorization = `Bearer ${newToken}`
  },

  setUserInfo(state, user) {
    state.userData = user
  },

  clearAuth() {
    state.loginStatus = false
    $cookies.remove('userData')
    $cookies.remove('userToken')
  },

  clearUserData () {
    state.loginStatus = false
    $cookies.remove('userData')
    $cookies.remove('userToken')
    this.$router.push({ path: '/'})
  }
};

export default {
  namespaced: true,
  state,
  getters,
  actions,
  mutations
}


