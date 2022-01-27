<template>
  <v-app class="ledradar-app">
    <transition name="fade">
      <LoadingOverlay v-if="fetchingStats">
        Calculating screens prices<br/>based on your video details
      </LoadingOverlay>
    </transition>
    <v-app-bar
        :color="appBarColor"
        dense
        dark
        app clipped-right
        :class="{'elevation-0': appBarColor === 'transparent'}"
        clipped-left
    >
      <v-app-bar-nav-icon @click="menu = !menu"></v-app-bar-nav-icon>
      <v-toolbar-title class="title-bar" @click="$router.push('/')">
        <img src="/img/redled.png" alt="REDLED"/>
        <strong>LED</strong>Radar
      </v-toolbar-title>

      <div class="video-status" :class="{'video-status--active': currentVideo }" @click="$router.push({name: 'video'})">
        <template v-if="!currentVideo">
          No video selected - click to upload
        </template>
        <template v-else>
          Your video: {{ currentVideo.name }} ({{ currentVideo.length.toFixed(2) }}s)
        </template>
      </div>

      <v-spacer></v-spacer>

      <v-btn icon @click="$router.push({name: 'screens'}); ">
        <v-icon>mdi-magnify</v-icon>
      </v-btn>
      <v-btn icon @click="cart = !cart">
        <v-badge
            overlap
            :color="cartSize > 0 ? 'green': 'red'"
            offset-x="12"
            offset-y="10"
            :content="`${cartSize}`">
          <v-icon>mdi-cart</v-icon>
        </v-badge>
      </v-btn>
    </v-app-bar>
    <v-navigation-drawer
        v-model="menu"
        fixed
        temporary
        class="menu-drawer"
    >
      <v-list nav>
        <v-list-item
            v-for="item in menuItems"
            :key="item.title"
            :to="item.to"
            link
        >
          <v-list-item-icon>
            <v-icon>{{ item.icon }}</v-icon>
          </v-list-item-icon>

          <v-list-item-content>
            <v-list-item-title>{{ item.title }}</v-list-item-title>
          </v-list-item-content>
        </v-list-item>
      </v-list>
    </v-navigation-drawer>
    <v-navigation-drawer
        v-model="cart"
        right
        fixed
        temporary
        class="cart-container"
        width="100%"

    >
      <div class="empty-cart d-flex flex-column" v-if="cartSize === 0 || !currentVideo">
        <v-icon color="black" style="font-size:120px">
          mdi-cart-remove
        </v-icon>
        <span>Your cart is empty</span>
      </div>
      <MiniCart @close="cart = false" v-else/>
    </v-navigation-drawer>
    <v-main>
      <router-view></router-view>
    </v-main>
    <v-footer
        v-if="false"
        dark
        padless
    >
      <v-card
          class="flex"
          color="transparent"
          flat
          tile
      >

        <v-card-text class="py-2 white--text text-center">
          {{ new Date().getFullYear() }} — <strong>LEDRadar</strong>
        </v-card-text>
      </v-card>
    </v-footer>
    <v-btn
        class="go-up"
        :class="{'go-up--hidden': !canScrollUp}"
        icon
        @click="goUp"
    >
      <v-icon large color="black">
        mdi-chevron-up-circle
      </v-icon>
    </v-btn>
  </v-app>
</template>

<script>
import {mapGetters} from "vuex";
import MiniCart from './blocks/MiniCart'
import LoadingOverlay from './blocks/LoadingOverlay'

export default {
  components: {
    MiniCart,
    LoadingOverlay
  },
  data: () => ({
    menu: false,
    cart: false,
    menuItems: [
      {title: 'Upload your video', icon: 'mdi-file-video', to: 'video'},
      {title: 'Browse screens', icon: 'mdi-monitor', to: 'screens'},
      {title: 'Help', icon: 'mdi-help-box'},
    ],
    canScrollUp: false,
  }),
  mounted() {
    this.checkCanScrollUp()
    window.addEventListener('scroll', this.checkCanScrollUp)
  },
  methods: {
    goUp() {
      window.scrollTo({top: 0, behavior: 'smooth'});
    },
    checkCanScrollUp() {
      this.canScrollUp = window.scrollY > 0
    }
  },
  computed: {
    ...mapGetters({
      currentVideo: 'video/getCurrentVideo',
      fetchingStats: 'stats/getFetchingStatsStatus',
    }),
    appBarColor() {
      return 'transparent'
      if (this.cart || this.menu || this.canScrollUp) {
        return 'black lighten-5'
      }
      return 'transparent'
    },
    cartSize() {
      return this.$store.state.cart.screens.length;
    }
  }
}
</script>
<style lang="scss">
html {
  overflow-y: auto !important;
}

.title-bar {
  display: flex;
  align-items: center;
  cursor: pointer;
}

.title-bar img {
  object-fit: cover;
  height: 25px;
  margin-right: 0.5em;
}

.empty-cart {
  display: flex;
  align-items: center;
  text-align: center;
  justify-content: center;
  width: 100%;
  height: 100%;
  color: black;
  font-size: 23px;
}

.video-status {
  position: absolute;
  left: 50%;
  transform: translateX(-50%);
  font-size: 0.8rem;
  text-shadow: 2px 2px #353535;
  font-weight: 600;
  cursor: pointer;
  display: inline-block;
  background-color: #e30000;
  border-radius: 5px;
  padding: 5px;
}

.video-status--active {
  background-color: green;
}

@media (max-width: 800px) {
  .video-status {
    display: none;
  }
}

.go-up {
  z-index: 9999;
  position: fixed;
  bottom: 24px;
  right: 15px;
}

.go-up--hidden {
  opacity: 0;
  pointer-events: none;
}

.ledradar-app.v-application {
  background-color: #121212;
  // background: linear-gradient(135deg, rgba(0, 0, 0, 0.35), black), url(/img/home.jpg);
  background: linear-gradient(
          135deg, #003ba27a, #001335), url(/img/home.jpg);
  background-size: cover;
  background-position: center;
  color: #fff;
}

.v-application, body {
  overflow: hidden !important;
}

.menu-drawer {
  border-bottom-right-radius: 15px;
  height: unset !important;
}

@media (min-width: 801px) {
  .menu-drawer {
    border-top-right-radius: 15px;
    top: 50px !important;
  }
}

.cart-container {
  max-width: 400px;
}

.fade-enter, .fade-leave-to {
  opacity: 0;
}

.fade-leave-active {
  transition: 0.5s all;
}
</style>
