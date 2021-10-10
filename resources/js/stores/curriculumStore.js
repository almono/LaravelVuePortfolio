import axios from 'axios'
import { backendUrl } from '../helpers/backendUrl.js'
//import vuecookie from 'vue-cookies'

const state = {
  cvInfo: null
};

const getters = {
  getCVInfo: (state) => state.cvInfo
};

const actions = {
  async getCurriculumData( context ){
    return await axios.get(`${backendUrl()}/getCVData`, null,
      {
          //headers: { 'Authorization': "bearer " +  vuecookie.get('token') }
      }).then(response => {
        if(response.data && response.data.cv_data) {
          context.commit("setCVInfo", response.data.cv_data)
        }

        return response.data
    })
  },

  async downloadCV( context, data ){
    return await axios.post(`${backendUrl()}/downloadCV`, data,
      {
        headers: { 'Authorization': "Bearer " +  $cookies.get('userToken') },
        responseType: 'blob'
      }).then(response => {
        if(response && response.data) {
          return response.data
        }
    })
  }
};

const mutations = {
  setCVInfo: (state, info) => (state.cvInfo = info)
};

export default {
  namespaced: true,
  state,
  getters,
  actions,
  mutations
}


