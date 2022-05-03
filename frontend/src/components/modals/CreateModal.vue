<template>
  <div class="modal-window create-modal">
    <div class="modal-main">
      <input
        type="text"
        placeholder="フォルダ名を入力(1~32文字)"
        v-model="newFolderName"
        @change="validateText"
      />
    </div>
    <div class="modal-ctl">
      <button @click="createFolder" :disabled="isLoading || validateError">
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
import Mixin from "../../mixin/mixin";
import apiMixin from "../../mixin/api";
import { VueLoading } from "vue-loading-template";

export default {
  components: {
    VueLoading,
  },
  data() {
    return {
      newFolderName: "",
      currentPath: "",
      isLoading: false,
      validateError: true,
    };
  },
  mixins: [Mixin, apiMixin],
  mounted() {
    this.currentPath = this.getPath();
  },
  methods: {
    closeModal() {
      this.$emit("closeModal");
      this.newFolderName = "";
    },
    validateText() {
      this.newFolderName.length > 0 && this.newFolderName.length <= 32
        ? (this.validateError = false)
        : (this.validateError = true);
    },
    async createFolder() {
      this.validateError = false;
      this.isLoading = true;
      await this.createFolderAPI(this.newFolderName, this.currentPath)
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
