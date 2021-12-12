<template>
  <div id="app">
    <path-view />
    <toolbar />
    <list-view />
  </div>
</template>

<script>
import store from "./store";

import PathView from "./components/PathViewComponent.vue";
import Toolbar from "./components/tools/ToolbarComponent.vue";
import ListView from "./components/ContentsComponent.vue";

export default {
  name: "App",
  store,
  components: {
    PathView,
    Toolbar,
    ListView,
  },
};
</script>

<style></style>
