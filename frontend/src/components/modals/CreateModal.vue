<template>
  <div class="modal-window create-modal">
    <div class="modal-main">
      <input
        type="text"
        placeholder="フォルダ名を入力"
        v-model="newFolderName"
        @keydown.enter="createFolder"
      />
    </div>
    <div class="modal-ctl">
      <button @click="createFolder">作成</button>
      <button @click="closeModal">キャンセル</button>
    </div>
  </div>
</template>

<script>
import axios from "axios";
import Mixin from "../../mixin/mixin";

export default {
  data() {
    return {
      newFolderName: "",
      currentPath: "",
      createAPI: process.env.VUE_APP_API_BASE_URL_DEV + "/create",
    };
  },
  mixins: [Mixin],
  mounted() {
    this.currentPath = this.getPath();
  },
  methods: {
    closeModal() {
      this.$emit("closeModal");
      this.newFolderName = "";
    },
    async createFolder() {
      await axios
        .post(this.createAPI, {
          new_folder_name: this.newFolderName,
          path: this.currentPath,
        })
        .then((response) => {
          this.addNotification(response.status, "create");
        })
        .catch((error) => {
          this.addNotification(
            error.response.status,
            "create",
            error.response.data.detail
          );
        });
      this.getItemList(this.currentPath);
      this.closeModal();
    },
  },
};
</script>
<style>
@import "../../css/Modal.css";
</style>
