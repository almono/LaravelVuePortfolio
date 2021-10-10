import axios from 'axios'
//import { backendUrl } from '@/helpers/backendUrl.js'
import vuecookie from 'vue-cookies'

const state = {};

const getters = {};

const actions = {
    /*async getDocumentsStats( context ){
        return await axios.post(`${backendUrl()}/getDocumentsStats`, null,
            {
                headers: { 'Authorization': "bearer " +  vuecookie.get('token') }
            }).then(response => {
            return response.data
        })
    }*/
};

const mutations = {};

export default {
    namespaced: true,
    state,
    getters,
    actions,
    mutations
}


