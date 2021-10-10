<template>
  <div>
    <div class="row mx-0">
      <div class="col-3">
        <b-card class="card-top">
          <template #header>
            <h6 class="mb-0">{{ $t('cryptoUSDPriceHeader', 'cryptoUSDPriceHeader') }}</h6>
          </template>
          <div class="col-12 px-0">
            <template v-if="isLoadingCrypto">
              <div class="text-center">
                <b-spinner variant="success" label="Spinning"></b-spinner>
              </div>
            </template>
            <template v-else>
              <vue-scroll :ops="ops">
                <div class="price-main">
                  <div v-for="crypto in cryptoPrices" class="col-12 px-1 py-1 mb-1 price-div" v-bind:class="[{'green-border': crypto.price_change_24h > 0}, 'red-border']">
                    <img style="max-height: 100%" :src="crypto.image" :alt="crypto.symbol" :title="crypto.name + ' $' + crypto.symbol" />
                    <span style="font-weight: 600">
                      {{ crypto.market_cap_rank }}. {{ crypto.name }}
                    </span>
                    <div class="text-right" style="display: inline-block;">
                      <span v-if="crypto.price_change_24h > 0" class="price-up">
                        ${{ crypto.current_price | formatPrice }} <span class="percent">(+{{ crypto.price_change_percentage_24h | percentageFormat }}%)</span>
                      </span>
                      <span v-else class="price-down">
                        ${{ crypto.current_price | formatPrice }} <span class="percent">({{ crypto.price_change_percentage_24h | percentageFormat }}%)</span>
                      </span>
                      <span style="cursor: pointer;" @click="showCryptoDetails(crypto, $event.target)">
                        <b-icon icon="info-circle" style="font-weight: 600; color: cornflowerblue"></b-icon>
                      </span>
                    </div>
                  </div>
                </div>
              </vue-scroll>
            </template>
          </div>
        </b-card>
      </div>
      <div class="col-9">
        <b-card class="card-top">
          <template #header>
            <h6 class="mb-0">{{ $t('cryptoNewsHeader', 'cryptoNewsHeader') }}</h6>
          </template>
          <div class="col-12 px-0">
            <template v-if="isLoadingNews">
              <div class="text-center">
                <b-spinner variant="success" label="Spinning"></b-spinner>
              </div>
            </template>
            <template v-else>
              <vue-scroll :ops="ops">
                <div class="news-main">
                  <div v-for="news in cryptoNews" class="col-12 news px-0 mb-2">
                    <div class="col-12 news-top text-left px-1 py-1">
                  <span v-if="news.project" class="news-project">
                    <template v-if="news.project.image">
                      <img :src="news.project.image.small" :alt="news.project.symbol" :title="news.project.name + ' $' + news.project.symbol">
                    </template>
                    <template v-else>
                      <span class="coin-top" :title="news.project.name">${{ news.project.symbol }}</span>
                    </template>
                  </span>
                      <span v-if="news.category" class="news-category">
                    {{ news.category }}
                  </span>
                      <span v-if="news.user" class="news-category">
                    / {{ news.user }}
                  </span>
                    </div>
                    <div class="col-12 news-content py-1">
                  <span v-if="news.description">
                    <div v-html="news.description" v-linkified></div>
                  </span>
                    </div>
                    <div class="col-12 news-bottom py-1">
                  <span v-if="news.created_at">
                    {{ news.created_at | toDate }}
                  </span>
                    </div>
                  </div>
                </div>
              </vue-scroll>
            </template>
          </div>
        </b-card>
      </div>
      <CryptoDetailsModal :crypto="cryptoDetails"></CryptoDetailsModal>
    </div>
  </div>
</template>

<script>
import Loading from 'vue-loading-overlay';
import CryptoDetailsModal from '../components/Crypto/CryptoDetailsModal'

export default {
  name: 'Cryptocurrencies',
  data() {
    return {
      cryptoPrices: null,
      cryptoNews: null,
      isLoadingNews: true,
      isLoadingCrypto: true,
      cryptoDetails: null,
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
      }
    }
  },
  created() {

  },
  mounted() {
    this.getCryptoPrice()
    this.getCryptoNews()
  },
  methods: {
    getCryptoNews() {
      this.$store.dispatch('cryptoStore/getCryptoNews', null).then(response => {
        this.isLoadingNews = false

        if(response.news && response.news.status_updates) {
          this.cryptoNews = response.news.status_updates
        }
      }, this)
    },
    getCryptoPrice() {
      this.$store.dispatch('cryptoStore/getCryptoPrice', null).then(response => {
        this.isLoadingCrypto = false

        if(response.crypto) {
          this.cryptoPrices = response.crypto
        }
      }, this)
    },
    showCryptoDetails(crypto, event) {
      this.cryptoDetails = crypto
      //this.$root.$emit('bv::modal::show', 'modal-crypto-details', event)
      this.$bvModal.show('modal-crypto-details', event)
    }
  },
  computed: {

  },
  components: {
    Loading,
    CryptoDetailsModal
  },
  filters: {
    toDate: function (value) {
      if (!value) {
        return ''
      } else {
        let time = Date.parse(value)
        let date = new Date(time)
        let month = date.getMonth()+1

        let string = date.getDate() + '.' + month + '.' + date.getFullYear() + ' ' + date.getHours() + ':' + date.getMinutes() + ':' + date.getSeconds()
//+ ' ' + date.getHours() + ':' + date.getMinutes() + ':' + date.getSeconds()
        return string
      }
    },
    percentageFormat: function (value) {
      if (!value) {
        return ''
      } else {
        return value.toFixed(2)
      }
    },
    formatPrice: function (value) {
      if (!value) {
        return ''
      } else {
        return parseFloat(value.toFixed(2)).toLocaleString()
      }
    }
  }
}
</script>

<style>
  .news-main {
    max-height: 500px;
    /*overflow-y: auto;*/
  }
  .price-main {
    max-height: 500px;
    /*overflow-y: auto;*/
  }
  .news {
    border: 1px solid gray;
  }
  .news-top {
    font-weight: 600;
    border-bottom: 1px solid black;
  }
  .news-content {
    white-space: break-spaces;
    background-color: rgba(88, 139, 139, 0.06) !important;
  }
  .news-bottom {
    border-top: 1px solid black;
    background-color: rgba(88, 139, 139, 0.2) !important;
  }
  .news-category {
    text-transform: capitalize;
  }
  .coin-top {
    text-transform: uppercase;
  }
  .price-div {
    height: 40px;
    white-space: nowrap;
  }
  .red-border {
    border: 1px solid red;
    background-color: rgba(221, 96, 96, 0.05);
  }
  .green-border {
    border: 1px solid green;
    background-color: rgba(96, 221, 104, 0.05);
  }
  .coin-symbol {
    text-transform: uppercase;
  }
  .price-up {
    color: green;
    font-weight: 600;
  }
  .price-down {
    color: red;
    font-weight: 600;
  }
  .percent {
    font-size: 12px;
  }
</style>