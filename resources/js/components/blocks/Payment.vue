<template>
  <div>
    <h3>Payment</h3>
    <p>After you pay for your campaign, it'll be transfered to directly the screens. You'll receive an e-mail
      notification about updates on your campaign status.</p>

    <v-select
        :items="currencies"
        v-model="selectedCurrency"
        label="Select a cryptocurrency"
        solo
    ></v-select>
    <v-progress-linear
        color="blue accent-4"
        indeterminate
        rounded
        height="4"
        v-if="loadingPaymentData"
    ></v-progress-linear>
    <div class="crypto-details" v-if="paymentData.pay_address">
      <img :src="paymentData.address_img" class="crypto-details__qr" :alt="paymentData.pay_address"/>
      <div>
        <div class="crypto-details__line">
          <b>{{ paymentData.pay_currency.toUpperCase() }} address</b>
          <span>{{ paymentData.pay_address }}</span>
        </div>
        <div class="crypto-details__line">
          <b>Pay amount</b>
          <span>{{ paymentData.pay_amount }} {{ paymentData.pay_currency.toUpperCase() }}</span>
        </div>
        <div class="crypto-details__line" v-if="paymentData.payin_extra_id">
          <b>Memo / Destination Tag</b>
          <span>{{ paymentData.payin_extra_id }}</span>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import QRious from 'qrious';

export default {
  name: "Payment",
  data() {
    return {
      selectedCurrency: null,
      currencies: [],
      paymentData: {},
      loadingPaymentData: false,
      axiosCancelToken: null,
      fetchStatusInterval: null,
    }
  },
  props: {
    campaignCode: {
      required: true,
      type: String,
      default: '',
    }
  },
  watch: {
    async selectedCurrency(currency) {
      this.paymentData = {}
      this.loadingPaymentData = true;
      await this.$axios.post(`campaigns/${this.campaignCode}/payment`, {currency}).then(({data}) => {
        if (data.pay_currency.toLowerCase() !== this.selectedCurrency.toLowerCase()) {
          return;
        }

        this.paymentData = data

        const qr = new QRious({
          value: data.pay_address,
          padding: 0,
        });

        this.paymentData.address_img = qr.toDataURL();
      }).catch(() => {
        this.paymentData = {}
      }).finally(() => {
        this.loadingPaymentData = false;
      })
    }
  },
  async beforeMount() {
    await this.$axios.get(`campaigns/${this.campaignCode}/payment`).then(({data}) => {
      this.currencies = data.currencies.map(c => c.toUpperCase())
    }).catch(() => {
      this.currencies = []
    })
  },
  mounted() {
    this.fetchStatusInterval = setInterval(() => {
      this.$axios.get(`campaigns/${this.campaignCode}/paymentStatus`).then(({data}) => {
        if (data.status && data.status !== 'pending_payment') {
          clearInterval(this.fetchStatusInterval);
          this.fetchStatusInterval = null;
          this.$emit('statusUpdate', data.status)
        }
      })
    }, 15000);
  }
}
</script>

<style scoped lang="scss">
.crypto-details {
  display: flex;
  align-items: flex-start;

  @include media-down(sm) {
    flex-direction: column;
  }
}

.crypto-details__line {
  display: flex;
  flex-direction: column;

  &:not(:first-child) {
    margin-top: 10px;
  }

  span {
    word-break: break-all;
  }
}
</style>
