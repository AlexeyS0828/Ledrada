<template>
  <v-container class="campaign-container" :fluid="true">
    <LoadingOverlay v-if="loading">
      Loading your campaign...
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
    <div class="campaign-container" v-if="campaign.code">
      <div class="campaign-info">
        <h2 class="campaign-header">Your campaign</h2>
        <div class="campaign-line">
          <h3>E-MAIL</h3>
          <h3 class="font-weight-regular">{{ campaign.email }}</h3>
        </div>
        <div class="campaign-line">
          <h3>CODE</h3>
          <h3 class="font-weight-regular">{{ campaign.code }}</h3>
        </div>
        <div class="campaign-line">
          <h3>STATUS</h3>
          <h3 class="font-weight-regular" :style="{'color': campaignStatusColor}">{{ campaignStatus }}</h3>
        </div>
        <div class="campaign-info-details">
          <Payment v-if="campaign.status === 'pending_payment'" :campaign-code="campaignCode" @statusUpdate="campaign.status = $event"></Payment>
          <p v-else-if="campaign.status === 'payment_received'">Your payment has arrived! Our staff is verifying your videos and soon your video will be live!</p>
        </div>
      </div>
      <div class="campaign">
        <div class="campaign-block">
          <h2 class="campaign-header">Details</h2>
          <h3 class="campaign-subheader">Screens</h3>
          <CampaignCart class="campaign-cart" :is-minified="true" :campaign="campaign"></CampaignCart>
          <h3 class="campaign-subheader campaign-border mt-2">Period</h3>
          <template>
            {{ periodText }}
            ({{ periodDays }} {{ periodDays > 1 ? 'days' : 'day' }})
          </template>
          <h3 class="campaign-subheader campaign-border mt-2 mb-0">Contact details</h3>
          <p>{{ email }}</p>
          <h3 class="campaign-subheader campaign-border mt-3">Summary</h3>
          <div class="campaign-summary-row">
            <h4>Total</h4>
            <h4 class="green--text">${{ totalPrice.toFixed(2) }}</h4>
          </div>
          <div class="campaign-summary-row" v-if="false">
            <h4>Tax</h4>
            <h4 class="green--text">$0.00</h4>
          </div>
        </div>
      </div>
    </div>
  </v-container>
</template>
<script>
import CampaignCart from "../blocks/CampaignCart";
import LoadingOverlay from "../blocks/LoadingOverlay";
import Payment from "../blocks/Payment";

export default {
  components: {
    Payment,
    CampaignCart,
    LoadingOverlay
  },
  data() {
    return {
      loading: false,
      error: null,
      errorMessage: null,
      campaign: {},
      notFound: false,
    }
  },
  computed: {
    email() {
      return this.campaign.email;
    },
    totalPrice() {
      return this.campaign.total_price ?? 0
    },
    periodText() {
      return this.period.join(' - ')
    },
    periodDays() {
      return this.campaign.campaign_length
    },
    period() {
      let period = []
      let campaignFrom = null;
      let campaignTo = null;

      if (this.campaign.campaign_from) {
        campaignFrom = this.campaign.campaign_from.split(' ')[0]
      }

      if (this.campaign.campaign_to) {
        campaignTo = this.campaign.campaign_to.split(' ')[0]
      }

      period.push(campaignFrom)
      if (campaignTo !== campaignFrom) {
        period.push(campaignTo)
      }

      return period
    },
    campaignCode() {
      if (!this.$route.params.email || !this.$route.params.code) {
        return null;
      }

      return [this.$route.params.email, this.$route.params.code.toUpperCase()].map(s => s.replace('/', '').trim()).join('/')
    },
    campaignStatus() {
      switch (this.campaign.status) {
        case 'pending_payment':
          return 'Waiting for payment';
        case 'canceled':
          return 'Cancelled';
        case 'payment_received':
          return 'Payment received';
      }
    },
    campaignStatusColor() {
      switch (this.campaign.status) {
        case 'pending_payment':
          return 'orange';
        case 'canceled':
          return 'red';
        default:
          return 'green';
      }
    },
  },
  async created() {
    if (!this.$route.params.email || !this.$route.params.code) {
      this.$router.push({name: 'find-campaign'})
      return;
    }

    this.loading = true;
    const params = [this.$route.params.email, this.$route.params.code].map(s => s.replace('/', '').trim()).join('/')
    await this.$axios.get(`campaigns/${params}`).then(({data}) => {
      this.campaign = data
    }).catch(() => {
      this.$router.push({name: 'find-campaign'})
    }).finally(() => {
      this.loading = false;
    })
  },
  watch: {},
  methods: {}
}
</script>
<style lang="scss" scoped>

.campaign-container {
  padding: 0 18px;
  display: flex;

  @include media-down(sm) {
    padding: 0;
  }
}

.campaign-container {
  display: flex;
  flex-direction: column;
  margin: 24px auto 0;
  justify-content: flex-start;
  align-items: flex-start;
  z-index: 6;

  @media (min-width: 800px) {
    flex-direction: row;
  }
}

.campaign-info {
  flex-shrink: 0.75;
  max-width: 100%;
  width: 549px;
  display: inline-block;
  padding: 24px;
  background-color: #ffffff;
  box-shadow: rgba(0, 0, 0, 0.09) 0 3px 12px;
  color: #000;

  @media (max-width: 1199px) {
    width: unset;
  }
}

.campaign {
  max-width: 500px;
  width: 100%;
  display: inline-block;
  padding: 24px;
  background-color: #ffffff;
  box-shadow: rgba(0, 0, 0, 0.09) 0 3px 12px;
  color: #000;

  @media (min-width: 1200px) {
    flex-shrink: 0;
    transform: scale(0.9);
    transform-origin: top left;
  }
}

.campaign-block {
  max-width: 480px;
}

.campaign-header {
  margin-bottom: 18px;
}

.campaign-subheader {
  font-size: 18px;
  font-weight: 600;
  margin-bottom: 12px;
}

.campaign-border {
  border-top: 1px solid #dcdcdc;
  padding-top: 12px;
}

.campaign-summary-row {
  display: flex;
  width: 100%;
  justify-content: space-between;
}

.price {
  color: #013ec6;
}

.campaign-cart {
  margin-left: -10px;

  ::v-deep {
    .cart-video__header {
      margin-bottom: 16px;
      text-align: left;
    }
  }
}

.campaign-id {
  font-style: italic;
  font-weight: 300;
  font-size: 17px;
}

.campaign-line {
  display: flex;

  h3:first-child {
    min-width: 85px;
  }
}

.campaign-info-details {
  border-top: 1px solid #dcdcdc;
  padding-top: 15px;
  margin-top: 15px;
}
</style>
