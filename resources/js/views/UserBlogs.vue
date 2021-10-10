<template>
  <div class="container">
    <div class="row mx-0 justify-content-center">
      <div class="col-md-9 col-sm-12">
          <template v-if="isLoading">
            <div class="text-center">
              <b-spinner variant="success" label="Spinning"></b-spinner>
            </div>
          </template>
          <template v-else="!isLoading">
            <template v-if="userBlogs && Object.keys(userBlogs).length > 0">
              <div class="card mb-2 blog-card" v-for="blog in userBlogs">
                <div class="card-body pb-2">
                  <div class="col-sm-12 px-0 text-left">
                    <h4>{{ blog.title }} <small class="blog-category-name">( {{ blog.category_name }} )</small></h4>
                    <hr class="mb-2 mt-2">
                  </div>
                  <div class="row mx-0 col-sm-12 px-0 blog-div">
                    <div class="blog-thumbnail">
                      <img v-if="!blog.image_path" :src="`/storage/blogImages/default/not_found.jpg`">
                      <img v-else :src="'/storage/blogImages/' + blog.creator_id + '/blog_thumbnail_' + blog.id + '.jpg'">
                    </div>
                    <div class="px-2 blog-text" v-html="blog.content"></div>
                  </div>
                  <hr class="mb-1 mt-2">
                  <div class="row mx-0 col-sm-12 px-0 text-right">
                    <div class="col-sm-6 px-0 text-left">
                      {{ $t('blogAuthor', 'blogAuthor') }}: {{ blog.username }}
                    </div>
                    <div class="col-sm-6 px-0 text-right">
                      {{ $t('blogCreatedOn', 'blogCreatedOn') }} {{ blog.created_at | toDate }}
                    </div>
                  </div>
                </div>
              </div>
            </template>
            <template v-else>
              <div class="card col-sm-12 px-0 text-center">
                <p class="text-center mt-2 mb-2">{{ $t('noUserBlogsFound', 'noUserBlogsFound') }}</p>
              </div>
            </template>
          </template>
      </div>
      <div class="col-md-3 col-sm-12 px-0">
        <div class="card blog-filters mb-3" v-if="getLoginStatus">
          <div class="card-body">
            <div class="col-sm-12 px-0 text-center">
              <span class="btn btn-primary blog-create-btn" @click="showNewBlogModal($event.target)">{{ $t('createNewBlogBtn', 'createNewBlogBtn') }}</span>
            </div>
          </div>
        </div>
        <div class="card blog-filters">
          <div class="card-body">
            <div class="col-sm-12 px-0 mb-3 text-center">
              <span class="font-weight-bold filters-header-text">Filters</span>
              <hr class="mb-2 mt-2">
            </div>
            <div class="col-sm-12 px-0 text-left" v-if="blogCategories">
              <label for="blog-cat">{{ $t('blogCategoryFilter', 'blogCategoryFilter') }}</label>
              <b-form-select id="blog-cat" class="blog-filter blog-category-select" v-model="blogFilters.category">
                <option v-for="cat in blogCategories" :key="cat.name" value="cat.id">{{ cat.name }}</option>
              </b-form-select>
            </div>
            <div class="col-sm-12 px-0 text-left mt-2">
              <label for="blog-title">{{ $t('blogTitleFilter', 'blogTitleFilter') }}</label>
              <b-form-input
                  id="blog-title"
                  class="blog-filter"
                  v-model="blogFilters.title"
                  trim
              ></b-form-input>
            </div>
            <div class="col-sm-12 px-0 text-left mt-2">
              <label for="blog-authoer">{{ $t('blogAuthorFilter', 'blogAuthorFilter') }}</label>
              <b-form-input
                  id="blog-author"
                  class="blog-filter"
                  v-model="blogFilters.author"
                  trim
              ></b-form-input>
            </div>
            <div class="col-sm-12 px-0 mt-2">
              <v-date-picker class="col-sm-4 px-0" v-model="blogFilters.date" :mode="'date'" is-range>
                <template v-slot="{ inputValue, inputEvents }">
                  <span label-for="activity-date-picker">{{ $t('blogDateFilter', 'blogDateFilter') }}</span>
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
            </div>
            <div class="col-sm-12 px-0 text-center mt-3">
              <span class="btn btn-primary blog-search-btn" @click="getUserBlogs()">{{ $t('searchBlogs', 'searchBlogs') }}</span>
            </div>
          </div>
        </div>
      </div>
    </div>
    <CreateBlogModal :categories="blogCategories" />
  </div>
</template>

<script>
import { mapGetters } from 'vuex'
import Loading from 'vue-loading-overlay';
import CreateBlogModal from '../components/Blog/CreateBlogModal'

export default {
  name: 'UserBlogs',
  data() {
    return {
      isLoading: true,
      userBlogs: null,
      blogCategories: null,
      blogFilters: {
        title: this.$route.query.title ? this.$route.query.title : null,
        author: this.$route.query.author ? this.$route.query.author : null,
        category: this.$route.query.category ? this.$route.query.category : null,
        dateFrom: this.$route.query.dateFrom ? this.$route.query.dateFrom : null,
        dateTo: this.$route.query.dateTo ? this.$route.query.dateTo : null,
        date: null
      }
    }
  },
  created() {
    this.getUserBlogs()
  },
  mounted() {

  },
  methods: {
    getUserBlogs() {
      this.isLoading = true
      let queryParams = Object.entries(this.blogFilters).reduce((acc, [key, val]) => {
        if (!val) return acc
        return { ...acc, [key]: val }
      }, {})
      this.$router.replace({ name: 'UserBlogs', query: { ...queryParams } })

      this.$store.dispatch('blogsStore/getUserBlogs', { blogFilters: this.blogFilters }).then(response => {
        this.isLoading = false
        if(response && response.userBlogs) {
          this.userBlogs = response.userBlogs
        }
        if(response && response.blogCategories) {
          this.blogCategories = response.blogCategories
        }
      }, this)
    },
    showNewBlogModal(event) {
      this.$bvModal.show('modal-new-blog', event)
    }
  },
  computed: {
    ...mapGetters('authStore', ['getLoginStatus', 'getUserData']),
  },
  components: {
    Loading,
    CreateBlogModal
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

<style scoped>
  .blog-thumbnail {
    width: 200px;
    height: 200px;
  }
  .blog-thumbnail > img {
    width: 100%;
    height: 100%;
  }
  .filters-header-text {
    font-size: 22px;
  }
  .blog-category-name {
    font-size: 12px;
  }
  .blog-filter {
    background-color: rgba(88, 139, 139, 0.13) !important
  }
  .blog-search-btn, .blog-create-btn {
    width: 100%;
    background-color: rgba(88, 139, 139, 0.84);
    border-color: rgba(82, 117, 125, 0.84);
  }
  .blog-search-btn:hover, .blog-create-btn:hover {
    width: 100%;
    background-color: rgba(65, 111, 111, 0.84);
    border-color: rgba(82, 117, 125, 0.84);
  }
  .blog-card {
    background-color: rgb(250, 255, 253) !important;
  }
  .blog-filters {
    background-color: rgb(250, 255, 253) !important;
  }
</style>