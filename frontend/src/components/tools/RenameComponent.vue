<template>
  <div>
    <button @click="rename_item()">名前を変更</button>
  </div>
</template>
<script>
import axios from "axios";
import Mixin from "../../mixin/mixin";

export default {
  data() {
    return {
      selected_item: null,
      rename_api_url: "http://127.0.0.1:8000/api/rename",
    };
  },
  mixins: [Mixin],
  computed: {
    selected_item_getters() {
      return this.$store.getters.get_selected_items;
    },
  },
  methods: {
    async rename_item() {
      this.selected_item = this.selected_item_getters[0];
      await axios.post(this.rename_api_url, {
        id: this.selected_item,
        new_name: "new-test",
      });
      this.reload_itemlist();
    },
    async reload_itemlist() {
      await this.get_itemlist(this.get_path() + "/");
    },
  },
};
</script>
