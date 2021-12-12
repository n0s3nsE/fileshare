<template>
  <div class="toolbar-delete">
    <button @click="delete_items()">削除</button>
  </div>
</template>
<script>
export default {
  data() {
    return {
      selected_items: [],
    };
  },
  computed: {
    selected_items_getters() {
      return this.$store.getters.get_selected_items;
    },
  },
  methods: {
    delete_items() {
      this.selected_items = this.selected_items_getters;
      //api delete method
      //api get_itemlist
    },
  },
};
</script>
