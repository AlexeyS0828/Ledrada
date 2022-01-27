<template>
  <v-container class="checkout-container" :fluid="true">
    <LoadingOverlay v-if="loading">
      Creating your campaign...
    </LoadingOverlay>
    <v-snackbar
        v-model="error"
        timeout="3000"
        class="mb-5"
        top
        color="red accent-4"
        elevation="24"
    >
      {{ errorMessage }}

      <template v-slot:action="{ attrs }">
        <v-btn
            text
            v-bind="attrs"
            @click="error = null"
        >
          Close
        </v-btn>
      </template>
    </v-snackbar>
    <div class="checkout">
      <div class="checkout-block">
        <h2 class="checkout-header">Checkout</h2>
        <h3 class="checkout-subheader">Screens</h3>
        <MiniCart :is-minified="true" class="checkout-cart"></MiniCart>
        <h3 class="checkout-subheader checkout-border mt-2">Period</h3>
        <template v-if="periodDays > 0">
          {{ periodText }}
          ({{ periodDays }} {{ periodDays > 1 ? 'days' : 'day' }})
        </template>
        <template v-else>
          <span @click="$router.push('/video')" class="text-decoration-underline" style="cursor: pointer">Select a campaign date</span>
        </template>
        <h3 class="checkout-subheader checkout-border mt-2 mb-0">Contact details</h3>
        <v-text-field placeholder="john@doe.com" hint="We'll send a payment confirmation to this email"
                      v-model="email"
                      :rules="rules"></v-text-field>
        <h3 class="checkout-subheader checkout-border mt-3">Summary</h3>
        <div class="checkout-summary-row">
          <h4>Total</h4>
          <h4 class="green--text">${{ totalPrice.toFixed(2) }}</h4>
        </div>
        <div class="checkout-summary-row" v-if="false">
          <h4>Tax</h4>
          <h4 class="green--text">$0.00</h4>
        </div>
        <v-btn
            width="100%"
            height="48px"
            depressed
            color="success"
            class="mt-6"
            @click="submitCampaign"
            :disabled="!periodDays"
        >
          Pay and send your video
        </v-btn>
      </div>
    </div>
  </v-container>
</template>
<script>
import {mapGetters} from "vuex";
import MiniCart from "../blocks/MiniCart";
import LoadingOverlay from "../blocks/LoadingOverlay";

export default {
  components: {
    MiniCart,
    LoadingOverlay
  },
  data() {
    return {
      loading: false,
      error: null,
      errorMessage: null,
      rules: [
        value => !!value || 'Required.',
        value => {
          const pattern = /^(([^<>()[\]\\.,;:\s@"]+(\.[^<>()[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/
          return pattern.test(value) || 'E-mail address is invalid'
        },
      ],
    }
  },
  computed: {
    ...mapGetters({
      currentVideo: 'video/getCurrentVideo',
      totalPrice: 'stats/getTotalPrice',
      period: 'cart/getPeriod',
      cart: 'cart/getCart',
    }),
    email: {
      get() {
        return this.$store.getters['cart/getEmail']
      },
      set(email) {
        this.$store.commit('cart/setEmail', email)
      }
    }
    ,
    periodText() {
      return this.period.join(' - ')
    },
    periodDays() {
      return this.$store.getters['cart/getPeriodDays']
    },
    period() {
      return this.$store.getters['cart/getPeriod']
    }
  },
  async mounted() {
    await this.$store.dispatch('stats/getStats')
  },
  watch: {},
  methods: {
    submitCampaign() {
      const payload = {
        video: this.currentVideo.uuid,
        email: this.email,
        screens: this.cart.map(screen => screen.id),
        days: this.period
      }

      this.loading = true
      this.$axios.post('campaigns', payload).then(({data}) => {
        this.$store.commit('cart/emptyCart');
        setTimeout(()=>{
          this.$router.push({name: 'campaign', params: {email: data.email, code: data.code}})
        })
      }).catch(
          ({response})=>{
            this.errorMessage = Object.values(response.data.errors)[0][0]
            this.error = true
            console.log(response)
          }
      ).finally(() => {
        this.loading = false
      })

    }
  }
}
</script>
<style lang="scss" scoped>

.checkout-container {
  padding: 0 18px;
  display: flex;

  @include media-down(sm) {
    padding: 0;
  }
}

.checkout {
  @include media-up(sm) {
    z-index: 6;
  }
  max-width: 500px;
  width: 100%;
  display: inline-block;
  margin: 24px auto 0;
  padding: 24px;
  background-color: #ffffff;
  box-shadow: rgba(0, 0, 0, 0.09) 0 3px 12px;
  color: #000;
}

.checkout-block {
  max-width: 480px;
}

.checkout-header {
  margin-bottom: 18px;
}

.checkout-subheader {
  font-size: 18px;
  font-weight: 600;
  margin-bottom: 12px;
}

.checkout-border {
  border-top: 1px solid #dcdcdc;
  padding-top: 12px;
}

.checkout-summary-row {
  display: flex;
  width: 100%;
  justify-content: space-between;
}

.price {
  color: #013ec6;
}

.checkout-cart {
  margin-left: -10px;

  ::v-deep {
    .cart-video__header {
      margin-bottom: 16px;
      text-align: left;
    }
  }
}
</style>
