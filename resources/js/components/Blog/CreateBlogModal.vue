<template>
  <div>
    <b-modal id="modal-new-blog" size="xl" no-close-on-backdrop body-class="disable-modal-body" footer-class="disable-modal-footer" header-class="disable-modal-header" centered :title="$t('createBlogModalTitle', 'createBlogModalTitle')">
      <template>
        <div class="col-sm-12 text-left mb-2">
          <small>{{ $t('recommendedFileFormat', 'recommendedFileFormat') }}</small>
          <b-form-file
              class="blog-file"
              v-model="blogFile"
              :state="Boolean(blogFile)"
              :placeholder="$t('blogImagePlaceholder', 'blogImagePlaceholder')"
              :drop-placeholder="$t('dropFileHereText', 'dropFileHereText')"
              @change="checkFileSize($event)"
              ref="blog-file"
          ></b-form-file>
          <small v-if="blogFile"> {{ $t('uploadedFilesize', 'uploadedFilesize') }}: {{ (blogFile.size / 1024) | formatSize }} KB / {{ (blogFile.size / 1024 / 1024) | formatSize }} MB</small>
        </div>
        <div class="col-sm-12 px-0">

        </div>
        <div class="col-sm-12">
          <vue-editor class="blog-editor" v-model="content" :editorToolbar="customToolbar"></vue-editor>
        </div>
        <div class="col-sm-12 mt-2" v-if="getCategories">
          <span class="col-sm-12 px-0 text-left">
            {{ $t('blogCategoryLabel', 'blogCategoryLabel') }}
          </span>
          <b-form-select class="blog-category-select" v-model="blogCategory">
            <option v-for="cat in getCategories" :key="cat.name" value="cat.id">{{ cat.name }}</option>
          </b-form-select>
        </div>
      </template>
      <template #modal-footer="{ ok, cancel }">
        <div class="col-sm-12 text-right px-0">
          <small v-if="fileWarning" class="file-warning">{{ $t('fileTooBigWarning', 'fileTooBigWarning') }}</small>
        </div>
        <b-button class="clearContent-btn float-left" @click="clearContent()">
          {{ $t('clearText', 'clearText') }}
        </b-button>
        <b-button class="profileUpdate-btn" :disabled="fileWarning || !blogCategory || !blogTitle" @click="createNewBlog()">
          {{ $t('saveText', 'saveText') }}
        </b-button>
      </template>
    </b-modal>
  </div>
</template>

<script>
import { VueEditor } from "vue2-editor";

export default {
  name: 'CreateBlogModal',
  props: ['categories'],
  data() {
    return {
      isToggled: false,
      content: null,
      blogFile: null,
      fileWarning: false,
      blogCategory: null,
      blogTitle: null,
      customToolbar: [
        [{ header: [false, 1, 2, 3, 4, 5, 6] }],
        ["bold", "italic", "underline", "strike"], // toggled buttons
        [
          { align: "" },
          { align: "center" },
          { align: "right" },
          { align: "justify" }
        ],
        ["blockquote"],
        [{ list: "ordered" }, { list: "bullet" }, { list: "check" }],
        [{ indent: "-1" }, { indent: "+1" }], // outdent/indent
        [{ color: [] }, { background: [] }], // dropdown with defaults from theme,
        ["clean"] // remove formatting button
      ]
    }
  },
  computed: {
    getCategories() {
      return this.$props.categories
    }
  },
  methods: {
    checkFileSize(event) {
      if(event.target && event.target.files[0]) {
        let blogThumbnail = event.target.files[0]
        if (blogThumbnail.size > 1024 * 1024) {
          this.fileWarning = true
        } else {
          this.fileWarning = false
        }
      }
    },
    createNewBlog() {
      let data = new FormData()
      data.append('content', this.content)
      data.append('blogFile', this.blogFile)
      data.append('blogCategory', this.blogCategory)
      data.append('blogTitle', this.blogTitle)

      if(!this.fileWarning) {
        this.$store.dispatch('blogsStore/createNewBlog', data).then(response => {
          if(response) {
            if(response.status === 'success') {
              this.content = null
              this.loadBlog
              this.hideModal()
            }
          }
        }, this)
      }
    },
    hideModal() {
      this.$bvModal.hide('modal-new-blog')
    },
    clearContent() {
      this.content = null
      this.blogFile = null
      this.fileWarning = false
      this.blogCategory = null
    }
  },
  components: {
    VueEditor
  },
  filters: {
    formatSize: function (value) {
      if (!value) {
        return ''
      } else {
        return parseFloat(value.toFixed(3)).toLocaleString()
      }
    }
  }
}
</script>

<style>
  .clearContent-btn {
    border-radius: 0px;
    background-color: #D54D4D !important;
    border: none !important;
  }
  .blog-file > label {
    border-color: gray !important;
    background-color: #cce1e1 !important;
  }
  .blog-editor > div {
    border-color: gray !important;
  }
  .file-warning {
    color: #a40404;
  }
  .blog-category-select {
    background-color: #cce1e1 !important;
    border-color: gray !important;
  }
</style>