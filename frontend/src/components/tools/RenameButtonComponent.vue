<template>
  <div class="toolbar-rename">
    <button @click="open_textbox()">名前を変更</button>
  </div>
</template>

<script>
export default {
  computed: {
    selected_item_getters() {
      return this.$store.getters.get_selected_items[0];
    },
  },
  methods: {
    open_textbox() {
      this.$store.commit("rename_flag_mutation", {
        id: this.selected_item_getters,
      });
    },
  },
};
</script>

<style scoped>
.toolbar-rename {
  margin: 0 4px;
}
</style>
