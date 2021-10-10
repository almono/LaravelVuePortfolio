<template>
  <div>
    <b-modal id="modal-crypto-details" body-class="disable-modal-body" footer-class="disable-modal-footer" header-class="disable-modal-header" centered :title="$t('cryptoDetailsModalTitle', 'cryptoDetailsModalTitle')">
      <template v-if="cryptoInfo">
        <div class="col-12 px-0">
          <div class="col-12 px-0">Name: {{ cryptoInfo.name }} (<b> {{ cryptoInfo.symbol }} </b>)</div>
          <div class="col-12 px-0">Current Price: <b>{{ cryptoInfo.current_price | formatPrice }}$</b></div>
          <div class="col-12 px-0">Market Cap: {{ cryptoInfo.market_cap | formatPrice }}$</div>
          <div class="col-12 px-0">Market Cap Rank: <b>#{{ cryptoInfo.market_cap_rank }}</b></div>
          <div class="col-12 px-0">Total Volume: {{ cryptoInfo.total_volume | formatPrice }}$</div>
          <div class="col-12 px-0">Coingecko Link: <a :href="'https://www.coingecko.com/en/coins/' + cryptoInfo.id " target="_blank">{{ cryptoInfo.name }}</a></div>
        </div>
        <div class="col-12 px-0 mt-1">
          <div class="col-12 px-0 collapse-div" v-b-toggle.collapse-crypto-info @click=toggleStatus()>
            <span v-if="isToggled"><b-icon icon="file-minus"></b-icon></span>
            <span v-else><b-icon icon="file-plus"></b-icon></span> Toggle Detailed Information
          </div>
          <b-collapse id="collapse-crypto-info">
            <div class="col-12 py-2 detailed-div" style="border: 1px solid gray; border-top: 0px;">
              <p class="mb-1 font-weight-bold">24h Changes</p>
              <div class="col-12 px-0">Price: <span v-bind:class="{ 'price-down': cryptoInfo.price_change_24h < 0,'price-up': cryptoInfo.price_change_24h > 0}">{{ cryptoInfo.price_change_24h | formatPrice }}$</span></div>
              <div class="col-12 px-0">Price Change Percentage: {{ cryptoInfo.price_change_percentage_24h | percentageFormat }}%</div>
              <div class="col-12 px-0">Market Cap: <span v-bind:class="{ 'price-down': cryptoInfo.market_cap_change_24h < 0,'price-up': cryptoInfo.market_cap_change_24h > 0}">{{ cryptoInfo.market_cap_change_24h | formatPrice }}$</span></div>
              <div class="col-12 px-0">Market Cap Percentage: {{ cryptoInfo.price_change_percentage_24h | percentageFormat }}%</div>
              <p class="mb-1 mt-2 font-weight-bold">Other Details</p>
              <div class="col-12 px-0">ATH: {{ cryptoInfo.ath }}$</div>
              <div class="col-12 px-0">ATH Date: {{ cryptoInfo.ath_date | toDate }}</div>
              <div class="col-12 px-0">Circulating Supply: {{ cryptoInfo.circulating_supply | separator }}</div>
              <div class="col-12 px-0">Max Supply: {{ cryptoInfo.max_supply | separator }}</div>
            </div>
          </b-collapse>
        </div>
      </template>
      <template #modal-footer="{ ok, cancel }">
        <b-button class="profileUpdate-btn" @click="cancel()">
          Close
        </b-button>
      </template>
    </b-modal>
  </div>
</template>

<script>
  export default {
    name: 'CryptoDetailsModal',
    props: ['crypto'],
    data() {
      return {
        isToggled: false
      }
    },
    computed: {
      cryptoInfo() {
        return this.$props.crypto
      }
    },
    methods: {
      toggleStatus() {
        this.isToggled = !this.isToggled
      }
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
      },
      separator: function (value) {
        if(!value) {
          return ''
        }

        return value.toLocaleString()
      }
    }
  }
</script>

<style scoped>
  .price-up {
    color: green;
  }
  .price-down {
    color: red;
  }
  .collapse-div {
    border: 1px solid gray;
  }
</style>