<template>
  <div class="toolbar-delete">
    <button @click="delete_items()">削除</button>
  </div>
</template>
<script>
import axios from "axios";
import Mixin from "../../mixin/mixin";

export default {
  data() {
    return {
      selected_items: [],
      current_path: "",
      delete_api_url: "http://127.0.0.1:8000/api/delete",
    };
  },
  mixins: [Mixin],
  computed: {
    selected_items_getters() {
      return this.$store.getters.get_selected_items;
    },
  },
  mounted() {
    this.current_path = this.get_path();
  },
  methods: {
    async delete_items() {
      this.selected_items = this.selected_items_getters;
      await axios.post(this.delete_api_url, {
        delete_items: this.selected_items,
      });
      this.reload_itemlist();
    },
    async reload_itemlist() {
      await this.get_itemlist(this.current_path);
    },
  },
};
</script>

<style scoped>
.toolbar-delete {
  margin: 0 4px;
}
</style>
