<template>
  <div class="cart">
    <div class="cart-items">
      <div v-for="screen in cart" class="cart-item">
        <div class="cart-item__image">
          <img :src="screen.photo" :alt="screen.name">
        </div>
        <div class="cart-item__description">
          <div class="cart-item__name">
            {{ screen.name }}
          </div>
          <div class="cart-item__address">
            {{ screen.address }}
          </div>
          <div class="cart-item__stats" v-if="getScreenPrice(screen.id)">
            <span class="price pr-2 mr-2 ">${{ getScreenPrice(screen.id).toFixed(2) }}/day</span>
            <span class="replays">Estimated replays per day: {{ getDailyReplays(screen.id) }}</span>
          </div>
        </div>
        <div class="cart-item__action">
          <v-hover v-slot="{ hover }">
            <v-icon class="cart-item__icon"
                    :color="hover ? 'red darken-2' : 'grey'"
                    @click="removeFromCart(screen)"
            >
              mdi-delete
            </v-icon>
          </v-hover>
        </div>
      </div>
    </div>
    <div class="cart-video">
      <div class="cart-video__header">
        Your video details
      </div>
      <div class="cart-video__details">
        <div class="cart-video__image">
          <img :src="'/storage/'+video.thumbnail_path" alt="Video"/>
        </div>
        <div>
          <div class="cart-video__title">
            {{ video.name }}
          </div>
          <div class="cart-video__length">
            Length: {{ video.length.toFixed(2) }}s
          </div>
        </div>
      </div>
    </div>
    <div class="cart-summary" v-if="!isMinified">
      <router-link :to="{name: 'checkout'}" class="cart-summary__proceed" tag="div" @click.native="$emit('close')">
        <v-btn
            width="100%"
            height="48px"
            depressed
            color="success"
        >
          Proceed to checkout
        </v-btn>
      </router-link>
    </div>
  </div>
</template>
<script>
import {mapGetters, mapMutations} from "vuex";

export default {
  props: {
    isMinified: {
      required: false,
      default: false,
      type: Boolean,
    },
  },
  data() {
    return {}
  },
  computed: {
    ...mapGetters({
      cart: 'cart/getCart',
      video: 'video/getCurrentVideo',
      getScreenPrice: 'stats/getScreenPrice',
      getDailyReplays: 'stats/getEstimatedReplays',
    }),
  },
  methods: {
    ...mapMutations({
      removeFromCart: 'cart/removeFromCart'
    })
  }
}
</script>
<style lang="scss" scoped>
.cart {
  display: flex;
  height: 100%;
  flex-direction: column;
  color: #000000;
}

.cart-item {
  display: flex;
  padding: 10px;
  align-items: center;
  transition: 0.25s all;

  &:hover {
    background-color: #f3f3f3;
  }

  &__name {
    font-weight: 600;
    font-size: 14px;
  }

  &__address {
    font-weight: 300;
    font-size: 11px;
  }

  &__stats {
    font-weight: 500;
    font-size: 11px;
  }

  &__action {
    margin-left: auto;
  }

  &__icon {
    cursor: pointer;
    transition: 0.25s;
  }

  &__image {
    width: 48px;
    height: 48px;
    flex-shrink: 0;
    box-shadow: rgba(0, 0, 0, 0.16) 0px 10px 36px 0px, rgba(0, 0, 0, 0.06) 0px 0px 0px 1px;
    border-radius: 8px;
    overflow: hidden;
    margin-right: 8px;

    img {
      width: 100%;
      height: 100%;
      object-fit: cover;
    }
  }
}

.cart-video {
  padding: 10px;
  border-top: 1px solid #dcdcdc;

  &__header {
    font-size: 18px;
    font-weight: 600;
    margin-bottom: 5px;
    text-align: center;
  }

  &__details {
    display: flex;
    align-items: center;
  }

  &__title {
    word-break: break-all;
    font-weight: 600;
  }

  &__length {
    font-size: 12px;
  }

  &__image {
    width: 72px;
    height: 72px;
    flex-shrink: 0;
    box-shadow: rgba(0, 0, 0, 0.16) 0px 10px 36px 0px, rgba(0, 0, 0, 0.06) 0px 0px 0px 1px;
    border-radius: 8px;
    overflow: hidden;
    margin-right: 10px;

    img {
      width: 100%;
      height: 100%;
      object-fit: cover;
    }
  }
}

.cart-summary {
  margin-top: auto;

  &__proceed {
    width: 100%;
    padding: 10px;
  }
}

.price {
  color: green;
  &:not(:last-child) {
    border-right: 1px solid #b1b1b1;
  }
}
</style>
