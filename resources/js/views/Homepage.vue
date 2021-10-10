<template>
  <div class="container">
    <div class="row justify-content-center">
      <div class="col-md-9 col-sm-12 pb-3">
        <div class="card">
          <div class="card-body">
            <div class="col-12 px-0">
              <p class="main-message mb-2 mt-1">Welcome to my personal Laravel/Vue application! Here you will find some of the things I have been testing and implementing.</p>
            </div>
            <div class="col-12 px-0">
              <p class="mb-1 mt-3 font-weight-bold main-header-text">The project can be divided into those sections:</p>
              <div class="col-12 main-section-text">
                <li>Login/Registration Process</li>
                <li>User Profile with user logs and followers logic added ( comments section is WIP )</li>
                <li>Cryptocurrencies API ( top 100 coins list with details on each of them, cryptocurrencies news )</li>
                <li>User Blogs ( they have to be approved by an admin or owner to be available/visible )</li>
                <li>Multi-language support</li>
                <li>Translations module that can be accessed by owner and admins, used to add different language translations and edit them</li>
                <li>CV Page with some interactive parts and PDF Export</li>
              </div>
            </div>
            <div class="col-12 px-0">
              <p class="mb-1 mt-3 font-weight-bold main-header-text">Technologies used</p>
              <div class="col-12 main-section-text">
                <li>Laravel 8.1 ( MVC, JTW Auth, Coingecko API, Laravel Mix )</li>
                <li>Vue.js 2.6 ( with vuex and vue router, i18n, axios, bootstrap )</li>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="col-md-3 col-sm-12 pb-3">
        <div class="card" style="height: 100%;">
          <div class="card-body">
            <div class="col-12 px-0 mb-3 text-center font-weight-bold quicklink-header">
              Quick Access
            </div>
            <router-link :to="{ name: 'UserBlogs'}">
              <div class="col-12 quicklink px-0">User Blogs</div>
            </router-link>
            <router-link :to="{ name: 'Cryptocurrencies'}">
              <div class="col-12 quicklink px-0">Cryptocurrencies</div>
            </router-link>
            <router-link :to="{ name: 'Dashboard'}">
              <div class="col-12 quicklink px-0">User Dashboard</div>
            </router-link>
            <!--
            <router-link :to="{ name: 'APIShowcase'}">
              <div class="col-12 quicklink px-0">API Showcase</div>
            </router-link>
            -->
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'Homepage',
  mounted() {

  }
}
</script>

<style scoped>
  .quicklink {
    height: 50px;
    max-height: 50px;
    display: flex;
    align-items: center;
    justify-content: center;
    border: 1px solid #1A8C7CED;
    margin-bottom: 20px;
    color: #4C8080ED;
    font-weight: 600;
    background-color: #4C808012;
  }
  .quicklink:hover {
    color: #4C8080;
    background-color: rgba(41, 82, 82, 0.2);
    -webkit-transition: background-color 500ms linear;
    -ms-transition: background-color 500ms linear;
    transition: background-color 500ms linear;
  }
  .quicklink-header {
    font-size: 22px;
    color: #4C8080ED;
  }
  .main-message {
    font-size: 22px;
    font-weight: 600;
    color: #4C8080ED;
  }
  .main-header-text {
    color: #588B8BC4;
  }
  .main-section-text {
    color: #588B8BDE;
  }
</style>
