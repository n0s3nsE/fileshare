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
      <button @click="createFolder" :disabled="isLoading">
        <vue-loading
          v-if="isLoading"
          type="spin"
          color="#333"
          :size="{ width: '22px', height: '22px' }"
        />
        <span v-else>作成</span>
      </button>
      <button @click="closeModal" :disabled="isLoading">キャンセル</button>
    </div>
  </div>
</template>

<script>
import axios from "axios";
import Mixin from "../../mixin/mixin";
import { VueLoading } from "vue-loading-template";

export default {
  components: {
    VueLoading,
  },
  data() {
    return {
      newFolderName: "",
      currentPath: "",
      createAPI: process.env.VUE_APP_API_BASE_URL_DEV + "/create",
      isLoading: false,
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
      this.isLoading = true;
      await axios
        .post(
          this.createAPI,
          {
            new_folder_name: this.newFolderName,
            path: this.currentPath,
          },
          this.axiosConfig
        )
        .then((response) => {
          this.addNotification(response.status, "create");
          this.isLoading = false;
        })
        .catch((error) => {
          if (error.code === "ECONNABORTED")
            this.addNotification(408, "create", "timeout.");
          else {
            this.addNotification(
              error.response.status,
              "create",
              error.response.data.detail
            );
          }
          this.isLoading = false;
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
