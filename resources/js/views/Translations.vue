<template>
  <div class="translations-div">
    <div class="row mx-0 justify-content-center">
      <div v-if="userData && (userData.role_name == 'Owner' || userData.role_name == 'Admin')" class="col-4">
        <b-card class="card-top">
          <template #header>
            <h6 class="mb-0">{{ $t('addTranslationCardHeader', 'addTranslationCardHeader') }}</h6>
          </template>
          <div class="col-12">
            <b-form-group id="fieldset-1" :label=" $t('translationKey', 'translationKey')" label-for="input-1" class="mb-2">
              <b-form-input id="input-1" :placeholder="$t('translationKeyPlaceholder', 'translationKeyPlaceholder')" v-model="transKey" trim></b-form-input>
            </b-form-group>

            <b-form-group id="fieldset-2" :label=" $t('translationValue', 'translationValue')" label-for="input-2" class="mb-2">
              <b-form-input id="input-2" :placeholder="$t('translationValuePlaceholder', 'translationValuePlaceholder')" v-model="transValue" trim></b-form-input>
            </b-form-group>

            <b-form-group id="fieldset-3" :label="$t('translationKeyLanguage', 'translationKeyLanguage')" label-for="input-3" class="mb-2">
              <b-form-select
                  id="input-3"
                  v-model="selectedKeyLanguage"
                  :options="[{text: 'English', value: 'EN'}, {text: 'Polski', value: 'PL'}, {text: '日本語', value: 'JP'}]"
                  required
              ></b-form-select>
            </b-form-group>
            <div class="col-12 text-left px-0 mt-3">
              <b-button variant="info" :disabled="!transKey || !transValue" @click="addNewTranslationKey()">{{ $t('addTranslationBtnText', 'addTranslationBtnText') }}</b-button>
            </div>
          </div>
        </b-card>
      </div>
      <div class="col-8">
        <b-card class="card-top">
          <template #header>
            <h6 v-if="userData && (userData.role_name == 'Moderator' || userData.role_name == 'User')" class="mb-0">{{ $t('editTranslationsCardHeader', 'editTranslationsCardHeader') }}</h6>
            <h6 v-else class="mb-0">{{ $t('suggestNewTranslationsCardHeader', 'suggestNewTranslationsCardHeader') }}</h6>
          </template>
          <div class="col-12 mb-2">
            <select class="form-control" @change="getCurrentTranslations()" v-model="selectedLanguage">
              <option :value="null">{{ $t('selectLanguageOption', 'selectLanguageOption') }}</option>
              <option value="EN">English</option>
              <option value="PL">Polski</option>
              <option value="JP">日本語</option>
            </select>
          </div>
          <div class="col-12 row mx-0 text-right" style="justify-content: right">
            <template v-if="totalRows > 0">
              <div class="row mx-0 col-12 px-0 d-flex justify-content-end">
                <div class="col-sm-4 col-md-3 my-1">
                  <b-form-group
                      label="Per page"
                      label-for="per-page-select"
                      label-cols-sm="6"
                      label-cols-md="4"
                      label-cols-lg="3"
                      label-align-sm="right"
                      label-size="sm"
                      class="mb-0"
                  >
                    <b-form-select id="per-page-select" v-model="perPage" :options="pageOptions" size="sm"></b-form-select>
                  </b-form-group>
                </div>
                <div class="my-1">
                  <b-pagination v-model="currentPage" :total-rows="totalRows" :per-page="perPage" align="fill" size="sm" class="my-0"></b-pagination>
                </div>
              </div>
              <div v-if="userData && (userData.role_name == 'Owner' || userData.role_name == 'Admin')" class="row mx-0 col-12 px-0 d-flex justify-content-end mt-3 mb-2">
                <b-button variant="info" @click="updateTranslations()">{{ $t('updateTranslationsBtnText', 'updateTranslationsBtnText') }}</b-button>
              </div>
              <div v-else>
                <b-button variant="info" :disabled="Object.keys(newTranslations).length === 0" @click="suggestNewTranslations()">{{ $t('suggestNewTranslationsBtnText', 'suggestNewTranslationsBtnText') }}</b-button>
              </div>
            </template>
            <b-table class="mt-2 mb-2 table-top" striped hover fixed responsive :busy.sync="isLoading" :current-page="currentPage" :per-page="perPage" :items="translationItems" show-empty>
              <template #empty="scope">
                <div v-if="requestFinished" class="col-12 text-center">
                  {{ $t('noTranslationsFound', 'noTranslationsFound') }}
                </div>
                <div v-else-if="!requestFinished" class="col-12 text-center">
                  {{ $t('pleaseSelectLanguageText', 'pleaseSelectLanguageText') }}
                </div>
              </template>
              <template #head(translationKey)="data">
                <span class="float-left">{{ $t('translationKeyText', 'translationKeyText') }}</span>
              </template>
              <template #head(translationValue)="data">
                <span class="float-left">{{ $t('translationValueText', 'translationValueText') }}</span>
              </template>
              <template #head(newTranslation)="data">
                <span class="float-left">{{ $t('newTranslationText', 'newTranslationText') }}</span>
              </template>
              <template #head(deleteTranslation)="data">
                <span class="float-right">{{ $t('deleteTranslationText', 'deleteTranslationText') }}</span>
              </template>
              <template #cell(translationKey)="data">
                <span class="float-left">{{ data.item.translationKey }}</span>
              </template>
              <template #cell(translationValue)="data">
                <span v-if="data.item.translationValue" class="float-left">{{ data.item.translationValue }}</span>
                <span v-else class="float-left">-</span>
              </template>
              <template #cell(newTranslation)="data">
                <input class="form-control float-left" type="text" v-model="newTranslations[data.item.translationKey]" />
              </template>
              <template #cell(deleteTranslation)="data">
                <b-button v-if="userData && userData.role_name == 'Owner'" class="btn btn-danger text-center m-auto" @click="setSuggestionKey(data.item.translationKey)" v-b-modal.modal-delete-key>X</b-button>
              </template>
            </b-table>
            <template v-if="totalRows > 0">
              <div class="col-sm-4 col-md-3 my-1">
                <b-form-group
                    label="Per page"
                    label-for="per-page-select"
                    label-cols-sm="6"
                    label-cols-md="4"
                    label-cols-lg="3"
                    label-align-sm="right"
                    label-size="sm"
                    class="mb-0"
                >
                  <b-form-select id="per-page-select" v-model="perPage" :options="pageOptions" size="sm"></b-form-select>
                </b-form-group>
              </div>
              <div class="my-1">
                <b-pagination v-model="currentPage" :total-rows="totalRows" :per-page="perPage" align="fill" size="sm" class="my-0"></b-pagination>
              </div>
            </template>
          </div>
        </b-card>
      </div>
      <div class="col-4" v-if="userData && (userData.role_name != 'Owner')">
        <b-card class="card-top">
          <template #header>
            <h6 class="mb-0">{{ $t('submittedSuggestionsCardHeader', 'submittedSuggestionsCardHeader') }}</h6>
          </template>
          <div class="col-12 px-0 sugg-div" v-if="suggestedTranslations">
            <div class="sugg-main mb-2 p-0" v-for="trans in suggestedTranslations">
              <div class="col-12 px-0 row mx-0 sugg-top">
                <div class="col-8"><span class="suggestion-head">{{ trans.suggestion }}</span> ({{trans.language}}) <span class="suggestion-key">[ {{trans.translation_key}} ]</span></div>
                <div class="col-4 text-right">
                  <span v-if="trans.acceptance_status == 1" class="accepted">{{ $t('acceptedSuggestion', 'acceptedSuggestion') }}</span>
                  <span v-else class="not-accepted">{{ $t('notAcceptedSuggestion', 'notAcceptedSuggestion') }}</span>
                </div>
              </div>
              <div class="col-12 px-0 row mx-0 my-1 sugg-bottom">
                <div class="col-10">
                  {{ $t('commentText', 'commentText') }}: {{ trans.suggestion_comment }}
                </div>
                <div class="col-2 text-right">
                  <b-button class="btn btn-danger" @click="setSuggestionId(trans.id)" v-b-modal.modal-delete>X</b-button>
                </div>
              </div>
            </div>
          </div>
        </b-card>
      </div>
    </div>
    <b-modal id="modal-delete-key" body-class="disable-modal-body" footer-class="disable-modal-footer" header-class="disable-modal-header" centered :title="$t('translationDeletionConfirmation', 'translationDeletionConfirmation')">
      <p class="my-1">{{ $t('deleteTranslationConfirmationText', 'deleteTranslationConfirmationText') }}</p>
      <template #modal-footer="{ ok, cancel }">
        <b-button variant="danger" @click="ok(); deleteTranslationKey()">
          {{ $t('translationDeleteBtnConfirmation', 'translationDeleteBtnConfirmation') }}
        </b-button>
        <b-button class="suggestion-delete-btn" @click="cancel()">
          {{ $t('cancelBtnText', 'cancelBtnText') }}
        </b-button>
      </template>
    </b-modal>

    <b-modal id="modal-delete" body-class="disable-modal-body" footer-class="disable-modal-footer" header-class="disable-modal-header" centered :title="$t('suggestionDeletionConfirmation', 'suggestionDeletionConfirmation')">
      <p class="my-1">{{ $t('deleteSuggestionConfirmationText', 'deleteSuggestionConfirmationText') }}</p>
      <template #modal-footer="{ ok, cancel }">
        <b-button variant="danger" @click="ok(); deleteSuggestion()">
          {{ $t('deleteSuggestionBtnConfirmation', 'deleteSuggestionBtnConfirmation') }}
        </b-button>
        <b-button class="suggestion-delete-btn" @click="cancel()">
          {{ $t('cancelBtnText', 'cancelBtnText') }}
        </b-button>
      </template>
    </b-modal>
  </div>
</template>

<script>
import { mapGetters } from 'vuex'

export default {
  name: 'Translations',
  data() {
    return {
      transValue: null,
      transKey: null,
      selectedLanguage: null,
      translations: {
        EN: null,
        PL: null,
        JP: null
      },
      translationItems: null,
      newTranslations: {},
      suggestedTranslations: null,
      currentPage: 1,
      perPage: 10,
      pageOptions: [5, 10, 20, 50],
      totalRows: 0,
      isLoading: true,
      selectedKeyLanguage: 'EN',
      requestFinished: false,
      deletedSuggestion: null,
      deletedKey: null
    }
  },
  mounted() {
    this.getCurrentTranslations()
    if(this.userData && this.userData.role_name != 'Owner') {
      this.getSuggestions()
    }
  },
  methods: {
    getCurrentTranslations() {
      this.currentPage = 1

      if(this.getTranslations[this.selectedLanguage]) {
        this.translationsItems = null
        this.translations[this.selectedLanguage] = this.getTranslations[this.selectedLanguage]
        let translationArray = Array()
        let translationCollection = this.translations[this.selectedLanguage]

        for (const [key, value] of Object.entries(translationCollection)) {
          if(this.userData && (this.userData.role_name != 'Owner')) {
            translationArray.push({ translationKey: key, translationValue: value, newTranslation: null })
          } else {
            translationArray.push({ translationKey: key, translationValue: value, newTranslation: null, deleteTranslation: null })
          }
        }

        this.translationItems = translationArray
        this.totalRows = translationArray.length
        this.requestFinished = true
      }

      if(this.userData && this.userData.role_name != 'Owner') {
        this.getSuggestions()
      }
    },

    addNewTranslationKey() {
      this.$store.dispatch('translationsStore/addNewTranslationKey', {
        translationKey: this.transKey,
        translationValue: this.transValue,
        translationLanguage: this.selectedKeyLanguage
      }).then(response => {

      })
    },

    updateTranslations() {
      this.$store.dispatch('translationsStore/updateLanguageTranslations', {
        translations: this.newTranslations,
        language: this.selectedLanguage
      }).then(response => {
        if(response.status && response.status === 'success') {
          this.newTranslations = {}
          this.updateTranslationsData()
        }
      })
    },

    updateTranslationsData() {
      this.$store.dispatch('translationsStore/updateTranslationsData', {
        language: this.selectedLanguage
      }).then(() => {
        this.getCurrentTranslations()
      })
    },

    suggestNewTranslations() {
      this.$store.dispatch('translationsStore/suggestNewTranslations', {
        translations: this.newTranslations,
        language: this.selectedLanguage
      }).then(response => {
        this.getSuggestions()
      }, this)
    },

    getSuggestions() {
      this.$store.dispatch('translationsStore/getUserSuggestions', null).then(response => {
        if(response.suggestions) {
          this.suggestedTranslations = response.suggestions
        }
      })
    },

    setSuggestionId(id) {
      this.deletedSuggestion = id
    },

    setSuggestionKey(key) {
      this.deletedKey = key
    },

    deleteSuggestion() {
      if(this.deletedSuggestion) {
        this.$store.dispatch('translationsStore/deleteTranslationSuggestion', {
          suggestionId: this.deletedSuggestion
        }).then(response => {
          this.getSuggestions()
        }, this)
      }
    },

    deleteTranslationKey() {
      if(this.deletedKey) {
        this.$store.dispatch('translationsStore/deleteTranslationKey', {
          translationKey: this.deletedKey,
          translationLanguage: this.selectedLanguage
        }).then(response => {
          if(response.status && response.status === 'success') {
            this.newTranslations = {}
            this.updateTranslationsData()
          }
        }, this)
      }
    }
  },
  computed: {
    ...mapGetters('translationsStore', ['getTranslations']),
    userData() {
      return this.$store.getters['authStore/getUserData']
    }
  }
}
</script>

<style>
  .card-top > .card-header {
    background-color: #588B8B !important;
    color: white;
  }

  .table-top > table > thead {
    background-color: rgba(44, 155, 119, 0.44) !important;
    color: #004888;
    font-weight: 700;
  }

  .page-item.active .page-link {
    background-color: #588B8B;
    color: white;
  }

  .suggestion-head {
    font-weight: 600;
    color: #6f827f;
  }

  .suggestion-key {
    font-size: 14px;
  }

  .sugg-div {
    max-height: 450px;
    overflow-y: auto;
  }

  .sugg-main {
    border: 1px solid #1f81a8ab;
  }

  .sugg-top {
    background-color: rgba(88, 139, 139, 0.2) !important;
    border-bottom: 1px solid #519191;
    padding: 5px;
  }

  .sugg-bottom {
    display: flex;
    align-items: center;
  }

  .accepted {
    font-weight: 600;
    color: #32b902;
  }

  .not-accepted {
    font-weight: 600;
    color: #d53d0e;
  }

  .suggestion-delete-btn {
    border-radius: 0px;
    background-color: #588B8B !important;
    border: none !important;
  }
  .suggestion-delete-btn:hover, .suggestion-delete-btn:focus {
    background-color: #366060 !important;
  }
  .suggestion-delete-btn:active {
    background-color: #274f4f !important;
  }

</style>
