<template>
  <v-container class="screens-container" :fluid="true">
    <v-dialog
        v-model="uploadVideoDialog"
        persistent
        max-width="500"
    >
      <v-card>
        <v-card-title class="text-h5">
          Upload your video first
        </v-card-title>
        <v-card-text>
          Before you add a screen to cart, we need to know your video details!
        </v-card-text>
        <v-card-actions>
          <v-spacer></v-spacer>
          <v-btn
              color="red darken-1"
              text
              @click="uploadVideoDialog = false"
          >
            Close
          </v-btn>
          <v-btn
              color="primary darken-1"
              text
              @click="$router.push({name: 'video'})"
          >
            Upload video
          </v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>
    <div class="screens">
      <div class="screens__list" v-if="screens.length >  0">
        <div class="screen" v-for="screen in screens" :key="screen.id" :data-screen="screen.id" :class="{'screen--highlighted': highlightedScreen === screen.id}">
          <div class="screen__image">
            <img :src="screen.photo"/>
          </div>
          <div class="screen__details">
            <div class="screen__title-container">
              <h3 class="screen__title">{{ screen.name }}</h3>
              <div class="d-flex align-center">
                <v-chip
                    class="ma-2 price-chip"
                    label
                    small
                >
                  <template v-if="!currentVideo">
                    Upload video to see price
                  </template>
                  <template v-else-if="!period.length">
                    Select period to see price
                  </template>
                  <template v-else-if="!getScreenPrice(screen.id)">
                    Unknown price
                  </template>
                  <template v-else>
                    $ {{ getScreenPrice(screen.id).toFixed(2) }} per day
                  </template>
                </v-chip>
                <div>
                  <v-btn
                      @click="addToCart(screen)"
                      icon
                      :color="isInCart(screen) ? 'green' : ''"
                  >
                    <v-icon>mdi-cart</v-icon>

                  </v-btn>
                </div>
                <!--
                <v-btn
                    small
                    color="primary"
                    dark v-if="!isInCart(screen)"
                    @click="addToCart(screen)"
                >
                  ADD to cart
                </v-btn>
                <div class="remove-from-cart" @click="removeFromCart(screen)" v-else>
                  <v-btn
                      small
                      color="error"
                      class="remove-from-cart__remove"
                  >
                    REMOVE FROM CART
                  </v-btn>
                  <v-btn
                      small
                      color="success"
                      class="remove-from-cart__info"
                  >
                    ADDED TO CART
                  </v-btn>
                </div>
                -->
              </div>
            </div>
            <div class="screen__parameters">{{ screen.address }}</div>
            <p class="screen__description">{{ screen.description }}</p>
          </div>
        </div>
      </div>
      <div class="screens__list" v-else>
        <div class="screen" v-for="i in 5">
          <v-skeleton-loader class="loader"
                             type="image"
          ></v-skeleton-loader>
        </div>
      </div>
      <div class="screens__map">
        <GmapMap
            :center="{lat:0, lng:0}"
            :zoom="7"
            map-type-id="terrain"
            class="map"
            ref="map"
        >
          <GmapMarker
              :key="screen.id"
              v-for="(screen, index) in screens"
              :position="getGmLatLng(screen.geolocation)"
              :clickable="true"
              :icon="markerIcon"
              :draggable="false"
              @click="highlightScreen(screen)"
          />
        </GmapMap>
      </div>
    </div>
  </v-container>
</template>
<script>
import {gmapApi} from 'vue2-google-maps'
import {mapGetters} from "vuex";

export default {
  data() {
    return {
      loading: false,
      selectPeriod: false,
      screens: [],
      uploadVideoDialog: false,
      highlightedScreen: null,
    }
  },
  computed: {
    ...mapGetters({
      currentVideo: 'video/getCurrentVideo',
      allScreens: 'screens/getAllScreens',
      cart: 'cart/getCart',
      period: 'cart/getPeriod',
      getScreenPrice: 'stats/getScreenPrice',
    }),
    google: gmapApi,
    markerIcon() {
      return {
        url: "/img/marker.png",
        scaledSize: {width: 40, height: 40}
      }
    }
  },
  async mounted() {
    if (!this.allScreens.length) {
      await this.$axios.get('screens').then(({data}) => {
        this.$store.commit('screens/setScreens', data)
      })
    }

    this.$store.dispatch('stats/getStats')
    this.screens = this.allScreens
    this.whenGoogleLoaded()
  },
  watch: {
    screens(screens) {
      setTimeout(() => {
        //   this.fitMapToScreens()
      })
    }
  },
  methods: {
    whenGoogleLoaded() {
      if (!this.google || !this.google.maps) {
        setTimeout(this.whenGoogleLoaded, 100)
      } else {
        this.fitMapToScreens()
      }
    },
    fitMapToScreens() {
      const bounds = new this.google.maps.LatLngBounds()
      for (let screen of this.screens) {
        bounds.extend(this.getGmLatLng(screen.geolocation))
      }

      setTimeout(()=>{
        this.$refs.map.fitBounds(bounds)
      })
    },
    getGmLatLng(geolocation) {
      if (!this.google) {
        return;
      }
      const position = geolocation.split(',').map(pos => parseFloat(pos.trim()))
      return new this.google.maps.LatLng(position[0], position[1]);
    },
    isInCart(screen) {
      return !!this.cart.find(cartScreen => screen.id === cartScreen.id)
    },
    addToCart(screen) {
      if(this.isInCart(screen)) {
        this.removeFromCart(screen)
        return;
      }
      if(!this.currentVideo) {
        this.uploadVideoDialog = true;
        return;
      }
      this.$store.commit('cart/addToCart', screen)
    },
    removeFromCart(screen) {
      this.$store.commit('cart/removeFromCart', screen)
    },
    highlightScreen(screen) {
      const screenElement = document.querySelector(`[data-screen=${screen.id}]`);
      screenElement.scrollIntoView({behavior: "smooth", block: "center", inline: "nearest"})

      this.highlightedScreen = screen.id

    }
  }
}
</script>
<style lang="scss" scoped>
.screens-container {
  display: flex;
  align-items: center;
  height: 100%;
  max-width: 1800px;
  margin: 0 auto;
}

.screens {
  display: flex;
  width: 100%;
  height: 90vh;
  border-radius: 10px;
  overflow: hidden;

  @media (max-width: 800px) {
    flex-direction: column;
  }
}

.screens__list, .screens__map {
  width: 100%;
  height: 100%;
}

.screens__list {
  overflow-y: auto;

  &::-webkit-scrollbar {
    width: 5px;
  }

  &::-webkit-scrollbar-track {
    background: #f1f1f1;


  }

  &::-webkit-scrollbar-thumb {
    background: #d2d2d2;
    border-radius: 15px;

  }

  &::-webkit-scrollbar-thumb:hover {
    background: #555;
  }
}

.screens__map {
  /* border-top-right-radius: 30px;
   border-bottom-right-radius: 30px;
   overflow: hidden;*/
}

.map {
  height: 100%;
  width: 100%;
}

.screen {
  display: flex;
  background-color: #f3f3f3;

  transition: 0.4s all;

  &--highlighted {
    background-color: #cce9ff  !important;
  }

  @media (max-width: 800px) {
    font-size: 13px;
  }

  &__image {
    width: 12vw;
    height: 12vw;
    flex-shrink: 0;

    img {
      width: 100%;
      height: 100%;
      object-fit: cover;
    }

    @media (max-width: 1260px) {
      align-self: center;
      width: 8vw;
      height:8vw;
      margin-left: 2vw;
      box-shadow: rgba(0, 0, 0, 0.16) 0px 10px 36px 0px, rgba(0, 0, 0, 0.06) 0px 0px 0px 1px;
    }
  }

  &__details {
    width: 100%;
    padding: 20px 20px 0;
    color: black;
  }

  &__title-container {
    display: flex;
    justify-content: space-between;
    align-items: center;
    width: 100%;
  }

  &__title {
    font-size: min(1.4vw, 25px);

    @media (max-width: 800px){
      font-size: min(6vw, 12px);
    }
  }

  &__parameters {
    font-size: 13px;
    color: #0d41bf;
    margin: 3px 0 6px;
    font-weight: 600;

    @include media-down(sm) {
      font-size: 10px;
    }
  }

  &__description {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
    text-overflow: ellipsis;

    @media (min-width: 1400px) {
      -webkit-line-clamp: 3;
    }
  }

  &:nth-child(odd) {
    background-color: rgba(255, 255, 255, 1);
  }
}

.loader {
  width: 100%;
}

.remove-from-cart {
  display: contents;

  &__remove {
    display: none;
  }

  &__remove, &__info {
    min-width: 170px !important;
  }

  &:hover &__remove {
    display: unset;
  }

  &:hover &__info {
    display: none;
  }
}

.price-chip {
  @include media-down(sm) {
    font-size: 10px;
    height: 20px;
  }
}
</style>
