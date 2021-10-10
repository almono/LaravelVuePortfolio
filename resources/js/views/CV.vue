<template>
  <div class="cv-card">
    <b-row class="mx-0">
      <b-col class="sticky-top" sm="12" md="2" lg="2" offset-lg="1">
        <div class="sticky-div">
          <b-card>
            <div class="col-12 px-0 text-center">
              <h4>{{ $t('cvCustomizerTitle') }}</h4>
            </div>
            <hr>
            <div class="col-12 px-0">
              <h5>{{ $t('cvDisplayType') }}</h5>
            </div>
            <div class="col-12 px-0">
              <b-form-radio-group
                  id="btn-radios-2"
                  v-model="singlePage"
                  :options="displayOptions"
                  button-variant="outline-info"
                  size="md"
                  name="radio-btn-outline"
                  buttons
                  style="width: 100%"
                  @change="changeDisplayType()"
              ></b-form-radio-group>
            </div>
            <hr>
            <template v-if="singlePage && !isLoading">
              <div class="col-12 px-0">
                <h5>{{ $t('toggleCVSections') }}</h5>
              </div>
              <div class="col-12 px-1">
                <div class="col-12 px-0" v-if="cvData.education">
                  <b-form-checkbox id="checkbox-0" v-model="educationEnabled" name="checkbox-0" :value="true" :unchecked-value="false">
                    {{ $t('education') }}
                  </b-form-checkbox>
                </div>
                <div class="col-12 px-0" v-if="cvData.experience">
                  <b-form-checkbox id="checkbox-1" v-model="experienceEnabled" name="checkbox-1" :value="true" :unchecked-value="false">
                    {{ $t('experience') }}
                  </b-form-checkbox>
                </div>
                <div class="col-12 px-0" v-if="cvData.skills">
                  <b-form-checkbox id="checkbox-2" v-model="skillsEnabled" name="checkbox-2" :value="true" :unchecked-value="false">
                    {{ $t('skills') }}
                  </b-form-checkbox>
                </div>
                <div class="col-12 px-0" v-if="cvData.interests">
                  <b-form-checkbox id="checkbox-3" v-model="interestsEnabled" name="checkbox-3" :value="true" :unchecked-value="false">
                    {{ $t('interests') }}
                  </b-form-checkbox>
                </div>
                <div class="col-12 px-0" v-if="cvData.other_projects">
                  <b-form-checkbox id="checkbox-4" v-model="otherEnabled" name="checkbox-4" :value="true" :unchecked-value="false">
                    {{ $t('otherProjects') }}
                  </b-form-checkbox>
                </div>
              </div>
            </template>
          </b-card>
          <b-card class="mt-3">
            <div class="col-12 px-0 text-center">
              <h4>{{ $t('downloadCVTitle') }}</h4>
            </div>
            <hr>
            <div class="col-12 px-0">
              <h5>{{ $t('language') }}</h5>
            </div>
            <div class="col-12 px-0 mt-1">
              <b-form-radio-group
                  id="btn-radios-3"
                  v-model="cvLanguage"
                  :options="languageOptions"
                  button-variant="outline-info"
                  size="md"
                  name="radio-btn-outline"
                  buttons
                  style="width: 100%"
              ></b-form-radio-group>
            </div>
            <hr>
            <div class="col-12 px-0">
              <h5>{{ $t('extraCVCriteria') }}</h5>
            </div>
            <div class="col-12 px-0">
              <b-form-checkbox id="checkbox-5" v-b-popover.hover.top="'Only checked sections will be included in final PDF'" v-model="allowToggled" name="checkbox-5" :value="true" :unchecked-value="false">
                {{ $t('cvExportOnlyToggledSections') }}
              </b-form-checkbox>
            </div>
            <hr>
            <div class="col-12 px-0 mt-2 text-center">
              <span v-if="!isDownloading" class="btn btn-info" @click="downloadCV()">{{ $t('download') }}</span>
              <span v-else class="btn btn-info">{{ $t('downloading') }}</span>
            </div>
          </b-card>
        </div>
      </b-col>
      <b-col sm="12" md="10" lg="6">
        <b-card class="main-card" v-if="cvData">
          <template v-if="cvData.main_info">
            <div class="row mx-0 mt-2 mb-2">
              <div class="col-9 px-0 main-info">
                <h3 class="font-weight-bold mb-2">Curriculum Vitae</h3>
                <h5 class="font-weight-bold mt-2 mb-2">Paweł Walczykiewicz</h5>
                <template v-if="cvData.main_info">
                  <div class="col-12 px-0" v-for="info in cvData.main_info">
                    <span class="info-label">{{ $t(info.info_title) }}: </span>
                    <template v-if="info.info_title == 'Github' || info.info_title == 'LinkedIn'">
                      <a :href="info.info_desc" target="_blank">{{ info.info_desc }}</a>
                    </template>
                    <template v-else>
                      <span>{{ $t(info.info_desc) }}</span>
                    </template>
                  </div>
                </template>
              </div>
              <div class="col-3 px-0 main-picture">
                <img style="max-width: 100%" src="/storage/CV_Photo.jpg"/>
              </div>
            </div>
            <hr>
          </template>
          <template v-if="!isLoading">
            <!-- Tabs page display variant -->
            <template v-if="!singlePage && cvData">
              <TabsPageView :tabData="cvData" />
            </template>
            <!-- Single page display variant -->
            <template v-else-if="singlePage && cvData">
              <SinglePageView :singleData="cvData" :educationEnabledInfo="educationEnabled" :experienceEnabledInfo="experienceEnabled" :skillsEnabledInfo="skillsEnabled" :interestsEnabledInfo="interestsEnabled" :otherEnabledInfo="otherEnabled" />
            </template>
          </template>
          <template v-else>
            <div class="loading-window">
              <loading
                  :active.sync="isLoading"
                  :can-cancel="false"
                  :is-full-page="false">
              </loading>
            </div>
          </template>
        </b-card>
      </b-col>
    </b-row>
  </div>
</template>

<script>
// Import component
import Loading from 'vue-loading-overlay';
// Import stylesheet
import 'vue-loading-overlay/dist/vue-loading.css';
import SinglePageView from '../components/CV_Components/SinglePageView';
import TabsPageView from '../components/CV_Components/TabsPageView';

export default {
  name: 'CV',
  data() {
    return {
      isLoading: true,
      singlePage: true,
      cvData: null,
      educationEnabled: true,
      experienceEnabled: true,
      skillsEnabled: true,
      interestsEnabled: true,
      otherEnabled: true,
      allowToggled: false,
      isDownloading: false,
      displayOptions: [
        { text: this.$t('Single'), value: true },
        { text: this.$t('Tabs'), value: false }
      ],
      cvLanguage: 'EN',
      languageOptions: [
        { text: 'EN', value: 'EN' },
        { text: 'PL', value: 'PL' },
        { text: 'JP', value: 'JP' }
      ]
    }
  },
  provide() {
    return {
      educationEnabled: this.educationEnabled
    }
  },
  mounted() {
    this.getCVData()
  },
  methods: {
    getCVData() {
      this.$store.dispatch('curriculumStore/getCurriculumData').then(response => {
        this.isLoading = false

        if(response.cv_data) {
          this.cvData = response.cv_data
        } else {
          this.cvData = null
        }
      })
    },
    downloadCV() {
      //isDownloading = true
      this.$store.dispatch('curriculumStore/downloadCV',
      {
        educationEnabled: this.educationEnabled,
        experienceEnabled: this.experienceEnabled,
        skillsEnabled: this.skillsEnabled,
        interestsEnabled: this.interestsEnabled,
        otherEnabled: this.otherEnabled,
        exportLanguage: this.cvLanguage
      }).then(response => {
        const url = window.URL.createObjectURL(new Blob([response]));
        const link = document.createElement('a');
        link.href = url;
        link.setAttribute('download', 'Pawel_Walczykiewicz_CV.pdf'); //or any other extension
        document.body.appendChild(link);
        link.click();

      }).catch(error => {
        console.log(error);
      })
    },
    changeDisplayType() {
      this.educationEnabled = true
      this.experienceEnabled = true
      this.skillsEnabled = true
      this.interestsEnabled = true
      this.otherEnabled = true
    }
  },
  computed: {
    cvInfo() {
      return this.$store.getters['curriculumStore/getCVInfo']
    }
  },
  components: {
    Loading,
    SinglePageView,
    TabsPageView
  }
}
</script>

<style scoped>
  .sticky-div {
    position: sticky;
    top: 30px;
  }
  .main-card > .card-body {
    padding-bottom: 0px;
  }
  .loading-window {
    min-height: 400px;
  }
  .info-label {
    font-weight: 600;
  }
  .cv-card {
    color: #588B8B;
  }
</style>
