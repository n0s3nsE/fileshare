<template>
  <div class="modal-window rename-modal">
    <div class="modal-main">
      <input
        type="text"
        placeholder="新たなファイル名"
        @keydown.enter="rename_item"
        ref="new_name"
      />
    </div>
    <div class="modal-ctl">
      <button @click="rename_item">保存</button>
      <button @click="close_modal">キャンセル</button>
    </div>
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
      return this.$store.getters.get_selected_items[0];
    },
  },
  methods: {
    async rename_item() {
      this.selected_item = this.selected_item_getters;
      await axios
        .post(this.rename_api_url, {
          id: this.selected_item,
          new_name: this.$refs.new_name.value,
        })
        .then((response) => {
          this.add_notification(response.status, "rename");
        })
        .catch((error) => {
          this.add_notification(
            error.response.status,
            "rename",
            error.response.data.detail
          );
        });
      this.close_modal();
      this.reload_itemlist();
    },
    reload_itemlist() {
      this.get_itemlist(this.get_path());
    },
    close_modal() {
      this.$emit("close_modal");
    },
  },
};
</script>
<style>
@import "../../css/Modal.css";
</style>
