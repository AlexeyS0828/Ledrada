<template>
  <v-container style="height:100%">
    <div class="home">
      <div class="padded-title">
        <h2>
          Play your ad on dozens</br>of LED screens
        </h2><br/>
        <transition name="banner-fade" mode="out-in">
          <h3 v-if="promoBanner === i" :key="i" v-for="(promoBannerText, i) in promoBanners"
              :title="promoBannerText">
            {{ promoBannerText }}
          </h3>
        </transition>
      </div>
      <div class="campaign-details">
        <div class="d-flex flex-column">
          <span>Campaign period</span>
          <div>
            <v-dialog
                ref="period"
                v-model="selectPeriod"
                :return-value.sync="period"
                persistent
                width="290px"
            >
              <template v-slot:activator="{ on, attrs }">
                <v-text-field
                    v-model="periodText"
                    outlined
                    background-color="white"
                    v-bind="attrs"
                    placeholder="Select date range"
                    readonly
                    v-on="on"
                ></v-text-field>
              </template>
              <v-date-picker
                  v-model="period"
                  range
                  color="black darken-4"
                  no-title
              >
                <v-spacer></v-spacer>
                <v-btn
                    text
                    color="primary"
                    @click="selectPeriod = false"
                >
                  Cancel
                </v-btn>
                <v-btn
                    text
                    color="primary"
                    @click="$refs.period.save(period)"
                >
                  OK
                </v-btn>
              </v-date-picker>
            </v-dialog>
          </div>
        </div>
        <div class="campaign-area">
          <span>Campaign area</span>
          <div>
            <v-text-field
                outlined
                background-color="white"
                placeholder="ex. America"
            ></v-text-field>
          </div>
        </div>
        <div class="search-button">
          <v-btn
              :loading="loading"
              fab
              @click="$router.push({name: 'screens'})"
          >
            <v-icon dark>
              mdi-magnify
            </v-icon>
          </v-btn>
        </div>
      </div>
    </div>
  </v-container>
</template>
<script>
export default {
  data() {
    return {
      loading: false,
      selectPeriod: false,
      promoBanner: 0,
      promoBanners: [
        'Buy an advertisement in 5 minutes',
        'Pay using credit card or cryptocurrencies',
        'Monitor performance of your ads in real time',
        'No hidden fees',
      ],
    }
  },
  computed: {
    periodText() {
      return this.period.join(' - ')
    },
    period: {
      get() {
        return this.$store.getters['cart/getPeriod']
      },
      set(period) {
        this.$store.commit('cart/setPeriod', period)
      }
    }
  },
  mounted() {
    setInterval(() => {
      this.promoBanner++
      if (this.promoBanner >= this.promoBanners.length) {
        this.promoBanner = 0;
      }
    }, 5000)
  }
}
</script>
<style>

.home {
  height: 100%;
  position: relative;
  z-index: 2;
  color: white;
  display: flex;
  flex-direction: column;
  justify-content: center;
  align-items: flex-start;
}

.campaign-details {
  margin-top: 2rem;
  display: inline-flex;
  padding: 0.45rem;
  color: white;
  max-width: 100%;
  width: 900px;
}

@media (min-width: 801px) {
  .campaign-details > div:not(:first-child) {
    margin-left: 2rem;
  }
}

@media (max-width: 800px) {
  .campaign-details {
    flex-direction: column;
  }

  .campaign-details > div:not(:first-child) {
    margin-top: 0.25rem;
  }
}

.search-button {
  align-self: center;
  position: relative;
  top: -3px;
}


.campaign-details span {
  font-weight: 600;
}

.padded-title h3::after {
  display: block;
  content: attr(title);
  font-weight: bold;
  height: 1px;
  color: transparent;
  overflow: hidden;
  visibility: hidden;
}

.padded-title h2, .padded-title h3 {
  font-size: min(60px, 8vw);
  line-height: 1.4;
  color: white;
  display: inline;
  padding: 0.45rem;

  box-decoration-break: clone;
  -webkit-box-decoration-break: clone;
}

.padded-title h3 {
  font-weight: 400;
  font-size: min(36px, 4.5vw);
}

.banner-fade-enter-active, .banner-fade-leave-active {
  transition: opacity .15s ease;
}

.banner-fade-enter, .banner-fade-leave-to /* .banner-fade-leave-active below version 2.1.8 */
{
  opacity: 0;
}

.campaign-area {
  flex: 0.7;
}
</style>
