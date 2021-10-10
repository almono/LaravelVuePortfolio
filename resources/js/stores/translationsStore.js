import axios from 'axios'
import {backendUrl} from "../helpers/backendUrl";
import { i18n } from '../plugins/i18n'

const state = {
  translations: null
};

const getters = {
  getTranslations: (state) => state.translations
};

const actions = {
  async getTranslations( context ){
    return await axios.get(`${backendUrl()}/getAvailableTranslations`, null).then(response => {
        if(response.data && response.data.translations) {
          context.commit("setTranslations", response.data.translations)

          let curr_lang = localStorage.getItem('currentLanguage') || 'EN'

          if(curr_lang != 'EN' && response.data.translations[curr_lang] != undefined) {
            i18n.setLocaleMessage(curr_lang, response.data.translations[curr_lang])
          }

          i18n.setLocaleMessage('EN', response.data.translations.EN)
          i18n.locale = curr_lang
        }

        return response.data
    })
  },

  async addNewTranslationKey( context, data ) {
    return await axios.post(`${backendUrl()}/addNewTranslationKey`, data).then(response => {
      return response.data
    })
  },

  async updateLanguageTranslations( context, data ) {
    return await axios.post(`${backendUrl()}/updateLanguageTranslations`, data).then(response => {
      return response.data
    })
  },

  async updateTranslationsData( context, data ) {
    return await axios.post(`${backendUrl()}/updateTranslationsData`, data).then(response => {
      state.translations[data.language] = response.data.translations
      context.commit("setLanguageTranslations", response.data.translations, data.language)
      i18n.setLocaleMessage(data.language, response.data.translations[data.language])
      return response.data
    })
  },

  async suggestNewTranslations( context, data ) {
    return await axios.post(`${backendUrl()}/suggestNewTranslations`, data).then(response => {
      return response.data
    })
  },

  async getUserSuggestions( context, data ) {
    return await axios.post(`${backendUrl()}/getUserSuggestions`, data).then(response => {
      return response.data
    })
  },

  async deleteTranslationSuggestion( context, data ) {
    return await axios.post(`${backendUrl()}/deleteTranslationSuggestion`, data).then(response => {
      return response.data
    })
  },

  async deleteTranslationKey( context, data ) {
    return await axios.post(`${backendUrl()}/deleteTranslationKey`, data).then(response => {
      return response.data
    })
  }
};

const mutations = {
  setTranslations: (state, translations) => (state.translations = translations),
  setLanguageTranslations: (state, translations, language) => (state.translations[language] = translations)
};

export default {
  namespaced: true,
  state,
  getters,
  actions,
  mutations
}


