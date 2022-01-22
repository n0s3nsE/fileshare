<template>
  <div id="app">
    <div v-if="!viewer_param">
      <path-view />
      <div class="contents">
        <toolbar />
        <item-list />
      </div>
    </div>
    <div v-else id="viewer">
      <viewer :param="viewer_param" />
    </div>
  </div>
</template>

<script>
import store from "./store";

import PathView from "./components/PathViewComponent.vue";
import Toolbar from "./components/tools/ToolbarComponent.vue";
import ItemList from "./components/ItemList/ItemListComponent.vue";
import Viewer from "./components/Viewer/Viewer.vue";

export default {
  name: "App",
  store,
  components: {
    PathView,
    Toolbar,
    ItemList,
    Viewer,
  },
  data() {
    return {
      viewer_param: "",
    };
  },
  mounted() {
    this.viewer_param = window.location.search.substring(1);
  },
};
</script>

<style>
@import url("https://fonts.googleapis.com/css2?family=Open+Sans&family=Roboto:wght@100&display=swap");
body {
  margin: 0;
}
#app {
  width: 100%;
  height: 100%;
  font-family: "Open Sans", sans-serif;
}
.contents {
  margin: 0 10%;
  position: relative;
}
</style>
