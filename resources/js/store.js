import Vue from 'vue'
import Vuex from 'vuex'
import homepageStore from './stores/homepageStore';
import authStore from './stores/authStore';
import curriculumStore from './stores/curriculumStore';
import translationsStore from './stores/translationsStore';
import userStore from './stores/userStore';
import cryptoStore from './stores/cryptoStore';
import blogsStore from './stores/blogsStore';

Vue.use(Vuex);

let store = new Vuex.Store({
  modules: {
    homepageStore,
    authStore,
    curriculumStore,
    translationsStore,
    userStore,
    cryptoStore,
    blogsStore
  }
});

export default store
