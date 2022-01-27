<template>
  <v-container>
    <v-dialog
        v-model="dialog"
        persistent
        max-width="500"
    >
      <v-card>
        <v-card-title v-html="dialogText">
        </v-card-title>
        <v-card-actions>
          <v-spacer></v-spacer>
          <v-btn
              color="primary darken-1"
              text
              @click="dialog = false"
          >
            Close
          </v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>
    <div class="current-video" v-if="currentVideo">
      <img :src="'/storage/'+currentVideo.thumbnail_path" :alt="currentVideo.name"/>
      <div>
        <h2>{{currentVideo.name}}</h2>
        <h3 class="mb-1 font-weight-regular">Length: <span class="font-weight-bold">{{ currentVideo.length.toFixed(2) }} seconds</span></h3>
        <v-dialog
            ref="period"
            v-model="selectPeriod"
            :return-value.sync="period"
            persistent
            width="290px"
        >
          <template v-slot:activator="{ on, attrs }">
            <h3 class="mb-5 font-weight-regular">Campaign period: <span v-on="on" v-bind="attrs" class="font-weight-bold text-decoration-underline">{{periodText || 'not selected'}}</span></h3>
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
        <v-btn x-large class="mr-2 mb-4 mb-md-0"
               color="success" @click="$router.push({name: 'screens'})">
          Select screens
        </v-btn>
        <v-btn x-large @click="removeVideo" color="error">
          Upload a different video
        </v-btn>
      </div>
    </div>
    <div class="upload-video" v-else>
      <input type="file" ref="file" class="d-none"/>
      <template v-if="name === null">
        <h2 class="mb-6">No video selected</h2>
        <v-btn x-large @click="selectFile">
          Click to select a video
        </v-btn>
      </template>
      <div v-show="name !== null">
        <h1 class="mb-5">You're about to advertise a following video</h1>
        <div class="selected-video">
          <video ref="video"></video>
          <div>
            <h2>{{ name }}</h2>
            <h3 class="mb-3">Length: {{ duration }} seconds</h3>
            <v-btn x-large class="mr-2"
                   color="success" @click="upload" :loading="loading">
              Confirm
            </v-btn>
            <v-btn x-large @click="name = null" color="error" v-if="!loading">
              Cancel
            </v-btn>
          </div>
        </div>
      </div>
    </div>
  </v-container>
</template>
<script>

import {mapGetters} from "vuex";

export default {
  data() {
    return {
      duration: null,
      name: null,
      dialog: false,
      dialogText: null,
      loading: false,
      selectPeriod: false,
    }
  },
  computed: {
    ...mapGetters({
      currentVideo: 'video/getCurrentVideo',
    }),
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
    this.mountFileChangeEventListener()
  },
  watch: {
    currentVideo(newVal, oldVal) {
      if(oldVal && !newVal) {
        this.mountFileChangeEventListener()
      }
    }
  },
  methods: {
    mountFileChangeEventListener() {
      setTimeout(() => {
        const video = this.$refs.video;
        const vm = this;

        if(!this.$refs.file) {
          return;
        }

        this.$refs.file.addEventListener('change', function () {
          video.src = URL.createObjectURL(this.files[0]) + "#t=5";

          if (!vm.isTypeSupported(this.files[0].type)) {
            vm.name = null;
            vm.dialog = true;
            vm.dialogText = 'Sorry - we currently support following formats:<br/>- mp4<br/>- webm';
            return;
          }

          vm.name = this.files[0].name
          video.ondurationchange = function () {
            vm.duration = this.duration;
          };
        });
      })
    },
    isTypeSupported(type) {
      return ['video/webm', 'video/mp4'].includes(type)
    },
    upload() {
      const formData = new FormData();
      const file = this.$refs.file
      formData.append("video", file.files[0]);
      this.loading = true;
      this.$axios.post('video', formData, {
        headers: {
          'Content-Type': 'multipart/form-data'
        }
      }).then(({data}) => {
        this.$store.commit('video/setCurrent', data.data)
      }).finally(()=>{
        this.loading = false
      })
    },
    removeVideo() {
      this.name = null;
      this.$store.commit('video/setCurrent', null)
    },
    selectFile() {
      this.$refs.file.value = null;
      this.$refs.file.click();
    }
  }
}
</script>
<style lang="scss" scoped>
h2, h3 {
  word-break: break-all;
}

h2 {
  @include media-down(sm) {
    font-size: 17px;
    margin-top: 20px;
  }
}

h3{
  @include media-down(sm) {
    font-size: 15px;
  }
}

.upload-video, .current-video {
  padding-top: 5vw;
}

.selected-video, .current-video {
  display: flex;

  @include media-down(sm) {
    flex-direction: column;
  }

  video, img {
    width: 15vw;
    height: 11vw;
    object-fit: cover;
    margin-right: 25px;
  }
}
</style>
