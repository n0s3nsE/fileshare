<template>
  <div id="app">
    <createfolder-modal v-if="createfoldermodal_status" />
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
import CreatefolderModal from "./components/CreateFolderModal.vue";

export default {
  name: "App",
  store,
  components: {
    PathView,
    Toolbar,
    ListView,
    CreatefolderModal,
  },
  data() {
    return {
      createfoldermodal_status: false,
    };
  },
  computed: {
    createfoldermodal_getters() {
      return this.$store.getters.get_createfoldermodal_status;
    },
  },
  watch: {
    createfoldermodal_getters() {
      this.createfoldermodal_status = this.createfoldermodal_getters;
      console.log(this.createfoldermodal_status);
    },
  },
};
</script>

<style>
@import url("https://fonts.googleapis.com/css2?family=Open+Sans:wght@300&family=Roboto:wght@100&display=swap");
#app {
  font-family: "Roboto";

  padding: 0 80px;
}
</style>
