<template>
  <input type="text" @keydown.enter="rename_item()" ref="new_name" />
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
      return this.$store.getters.get_selected_items[0];
    },
  },
  methods: {
    async rename_item() {
      this.selected_item = this.selected_item_getters;
      await axios.post(this.rename_api_url, {
        id: this.selected_item,
        new_name: this.$refs.new_name.value,
      });
      this.reload_itemlist();
    },
    async reload_itemlist() {
      this.$store.commit("rename_flag_mutation", {
        id: null,
      });
      await this.get_itemlist(this.get_path());
    },
  },
};
</script>
