<template>
  <div id="app">
    <path-view />
    <list-view />
  </div>
</template>

<script>
import PathView from './components/PathViewComponent.vue';
import ListView from './components/ContentsComponent.vue';

export default {
  name: 'App',
  components: {
    PathView,
    ListView,
  },
};
</script>

<style></style>
