<template>
  <div class="modal-window delete-modal">
    <div class="modal-main">
      <p>削除しますか？</p>
    </div>
    <div class="modal-ctl">
      <button @click="delete_items">削除</button>
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
    this.selected_items = this.selected_items_getters;
  },
  methods: {
    async delete_items() {
      await axios
        .post(this.delete_api_url, {
          delete_items: this.selected_items,
        })
        .then((response) => {
          this.add_notification(response.status, "削除成功");
        })
        .catch((error) => {
          error.response.data.detail.map((d) => {
            this.add_notification(error.response.status, d);
          });
        });
      this.reload_itemlist();
    },
    reload_itemlist() {
      this.close_modal();
      this.get_itemlist(this.current_path);
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
