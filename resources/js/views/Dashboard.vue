<template>
  <div class="container">
    <div class="row justify-content-center">
      <div class="col-12">
        <div class="card">
          <div class="card-header dashboard-header">
            <h3 class="my-0">Dashboard</h3>
          </div>
          <div class="card-body px-0 py-0">
            <!-- !isLoading || activities -->
            <template>
              <div class="row mx-0">
                <div class="col-12 px-0">
                  <b-tabs class="dashboard-pills" fill pills card vertical>
                    <b-tab :title-link-class="'dashboard-tab'" :title-item-class="'dashboard-tab2'" active>
                      <div class="row mx-0 activity-filters-toggle px-1 mb-2">
                        <div class="col-6 dashboard-flex text-left px-1">Toggle Activity Filters</div>
                        <div class="col-6 text-right py-1 px-0">
                          <span class="btn btn-info btn-dashboard" v-b-toggle.collapse-1 variant="primary">+</span>
                        </div>
                      </div>
                      <b-collapse id="collapse-1" class="col-12 row mx-0 px-1 mt-2 mb-2 activity-filters ">
                        <!-- BASIC FILTERS -->
                        <b-form-group class="col-4 px-1" id="input-group-type" label="Log Type" label-for="input-type">
                          <b-form-select
                              id="input-type"
                              v-model="activityFilters.type"
                              :options="logTypes"
                              class="activity-input"
                          ></b-form-select>
                        </b-form-group>
                        <b-form-group class="col-4 px-1" id="input-group-category" label="Log Category" label-for="input-category" v-if="logCategories">
                          <b-form-select
                              id="input-category"
                              v-model="activityFilters.category"
                              :options="logCategories"
                              class="activity-input"
                          ></b-form-select>
                        </b-form-group>
                        <!-- CALENDAR DATE PICKER FILTERS -->
                        <v-date-picker class="col-4" v-model="range" is-range>
                          <template v-slot="{ inputValue, inputEvents }">
                            <span label-for="activity-date-picker">Log Date Range</span>
                            <div id="activity-date-picker" class="row mx-0 mt-2 flex justify-center items-center">
                              <input
                                  :value="inputValue.start"
                                  v-on="inputEvents.start"
                                  class="col-5 form-control activity-input px-2 py-1"
                              />
                              <span class="col-2 flex date-arrow"><b-icon icon="arrow-right"></b-icon></span>
                              <input
                                  :value="inputValue.end"
                                  v-on="inputEvents.end"
                                  class="col-5 form-control activity-input px-2 py-1"
                              />
                            </div>
                          </template>
                        </v-date-picker>
                        <div class="col-12 text-right mb-2">
                          <span class="btn btn-primary submit-btn" @click="searchActivities()">Search</span>
                        </div>
                      </b-collapse>
                      <div class="col-12 px-0 activities-pagination text-right mt-3" v-if="currentPage && perPage && activityCount && activityPages">
                        <b-pagination v-model="currentPage" :total-rows="activityCount" :per-page="perPage" align="right" class="activities-pagination" @change="requestActivities($event)"></b-pagination>
                      </div>
                      <template class="col-12 text-center" #title>
                        <b-icon icon="calendar-check"></b-icon>
                        <span class="ml-2">Activities</span>
                      </template>
                      <vue-scroll :ops="ops">
                        <div v-if="!isLoading || activities" class="col-12 px-0 activities-panel">
                          <div class="col-12 px-0 mb-2 activity-div" v-for="activity in activities" :key="activity.id">
                            <div class="row mx-0 py-1 activity-header" v-bind:class="{'activity-success' : (activity.log_type_name === 'success'), 'activity-error' : (activity.log_type_name === 'error'), 'activity-warning' : (activity.log_type_name === 'warning'), 'activity-info' : (activity.log_type_name === 'info')}">
                              <div class="col-sm-12 activity-name px-1 col-md-8">
                                {{ $t('activity_' + activity.log_category_name, 'activity_' + activity.log_category_name) }} {{ activity.id }}
                              </div>
                              <div class="col-sm-12 px-1 pr-3 activity-date text-right col-md-4">
                                {{ activity.created_at | toDate }}
                              </div>
                            </div>
                            <div v-if="activity.log_info" class="col-12 px-1 py-1 activity-description">
                              {{ activity.log_info }}
                            </div>
                          </div>
                        </div>
                        <div v-else>
                          <loading
                              :active.sync="isLoading"
                              :can-cancel="false"
                              :is-full-page="false"
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
                        </div>
                      </vue-scroll>
                      <div class="col-12 px-0 activities-pagination text-right mt-3" v-if="currentPage && perPage && activityCount && activityPagesData">
                        <b-pagination v-model="currentPage" :total-rows="activityCount" :per-page="perPage" align="right" class="activities-pagination" @change="requestActivities($event)"></b-pagination>
                      </div>
                    </b-tab>
                    <b-tab :title-link-class="'dashboard-tab'" :title-item-class="'dashboard-tab2'">
                      <template class="col-12 text-center" #title>
                        <b-icon icon="person-circle"></b-icon>
                        <span class="ml-2">Profile</span>
                      </template>
                      <div class="row mx-0 profile-divs align-self-stretch">
                        <div class="col-3 profile-left text-center p-2 mb-3">
                          <b-avatar class="mt-2" src="https://placekitten.com/300/300" size="9rem"></b-avatar>
                          <hr>
                          <template v-if="userData && userProfile">
                            <div class="col-12 profile-username" v-if="userData.username">{{ userData.username }}<span v-if="userData.id"> ( {{userData.id}} )</span></div>
                            <div class="col-12 profile-role" v-if="userData.role_name">Role: {{ userData.role_name }}</div>
                            <div class="col-12 profile-role" v-if="userProfile.is_private">
                              <span class="profile-private-text">{{ $t('profilePrivateLabel', 'profilePrivateLabel') }}</span>
                            </div>
                          </template>
                          <div class="col-12 mt-3">
                            <b-button class="btn btn-danger" v-b-modal.modal-center>Disable Account</b-button>
                            <b-modal id="modal-center" body-class="disable-modal-body" footer-class="disable-modal-footer" header-class="disable-modal-header" centered :title="$t('accountDisableConfirmationHeader', 'accountDisableConfirmationHeader')">
                              <p class="my-1">{{ $t('disabledAccountConfirmationText', 'disabledAccountConfirmationText') }}</p>
                              <template #modal-footer="{ ok, cancel }">
                                <b-button variant="danger" @click="ok(); disableAccount()">
                                  Disable Account
                                </b-button>
                                <b-button class="profileUpdate-btn" @click="cancel()">
                                  Cancel
                                </b-button>
                              </template>
                            </b-modal>
                          </div>
                        </div>
                        <div class="col-9 profile-right px-0">
                          <b-tabs class="profile-tabs-div" content-class="mt-3">
                            <b-tab :title-link-class="'profile-tab'" :title-item-class="'profile-tab2'" title="User Information" active>
                              <div class="col-12" v-if="userProfile">
                                <b-input-group prepend="Username" class="mr-sm-2 mb-2 profile-label">
                                  <b-form-input class="username-span" id="inline-form-input-username" style="background-color: #accece !important;" disabled v-model="userProfile.username"></b-form-input>
                                </b-input-group>
                                <b-input-group :prepend="$t('userEmailLabel', 'userEmailLabel')" class="mr-sm-2 mb-2 profile-label">
                                  <b-form-input id="inline-form-input-email" :type="'email'" v-model="userProfile.email"></b-form-input>
                                </b-input-group>
                                <b-input-group :prepend="$t('oldPasswordLabel', 'oldPasswordLabel')" class="mr-sm-2 mb-2 profile-label">
                                  <b-form-input id="inline-form-input-old-password" :placeholder="$t('oldPasswordLabel', 'oldPasswordLabel')" v-model="userProfile.old_password" :type="'password'"></b-form-input>
                                </b-input-group>
                                <b-input-group :prepend="$t('newPasswordLabel', 'newPasswordLabel')" class="mr-sm-2 mb-2 profile-label">
                                  <b-form-input id="inline-form-input-new-password" :placeholder="$t('newPasswordLabel', 'newPasswordLabel')" v-model="userProfile.new_password" :type="'password'"></b-form-input>
                                </b-input-group>
                                <b-form-checkbox
                                    id="checkbox-1"
                                    name="checkbox-1"
                                    class="private-checkbox"
                                    value="1"
                                    unchecked-value="0"
                                    v-model="userProfile.is_private"
                                >
                                  Private Profile
                                </b-form-checkbox>
                              </div>
                              <div class="col-12 text-right mt-2 mb-3">
                                <span class="btn btn-primary profileUpdate-btn" @click="updateUserInfo()">
                                  Update Profile
                                </span>
                              </div>
                            </b-tab>
                            <b-tab :title-link-class="'profile-tab'" :title-item-class="'profile-tab2'">
                              <template class="col-12 text-center" #title>
                                <span>Following ({{ following.length }})</span>
                              </template>
                              <template class="col-12" v-if="following.length == 0">
                                <div class="col-12 text-center">
                                  {{ $t('noFollowingText', 'noFollowingText') }}
                                </div>
                              </template>
                              <template v-else>
                                <div class="row mx-0 mt-3">
                                  <div v-for="(follower, index) in following" class="col-3 follow-div mb-3">
                                    <!--<template v-if="index > 0">
                                      <div class="col-12 px-0">
                                        <hr class="py-0 my-2">
                                      </div>
                                    </template>-->
                                    <div :title="$t('followedOnText', 'followedOnText') + ' ' + follower.follow_date" class="col-12 text-center px-0" style="max-height: 100%;">
                                      <div class="col-12 px-0">
                                        <router-link :to="{ name: 'UserProfile', params: { userId: follower.user_id } }">
                                          <template v-if="follower.avatar_path">
                                            <img style="max-height: 100%; width: 80px;" src="/portfolio/public/storage/avatar_missing.png"/>
                                          </template>
                                          <template v-else>
                                            <img style="max-height: 100%; width: 80px;" src="/portfolio/public/storage/avatar_missing.png"/>
                                          </template>
                                        </router-link>
                                      </div>
                                      <div class="col-12 px-0">
                                        <router-link :to="{ name: 'UserProfile', params: { userId: follower.user_id }}">
                                        <span class="username-text">
                                          {{ follower.username }}
                                        </span>
                                        </router-link>
                                      </div>
                                    </div>
                                  </div>
                                </div>
                              </template>
                            </b-tab>
                            <b-tab :title-link-class="'profile-tab'" :title-item-class="'profile-tab2'">
                              <template class="col-12 text-center" #title>
                                <span>Followers ({{ followers.length }})</span>
                              </template>
                              <template class="col-12" v-if="followers.length == 0">
                                <div class="col-12 text-center">
                                  {{ $t('noFollowersText', 'noFollowersText') }}
                                </div>
                              </template>
                              <template v-else>
                                <div class="row mx-0 mt-3">
                                  <div v-for="(follower, index) in followers" class="col-3 follow-div mb-3">
                                    <!--<template v-if="index > 0">
                                      <div class="col-12 px-0">
                                        <hr class="py-0 my-2">
                                      </div>
                                    </template>-->
                                    <div :title="$t('followedOnText', 'followedOnText') + ' ' + follower.follow_date" class="col-12 text-center px-0" style="max-height: 100%;">
                                      <div class="col-12 px-0">
                                        <router-link :to="{ name: 'UserProfile', params: { userId: follower.user_id } }">
                                          <template v-if="follower.avatar_path">
                                            <img style="max-height: 100%; width: 80px;" src="/portfolio/public/storage/avatar_missing.png"/>
                                          </template>
                                          <template v-else>
                                            <img style="max-height: 100%; width: 80px;" src="/portfolio/public/storage/avatar_missing.png"/>
                                          </template>
                                        </router-link>
                                      </div>
                                      <div class="col-12 px-0">
                                        <router-link :to="{ name: 'UserProfile', params: { userId: follower.user_id }}">
                                        <span class="username-text">
                                          {{ follower.username }}
                                        </span>
                                        </router-link>
                                      </div>
                                    </div>
                                  </div>
                                </div>
                                <!-- -->
                              </template>
                            </b-tab>
                          </b-tabs>
                        </div>
                      </div>
                    </b-tab>
                    <b-tab :title-link-class="'dashboard-tab'" :title-item-class="'dashboard-tab2'">
                      <template class="col-12 text-center" #title>
                        <b-icon icon="card-list"></b-icon>
                        <span class="ml-2">Comments (0)</span>
                      </template>
                      <template>
                        <div v-if="userComments" class="col-12">

                        </div>
                        <div v-else class="col-12 text-center">
                          You do not have any profile comments :(
                        </div>
                      </template>
                    </b-tab>
                  </b-tabs>
                </div>
              </div>
            </template>
            <div class="row mx-0 card-footer dashboard-footer py-2">
              <div class="col-12 px-0 last-login-div" v-if="lastLogin">
                Last login at: {{ this.lastLogin }}
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import Loading from 'vue-loading-overlay';

export default {
  name: 'Dashboard',
  data() {
    return {
      isLoading: true,
      currentPage: this.$route.query.page ? this.$route.query.page : 1,
      perPage: this.$route.query.per_page ? this.$route.query.per_page : 10,
      activityCount: null,
      activityPages: null,
      activityFilters: {
        type: null,
        category: null,
        dateFrom: null,
        dateTo: null
      },
      logTypes: [{ text: 'Select Type', value: null }, 'Success', 'Error', 'Warning', 'Info'],
      logCategories: null,
      range: {
        start: null,
        end: null
      },
      ops: {
        vuescroll: {},
        scrollPanel: {
          easing: 'easeInQuad',
        },
        rail: {},
        bar: {
          showDelay: 500,
          onlyShowBarOnScroll: false,
          keepShow: true,
          background: '#117a8b',
          opacity: 0.4,
          hoverStyle: false,
          specifyBorderRadius: false,
          minSize: 0,
          size: '10px',
          disable: false
        }
      },
      userProfile: null,
      followers: [],
      following: [],
      userComments: null
    }
  },
  created() {
    if(!this.$route.query.page) {
      //this.$router.push({ query: { page: 1 }})
      //this.$route.query = { page: this.currentPage };
      //this.$router.push({ query: { page: this.currentPage, per_page: this.perPage } })
    }

    if(!this.$route.query.per_page) {
      //let query = { ...this.$route.query, per_page: this.perPage };
      //this.$router.replace({ query });
    }
  },
  mounted() {
    this.getDashboard()
  },
  methods: {
    getDashboard() {
      this.$store.dispatch('userStore/getDashboard', {
        page: this.currentPage,
        perPage: this.perPage
      }).then(response => {
        this.isLoading = false
        this.activityCount = response.activityCount
        this.activityPages = response.activityPages
        this.logCategories = response.logCategories
        this.userProfile = response.userData
        this.followers = response.followers
        this.following = response.following
      })
    },
    getActivities() {
      this.$store.dispatch('userStore/getActivities', {
        page: this.currentPage,
        perPage: this.perPage,
        filters: this.activityFilters
      }).then(response => {
        this.activityCount = response.activityCount
        this.activityPages = response.activityPages
      })
    },
    updateUserInfo() {
      this.$store.dispatch('userStore/updateUserInfo', {
        isPrivate: this.userProfile.is_private,
        oldPassword: this.userProfile.old_password,
        newPassword: this.userProfile.new_password,
        email: this.userProfile.email,
        userId: this.userData.id
      }).then(response => {
        if(response.status === 'success' && response.updatedUser) {
          this.userProfile = response.updatedUser
          this.$store.commit('authStore/setUserInfo', response.updatedUser)
        }
      })
    },
    requestActivities(page) {
      this.currentPage = page
      this.getActivities()
    },
    searchActivities() {
      this.getActivities()
    },
    disableAccount() {

    }
  },
  computed: {
    lastLogin() {
      return this.$store.getters['userStore/getUserLastLogin']
    },
    activities() {
      return this.$store.getters['userStore/getUserActivities']
    },
    userData() {
      return this.$store.getters['authStore/getUserData']
    },
    activityPagesData() {
      return this.activityPages
    }
  },
  components: {
    Loading
  },
  filters: {
    toDate: function (value) {
      if (!value) {
        return ''
      } else {
        let time = Date.parse(value)
        let date = new Date(time)
        //let month = date.getMonth()+1

        let string = ('0' + date.getDate()).slice(-2) + '.' + ('0' + (date.getMonth()+1)).slice(-2) + '.' + date.getFullYear() + ' ' + ('0' + (date.getHours())).slice(-2) + ':' + ('0' + (date.getMinutes())).slice(-2) + ':' + ('0' + (date.getSeconds())).slice(-2);
        return string
      }
    }
  }
}
</script>

<style>
  /* #17a2b8 */
  /* #117a8b */
  .dashboard-header {
    border: 0px !important;
    color: white;
    background-color: #588B8B !important;
  }
  .dashboard-footer {
    border: 0px !important;
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
  .activity-filters-toggle {
    border: 1px solid #588B8B;
  }
  .activity-div {
    border: 1px solid #588B8B;
    background-color: rgba(219, 249, 244, 0.2);
  }
  .activity-success {
    background-color: #CACF857A;
  }
  .activity-error {
    background-color: rgba(238, 99, 82, 0.3);
  }
  .activity-warning {
    background-color: rgba(218, 204, 62, 0.4);
  }
  .activity-info {
    background-color: rgba(105, 255, 241, 0.1);
  }
  .activity-name {
    font-weight: 600;
    color: #4d6f57;
  }
  .activity-date {
    color: #00000096;
  }
  .activity-description {
    border-top: 1px solid #588B8B;
    color: #708275;
  }
  .activities-panel {
    max-height: 400px;
  }
  .dashboard-flex {
    display: flex;
    align-items: center;
  }
  .activity-filters {
    border: 1px solid #588B8B;
  }
  .btn-dashboard {
    background-color: #588B8B !important;
    border-radius: 0px;
    border: 1px solid #588B8B !important;
  }
  .activities-pagination > li > button:hover, .activities-pagination > li > span:hover {
    background-color: #477575 !important;
    color: #d6d015 !important;
    border-color: rgba(117, 122, 64, 0.48) !important;
    font-weight: 600;
  }
  .activities-pagination > li.active > button {
    background-color: #117a8b !important;
    color: white !important;
    border-color: #CACF857A !important;
    font-weight: 600;
  }
  .activities-pagination > li > button, .activities-pagination > li > span {
     background-color: #588B8B !important;
     color: white !important;
     border-color: #CACF857A !important;
   }
  .date-arrow {
    align-items: center;
    justify-content: center;
    color: #588B8B;
    display: flex;
    font-size: 24px;
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
  .activity-input {
    background-color: rgba(88, 139, 139, 0.13) !important;
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
    border-left: 1px solid #519191;
  }
  .profile-divs {
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
  .profileUpdate-btn {
    border-radius: 0px;
    background-color: #588B8B !important;
    border: none !important;
  }
  .profileUpdate-btn:hover, .profileUpdate-btn:focus {
    background-color: #366060 !important;
  }
  .profileUpdate-btn:active {
    background-color: #274f4f !important;
  }
  .private-checkbox > label::before {
    background-color: #366060 !important;
    border-color: #366060 !important;
  }
  .disable-modal-header {
    color: white;
    background-color: #588B8B !important;
  }
  .disable-modal-footer {
    color: white;
    background-color: #bdd2d2 !important;
  }
  .disable-modal-body {
    background-color: #cce1e1 !important;
  }
  .username-span {
    background-color: #accece !important;
  }
  .username-text {
    font-weight: 600;
    color: #117a8b !important;
  }
  .follow-div {
    display: flex;
    align-items: center;
  }
</style>