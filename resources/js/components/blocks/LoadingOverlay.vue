<template>
  <div class="loading-overlay">
    <slot></slot>
    <div class="bar">
      <v-progress-linear
          color="white accent-4"
          indeterminate
          rounded
          height="6"
      ></v-progress-linear>
    </div>
  </div>
</template>
<script>

export default {
  props: {},
  data() {
    return {}
  },
  computed: {},
  methods: {},
  mounted() {
    document.body.setAttribute('style','overflow:hidden;');

  },
  beforeDestroy() {
    setTimeout(function(){document.body.setAttribute('style','overflow:visible;');}, 500);

  }
}
</script>
<style lang="scss" scoped>
.loading-overlay {
  z-index: 999;
  position: fixed;
  left: 0;
  top: 0;
  right: 0;
  bottom: 0;
  background-color: rgba(20, 59, 125, 0.9);
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 24px;
  flex-direction: column;
  text-align: center;
}

.bar {
  margin-top: 24px;
  max-width: 280px;
  width: 100%;
  padding: 0 18px;
}
</style>
