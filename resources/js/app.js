import App from './views/App.vue';
import router from './router';
import store from './store';
import { BootstrapVue, BootstrapVueIcons } from 'bootstrap-vue'
import VueI18n from 'vue-i18n'
import {i18n} from './plugins/i18n'
import VueCookies from 'vue-cookies'
import VueParticles from 'vue-particles'
import VueToast from 'vue-toast-notification';
import vuescroll from 'vuescroll';
import VCalendar from 'v-calendar';
import linkify from 'vue-linkify';

require('./bootstrap');
window.Vue = require('vue');

Vue.use(BootstrapVue);
Vue.use(BootstrapVueIcons);
Vue.use(VueI18n);
Vue.use(VueCookies);
Vue.use(VueParticles);
Vue.use(VueToast, {
  position: 'bottom',
  pauseOnHover: true,
  duration: 3000
});
Vue.use(vuescroll);
Vue.prototype.$vuescrollConfig = {
  bar: {
    background: 'red'
  }
};
Vue.use(VCalendar);

Vue.directive('linkified', linkify);

import 'bootstrap/dist/css/bootstrap.css';
import 'bootstrap-vue/dist/bootstrap-vue.css';
import 'vue-toast-notification/dist/theme-sugar.css';
import axios from "axios/index";

//Vue.config.productionTip = false;
//Vue.config.devtools = false;

let self = this

axios.interceptors.response.use((response) => {
  if(response.status && response.status === 200 && response.data && response.data.message) {
    Vue.$toast.open({
      message: i18n.t(response.data.message, response.data.message),
      type: response.data.status
    });
  }

  return response;

  }, (error) => {

  if(error.response) {
    if (error.response.status && error.response.status === 400) {
      store.commit('authStore/clearAuth')
      Vue.$toast.open({
        message: 'You must be logged in to perform this action!',
        type: 'error'
      });
      router.push({ name: 'Homepage' })
    }
  } else {
    console.log(error)
  }

  return Promise.reject(error.response)
});

Vue.config.errorHandler = err => {
  console.log('Exception: ', err.message)
  console.log('Inside: ', err.vm)
};

// "h" is just a standard taken from JSX
new Vue({
  created: function () {
    /*let userToken = $cookies.get('userToken')
    let userData = $cookies.get('userData')
    if(userToken && userToken !== 'undefined' && userData && userData !== 'undefined') {
      // set default token
      axios.defaults.headers.common.Authorization = `Bearer ${userToken}`
      this.$store.dispatch('authStore/authenticateUser', userData).then(response => {
        if (response.data) {
          if (response.data.status === 'success') {
            this.$store.commit('setUserData', response.data.userData)
            this.$store.commit('setLoggedInStatus', true)
          } else if (response.data.status === 'error') {
            this.$store.commit('clearUserData')

            Vue.$toast.open({
              message: 'Logged out due to inactivity',
              type: 'error'
            });
          }
        }
      })
    }*/
  },
  router,
  store,
  i18n,
  render: h => h(App)
}).$mount("#app");