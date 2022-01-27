<template>
  <v-container class="campaign-container" :fluid="true">
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
    <div class="campaign">
      <div class="campaign-block">
        <h2 class="campaign-header">Check your campaign</h2>
        <h3 class="campaign-subheader">E-mail</h3>
        <v-text-field placeholder="john@doe.com" hint="E-mail used for creating a campaign" class="mb-5"
                      v-model="email"></v-text-field>
        <h3 class="campaign-subheader">Code</h3>
        <v-text-field placeholder="ABC123" hint="A special code you were given when creating a campaign"
                      v-model="code"></v-text-field>

        <v-btn
            width="100%"
            height="48px"
            depressed
            color="primary"
            class="mt-6"
            @click="checkCampaign"
        >
          GO
        </v-btn>
      </div>
    </div>
  </v-container>
</template>
<script>
import {mapGetters} from "vuex";
import CampaignCart from "../blocks/CampaignCart";
import LoadingOverlay from "../blocks/LoadingOverlay";

export default {
  components: {
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
      email: null,
      code: null,
    }
  },
  computed: {
    ...mapGetters({}),
  },
  async created() {

  },
  watch: {},
  methods: {
    checkCampaign() {
      if (this.email && this.code) {
        this.$router.push({name: 'campaign', params: {email: this.email, code: this.code.toUpperCase()}})
      }
    }
  }
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

.campaign {
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
</style>
