<template>
  <div>
    <loading
        :active.sync="languagesLoading"
        :can-cancel="false"
        :is-full-page="true"
        :opacity=1
        :lock-scroll="true"
        :enforce-focus="true"
        color="#588B8B"
        :z-index=9999
        :width=200
        :height=200
        transition="slide-fade"
    >
    </loading>

    <Navbar/>
    <transition name="fade" mode="out-in">
      <router-view class="px-2 py-4 min"></router-view>
    </transition>
  </div>
</template>

<script>
import Navbar from '../components/Navbar';
import Footer from '../components/Footer';
import Loading from 'vue-loading-overlay';

export default {
  name: 'App',
  data() {
    return {
      languagesLoading: true
    }
  },
  components: {
    Navbar,
    Footer,
    Loading
  },
  mounted() {
    this.$store.dispatch('translationsStore/getTranslations').then(response => {
      this.languagesLoading = false
    }, this)
  }
};
</script>

<style>
  body {
    background-color: rgba(88, 139, 139, 0.05) !important;
  }
  .min {
    min-height: 75vh;
  }
  .fade-enter {
    opacity: 0;
  }

  .fade-enter-active {
    transition: opacity 0.3s ease;
  }

  .fade-leave {

  }

  .fade-leave-active {
    transition: opacity 0.3s ease;
    opacity: 0;
  }

  .v-toast__item--success {
    background-color: #80C197 !important;
  }
</style>