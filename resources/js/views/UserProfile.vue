<template>
  <div class="container">
    <div class="row justify-content-center">
      <div class="col-12">
        <div class="card">
          <div class="card-header dashboard-header">
            <h3 class="my-0">User Profile</h3>
          </div>
          <div class="card-body px-0 py-0">
            <template v-if="!isLoading">
              <div class="row mx-0">
                <div class="col-12 px-0">
                  <div class="row mx-0 profile-divs align-self-stretch" v-if="userProfile">
                    <div class="col-3 profile-left text-center p-2 mb-3">
                      <b-avatar class="mt-2" src="https://placekitten.com/300/300" size="9rem"></b-avatar>
                      <hr>
                      <template>
                        <div class="col-12 profile-username" v-if="userProfile.username">{{ userProfile.username }}</div>
                        <div class="col-12 profile-role" v-if="userProfile.role_name">Role: {{ userProfile.role_name }}</div>
                        <div v-if="userId != getUserData.id" class="col-12 text-center">
                          <span class="btn btn-primary" v-if="!isFollowed" @click="followUser()">{{ $t('btnFollowText', 'btnFollowText') }}</span>
                          <span class="btn btn-warning btn-unfollow" v-else @click="unfollowUser()">{{ $t('btnUnfollowText', 'btnUnfollowText') }}</span>
                        </div>
                      </template>
                    </div>
                    <div class="col-9 profile-right px-0">
                      <b-tabs class="profile-tabs-div" content-class="mt-3">
                        <b-tab :title-link-class="'profile-tab'" :title-item-class="'profile-tab2'" title="User Information" active>
                          <template v-if="userProfile">
                            <div class="col-12" v-if="userProfile.is_private === 0">
                              <b-input-group prepend="Username" class="mr-sm-2 mb-2 profile-label">
                                <b-form-input id="inline-form-input-username" disabled v-model="userProfile.username"></b-form-input>
                              </b-input-group>
                              <b-input-group :prepend="$t('userEmailLabel', 'userEmailLabel')" class="mr-sm-2 mb-2 profile-label">
                                <b-form-input id="inline-form-input-email" disabled :type="email" v-model="userProfile.email"></b-form-input>
                              </b-input-group>
                            </div>
                            <div class="col-12 text-center" v-else-if="userProfile.is_private === 1">
                              <h3 class="priv-profile">Profile has been set as private!</h3>
                            </div>
                          </template>
                        </b-tab>
                      </b-tabs>
                    </div>
                  </div>
                  <div class="col-12 text-center py-2" v-else>
                    <h3>Profile not found</h3>
                  </div>
                </div>
              </div>
            </template>
            <template v-else>
              <div class="col-12 px-0 loading-div">
                <loading
                    :active.sync="isLoading"
                    :can-cancel="false"
                    :is-full-page="false"
                    opacity=1
                    :lock-scroll="true"
                    :enforce-focus="true"
                    color="#588B8B"
                    z-index=9999
                    width=200
                    height=200
                    transition="slide-fade"
                >
                </loading>
              </div>
            </template>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { mapGetters } from 'vuex'
import Loading from 'vue-loading-overlay';

export default {
  name: 'UserProfile',
  data() {
    return {
      isLoading: true,
      userProfile: null,
      userId: this.$route.params.userId ? this.$route.params.userId : 0,
      isFollowed: false
    }
  },
  created() {

  },
  mounted() {
    this.getUserProfile()
  },
  methods: {
    getUserProfile() {
      this.$store.dispatch('userStore/getUserProfile', this.userId).then(response => {
        this.isLoading = false
        if(response.status === 'success') {
          this.userProfile = response.user
          this.isFollowed = response.isFollowed
        }
      })
    },
    followUser() {
      this.$store.dispatch('userStore/followUser', this.userId).then(response => {
        if(response.status === 'success') {
          this.isFollowed = true
        }
      })
    },
    unfollowUser() {
      this.$store.dispatch('userStore/unfollowUser', this.userId).then(response => {
        if(response.status === 'success') {
          this.isFollowed = false
        }
      })
    }
  },
  computed: {
    ...mapGetters('authStore', ['getLoginStatus', 'getUserData']),
  },
  components: {
    Loading
  }
}
</script>

<style>
  .dashboard-header {
    border: 0px !important;
    color: white;
    background-color: #588B8B !important;
  }
  .dashboard-tab {
    border-radius: 0px !important;
    height: 100% !important;
    display: flex !important;
    align-items: center !important;
    color: white !important;
  }
  .dashboard-tab.active {
    background-color: #117a8b !important;
  }
  .dashboard-tab2 {
    height: 60px;
  }
  .dashboard-pills {
    width: 100%;
    min-height: 400px;
    background-color: rgba(88, 139, 139, 0.2) !important;
  }
  .dashboard-pills > div > ul {
    padding: 20px 0px 0px 0px !important;
    background-color: #588B8B !important;
  }
  .last-login-div {
    color: rgba(255, 255, 255, 0.6)
  }
  .dashboard-flex {
    display: flex;
    align-items: center;
  }
  .btn-dashboard {
    background-color: #588B8B !important;
    border-radius: 0px;
    border: 1px solid #588B8B !important;
  }
  .submit-btn {
    border-radius: 0px !important;
    background-color: #588B8B !important;
    border: none !important;
    width: 150px !important;
  }
  .submit-btn:hover, .submit-btn:focus {
    background-color: #366060 !important;
  }
  .submit-btn:active {
    background-color: #274f4f !important;
  }
  .profile-tab {
    border-radius: 0px !important;
    height: 100% !important;
    display: flex !important;
    align-items: center !important;
    color: white !important;
    background-color: #74a8a8 !important;
    text-decoration: none !important;
    border: none !important;
  }
  .profile-tab.active {
    background-color: #117a8b !important;
    border: none !important;
  }
  .profile-tabs-div > div > ul {
    background-color: #588B8B !important;
    border: 0px !important;
  }
  .profile-tab:hover {
    border: none !important;
  }
  .profile-tab2 {
    height: 60px;
    margin-bottom: 0px !important;
  }
  .profile-right {
    background-color: rgba(88, 139, 139, 0.2) !important;
    border-left: 1px solid #519191;
  }
  .profile-left {

  }
  .profile-divs {
    background-color: rgba(88, 139, 139, 0.2) !important;
    border: 1px solid #519191;
  }
  .profile-username {
    font-weight: 600;
  }
  .profile-role {
    font-weight: 600;
  }
  .profile-private-text {
    font-weight: 700;
    color: #117a8b;
  }
  .profile-label > div > div {
    width: 150px;
    background-color: #6a9197 !important;
    color: white;
  }
  .profile-label > input {
    background-color: #cce1e1 !important
  }
  .priv-profile {
    color: #1d7332;
    font-weight: 600;
  }
  .loading-div {
    height: 300px;
  }
  .btn-unfollow {
    background-color: #c85167 !important;
    border-color: #c85167 !important;
    color: white !important;
  }
</style>