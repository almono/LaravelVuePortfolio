<template>
  <div>
    <div class="col-12 navbar-top py-2 px-0">
      <vue-particles color="#588B8B"
                     :particleSize="3"
                     linesColor="#366060"
                     :clickEffect="false"
      ></vue-particles>
      <div class="col-12 px-0">

      </div>
    </div>
    <div class="col-12 navbar-bottom px-0">
      <b-navbar toggleable="lg" type="dark" variant="dark" class="p-0 navbar-div">
        <b-navbar-brand class="navbar-brand-item px-2 py-1" href="#">
          {{ $t('portfolioBrand', 'portfolioBrand') }}
        </b-navbar-brand>
        <b-navbar-toggle target="nav-collapse"></b-navbar-toggle>

        <b-collapse class="py-1" id="nav-collapse" is-nav>
          <b-navbar-nav>
            <b-nav-item :to="{ path: '/' }">{{ $t('homepageLink', 'homepageLink') }}</b-nav-item>
            <b-nav-item :to="{ path: '/cv' }">{{ $t('cvLink', 'cvLink') }}</b-nav-item>
            <b-nav-item :to="{ path: '/blogs' }">{{ $t('userBlogs', 'userBlogs') }}</b-nav-item>
            <b-nav-item :to="{ path: '/translations' }">{{ $t('translationsNav', 'translationsNav') }}</b-nav-item>
            <b-nav-item :to="{ path: '/cryptocurrencies' }">{{ $t('cryptoInfoNav', 'cryptoInfoNav') }}</b-nav-item>
          </b-navbar-nav>

          <!-- Right aligned nav items -->
          <b-navbar-nav class="ml-auto">
            <b-nav-item-dropdown id="langDropdown" class="px-2" right>
              <template v-slot:button-content>
                <span style="color: white;">{{ languageText }}</span>
              </template>
              <b-dropdown-item @click="changeLanguage('EN')">English</b-dropdown-item>
              <b-dropdown-item @click="changeLanguage('PL')">Polski</b-dropdown-item>
              <b-dropdown-item @click="changeLanguage('JP')">日本語</b-dropdown-item>
            </b-nav-item-dropdown>

            <b-nav-item-dropdown v-if="!getLoginStatus" right>
              <!-- Using 'button-content' slot -->
              <template #button-content>
                <span>{{ $t('navbarGuest', 'navbarGuest') }}</span>
              </template>
              <b-dropdown-item :to="{ path: '/login' }">{{ $t('loginLink', 'loginLink') }}</b-dropdown-item>
              <b-dropdown-item :to="{ path: '/register' }">{{ $t('registerLink', 'registerLink') }}</b-dropdown-item>
            </b-nav-item-dropdown>
            <b-nav-item-dropdown v-else right>
              <!-- Using 'button-content' slot -->
              <template #button-content>
                <span style="color: white;">{{ $t('navbarHello', 'navbarHello') }}
                  <span v-if="getUserData.username">{{ getUserData.username }}</span>
                  <span v-else>{{ getUserData.email }}</span>
                </span>
              </template>
              <b-dropdown-item :to="{ path: '/dashboard' }">{{ $t('dashboardLink', 'dashboardLink') }}</b-dropdown-item>
              <b-dropdown-item @click="logoutUser()" href="#">{{ $t('signOutLink', 'signOutLink') }}</b-dropdown-item>
            </b-nav-item-dropdown>
          </b-navbar-nav>
        </b-collapse>
      </b-navbar>
    </div>
  </div>
</template>


<script>
import { mapGetters } from 'vuex'

export default {
  name: 'Navbar',
  data() {
    return {
      currentLang: localStorage.getItem('currentLanguage') || 'EN'
    }
  },
  components: {

  },
  mounted() {
    this.setLanguageText()
  },
  computed: {
    ...mapGetters('authStore', ['getLoginStatus', 'getUserData']),

    languageText() {
      switch(this.currentLang) {
        case 'EN':
          return 'English'
          break
        case 'PL':
          return 'Polski'
          break
        case 'JP':
          return '日本語'
          break
        default:
          return 'English'
          break
      }
    }
  },
  methods: {
    changeLanguage(lang) {
      let translations = this.$store.getters['translationsStore/getTranslations']

      if(translations[lang]) {
        localStorage.setItem('currentLanguage', lang)
        this.currentLang = lang
        this.$i18n.setLocaleMessage(lang, translations[lang])
        this.$i18n.locale = lang
      }
    },
    setLanguageText() {
      if(localStorage.getItem('currentLanguage')) {
        this.currentLanguage = localStorage.getItem('currentLanguage')
      }
    },
    logoutUser() {
      this.$store.dispatch('authStore/logoutUser')
    }
  }
};
</script>

<style scoped>
  .navbar-dark .navbar-nav .nav-link {
    color: white !important;
  }
  .navbar-div {
    background-color: #588B8B !important;
  }
  .navbar-top {
    height: 100px;
  }
  .navbar-brand-item {
    min-width: 100px;
  }
  .navbar-item-block {
    border-left: 1px #6f7582 solid;
    border-right: 1px #6f7582 solid;
    min-width: 120px;
  }
  .navbar-item-block:last-of-type {
    border-left: none;
    border-right: 1px #6f7582 solid;
  }
  .navbar-item-block:first-of-type {
    border-left: 1px #6f7582 solid;
    border-right: none;
  }
</style>