import axios from 'axios'
import { backendUrl } from '../helpers/backendUrl.js'

const state = {
  userActivities: null,
  lastLogin: null
};

const getters = {
  getUserActivities: (state) => state.userActivities,
  getUserLastLogin: (state) => state.lastLogin
};

const actions = {
  getDashboard( context, data ) {
    return axios.post(`${backendUrl()}/getUserDashboard`, data).then(response => {
        if(response.data) {
          context.commit('setUserActivities', response.data.userActivities)
          context.commit('setUserLastLogin', response.data.lastLogin)
        }
        return response.data
    })
  },
  async getActivities( context, data ) {
    return await axios.post(`${backendUrl()}/getDashboardActivities`, data).then(response => {
      if(response.data) {
        context.commit('setUserActivities', response.data.activities)
      }
      return response.data
    })
  },
  async updateUserInfo( context, data ) {
    return await axios.post(`${backendUrl()}/updateUserInfo`, data).then(response => {
      return response.data
    })
  },
  async getUserProfile( context, userId ) {
    return await axios.get(`${backendUrl()}/getUserProfile/` + userId ).then(response => {
      return response.data
    })
  },

  async followUser(context, userId ) {
    return await axios.post(`${backendUrl()}/followUser`, {
      userId: userId
    }).then(response => {
      return response.data
    })
  },

  async unfollowUser(context, userId ) {
    return await axios.post(`${backendUrl()}/unfollowUser`, {
      userId: userId
    }).then(response => {
      return response.data
    })
  }
};

const mutations = {
  setUserActivities: (state, activities) => (state.userActivities = activities),
  setUserLastLogin: (state, last_login) => (state.lastLogin = last_login)
};

export default {
  namespaced: true,
  state,
  getters,
  actions,
  mutations
}


