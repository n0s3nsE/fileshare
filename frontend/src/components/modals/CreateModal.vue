<template>
  <div class="modal-window create-modal">
    <div class="modal-main">
      <input
        type="text"
        placeholder="フォルダ名を入力"
        v-model="new_folder_name"
        @keydown.enter="create_folder"
      />
    </div>
    <div class="modal-ctl">
      <button @click="create_folder">作成</button>
      <button @click="close_modal">キャンセル</button>
    </div>
  </div>
</template>

<script>
import axios from "axios";
import Mixin from "../../mixin/mixin";

export default {
  props: ["prop"],
  data() {
    return {
      new_folder_name: "",
      current_path: "",
      create_api_url: "http://127.0.0.1:8000/api/create",
    };
  },
  mixins: [Mixin],
  mounted() {
    this.current_path = this.get_path();
  },
  methods: {
    close_modal() {
      this.$emit("close_modal");
      this.new_folder_name = "";
    },
    async create_folder() {
      await axios.post(this.create_api_url, {
        new_folder_name: this.new_folder_name,
        path: this.current_path,
      });
      await this.get_itemlist(this.current_path);
      this.close_modal();
    },
  },
};
</script>
<style>
@import "../../css/Modal.css";
</style>
