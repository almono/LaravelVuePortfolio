import axios from 'axios'
import { backendUrl } from '../helpers/backendUrl.js'

const state = {};

const getters = {};

const actions = {
    async getCryptoNews( context ){
        return await axios.post(`${backendUrl()}/getCryptoNews`, null).then(response => {
                return response.data
        })
    },

    async getCryptoPrice( context ){
      return await axios.post(`${backendUrl()}/getCryptoPrice`, null).then(response => {
        return response.data
      })
    }
};

const mutations = {};

export default {
    namespaced: true,
    state,
    getters,
    actions,
    mutations
}


