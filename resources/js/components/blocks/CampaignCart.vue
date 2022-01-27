<template>
  <div class="cart" v-if="campaign.code">
    <div class="cart-items">
      <div v-for="(screen, screenId) in screens" class="cart-item">
        <div class="cart-item__image">
          <img :src="screen.screen_details.photo" :alt="screen.screen_details.name">
        </div>
        <div class="cart-item__description">
          <div class="cart-item__name">
            {{ screen.screen_details.name }}
          </div>
          <div class="cart-item__address">
            {{ screen.screen_details.address }}
          </div>
          <div class="cart-item__stats">
            <span class="price pr-2 mr-2 ">${{ screen.price_per_day.toFixed(2) }}/day</span>
            <span class="replays">Estimated replays per day: {{ screen.replays }}</span>
          </div>
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
            Length: {{ parseInt(video.length)}}s
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
<script>
import {mapGetters, mapMutations} from "vuex";

export default {
  props: {
    campaign: {
      required: false,
      type: Object,
      default: () => {}
    }
  },
  data() {
    return {}
  },
  computed: {
    screens () {
      return this.campaign.screens ?? {}
    },
    video() {
      return this.campaign.video_details ?? {}
    }
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
