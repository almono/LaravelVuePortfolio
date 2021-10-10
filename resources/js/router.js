import Vue from 'vue'
import Router from 'vue-router'
import store from './store'

import Homepage from './views/Homepage.vue'
import CV from './views/CV.vue'
import Login from './views/Login.vue'
import ForgotPassword from './views/ForgotPassword.vue'
import ResetPassword from './views/ResetPassword.vue'
import Dashboard from './views/Dashboard'
import UserProfile from './views/UserProfile'
import UserBlogs from './views/UserBlogs'
import Translations from './views/Translations'
import Cryptocurrencies from './views/Cryptocurrencies'

import {i18n} from "./plugins/i18n";
import axios from "axios/index";

Vue.use(Router);
// base process.env.BASE_URL

let router = new Router({
    mode: 'history',
    base: '/portfolio/public',
    linkActiveClass: 'is-active',
    routes: [
        {
          path: '/',
          name: 'Homepage',
          component: Homepage,
          meta: {
            requireAuth: false
          }
        },
        {
          path: '/login',
          name: 'Login',
          component: Login,
          meta: {
            requireAuth: false,
            redirectLogged: true
          }
        },
        {
          path: '/forgot_password',
          name: 'ForgotPassword',
          component: ForgotPassword,
          meta: {
            requireAuth: false,
            redirectLogged: true
          }
        },
        {
          path: '/reset_password',
          name: 'ResetPassword',
          component: ResetPassword,
          meta: {
            requireAuth: false,
            redirectLogged: true
          }
        },
        {
            path: '/cv',
            name: 'CV',
            component: CV,
            meta: {
                requireAuth: false
            }
        },
        {
          path: '/dashboard',
          name: 'Dashboard',
          component: Dashboard,
          meta: {
            requireAuth: true
          }
        },
        {
          path: '/profile/:userId',
          name: 'UserProfile',
          component: UserProfile,
          meta: {
            requireAuth: true
          }
        },
        {
          path: '/blogs',
          name: 'UserBlogs',
          component: UserBlogs,
          props: true,
          meta: {
            requireAuth: false
          }
        },
        {
          path: '/translations',
          name: 'Translations',
          component: Translations,
          props: true,
          meta: {
            requireAuth: true
          }
        },
        {
          path: '/cryptocurrencies',
          name: 'Cryptocurrencies',
          component: Cryptocurrencies,
          props: true,
          meta: {
            requireAuth: false
          }
        },
        {
          path: '/api_showcase',
          name: 'APIShowcase',
          component: Cryptocurrencies,
          props: true,
          meta: {
            requireAuth: false
          }
        }

        /*,
        {
          path: '*',
          redirect: '/404'
        }*/
    ]
});

router.beforeEach((to, from, next) => {

  let userToken = $cookies.get('userToken')
  let loggedIn = $cookies.get('userData')
  let isLoggedIn = store.getters['authStore/getLoginStatus']

  if(userToken && userToken !== 'undefined' && loggedIn && loggedIn !== 'undefined' && !isLoggedIn) {
    // set default token
    //console.log('if')
    axios.defaults.headers.common.Authorization = `Bearer ${userToken}`
    store.dispatch('authStore/authenticateUser', loggedIn).then(response => {
      if (response) {
        if (response.status === 'success') {
          //console.log('success')
          $cookies.set('userData', JSON.stringify(response.userData.user))
          $cookies.set('userToken', response.userData.token)
          axios.defaults.headers.common.Authorization = `Bearer ${response.userData.token}`
          store.commit('authStore/setLoggedInStatus', true)

          next()
        } else if (response.status === 'error') {
          store.commit('clearUserData')

          Vue.$toast.open({
            message: 'Logged out due to inactivity',
            type: 'error'
          });
          //console.log('error')
          next('/login')
        }
      }
    })
  } else {
    //console.log('else')
    let isLoggedIn = store.getters['authStore/getLoginStatus']
    //console.log('from: ' + from.name)
    //console.log('to: ' + to.name)
    if(to.matched.some(record => record.meta.requireAuth) && loggedIn && loggedIn != 'undefined' && userToken && userToken != 'undefined' && isLoggedIn && from.name != 'Login' && to.name != 'Login') {
      store.dispatch('authStore/validateToken').then(response => {
        if(!response.tokenValid) {

          store.commit('authStore/clearAuth')
          Vue.$toast.open({
            message: i18n.t('loggedOutError', 'loggedOutError'),
            type: 'error'
          });

          next('/login')
        } else {
          $cookies.set('userToken', response.token)
          axios.defaults.headers.common.Authorization = `Bearer ${response.token}`
          //store.commit('authStore/setUserToken', response.token)
          next()
        }
      })
    } else {
      // get updated data after verification
      loggedIn = $cookies.get('userData')
      userToken = $cookies.get('userToken')
      isLoggedIn = store.getters['authStore/getLoginStatus']

      if (to.matched.some(record => record.meta.requireAuth) && (!loggedIn || loggedIn == 'undefined')) {
        next('/login')
        return
      } else if(to.matched.some(record => record.meta.redirectLogged) && loggedIn && loggedIn != 'undefined' && userToken && userToken != 'undefined' && !isLoggedIn) {
        next('/')
      }

      next()
    }
  }
});

export default router
