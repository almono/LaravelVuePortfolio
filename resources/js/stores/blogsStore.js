import axios from 'axios';
import { backendUrl } from '../helpers/backendUrl.js'

const state = {};

const getters = {};

const actions = {
    async getUserBlogs( context, data ) {
        return await axios.post(`${backendUrl()}/getUserBlogs`, data).then(response => {
            return response.data
        })
    },
    async createNewBlog( context, data ) {
        return await axios.post(`${backendUrl()}/createNewBlog`, data, {
          headers: {'Content-Type': 'multipart/form-data'}
        }).then(response => {
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


