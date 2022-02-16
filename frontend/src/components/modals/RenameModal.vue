<template>
  <div class="modal-window rename-modal">
    <div class="modal-main">
      <input
        type="text"
        :placeholder="placeholderMsg"
        v-model="newName"
        @change="validateText"
      />
    </div>
    <div class="modal-ctl">
      <button @click="renameItem" :disabled="isLoading || validateError">
        <vue-loading
          v-if="isLoading"
          type="spin"
          color="#333"
          :size="{ width: '22px', height: '22px' }"
        />
        <span v-else>保存</span>
      </button>
      <button @click="closeModal">キャンセル</button>
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
      selectedItem: null,
      renameAPI: process.env.VUE_APP_API_BASE_URL + "/rename",
      newName: "",
      isLoading: false,
      placeholderMsg: "",
      validateError: true,
      isFolder: false,
    };
  },
  mixins: [Mixin],
  computed: {
    selectedItemGetters() {
      return this.$store.getters.getSelectedItems[0];
    },
  },
  mounted() {
    this.contentDetail();
  },
  methods: {
    validateText() {
      if (this.isFolder) {
        this.newName.length > 0 && this.newName.length <= 32
          ? (this.validateError = false)
          : (this.validateError = true);
      } else {
        this.newName.length > 0
          ? (this.validateError = false)
          : (this.validateError = true);
      }
    },
    async contentDetail() {
      const gd = await this.getDetail(this.selectedItemGetters);

      if (gd.isfolder) {
        this.placeholderMsg = "新しいフォルダ名(1~32文字)";
        this.isFolder = true;
      } else {
        this.placeholderMsg = "新しいファイル名";
        this.isFolder = false;
      }
    },
    async renameItem() {
      this.selectedItem = this.selectedItemGetters;
      this.isLoading = true;
      await axios
        .post(
          this.renameAPI,
          {
            id: this.selectedItem,
            new_name: this.newName,
          },
          this.axiosConfig
        )
        .then((response) => {
          this.addNotification(response.status, "rename");
          this.isLoading = false;
        })
        .catch((error) => {
          if (error.code === "ECONNABORTED")
            this.addNotification(408, "rename", "timeout");
          else {
            this.addNotification(
              error.response.status,
              "rename",
              error.response.data.detail
            );
          }
          this.isLoading = false;
        });
      this.closeModal();
      this.reloadItemlist();
    },
    reloadItemlist() {
      this.getItemList(this.getPath());
    },
    closeModal() {
      this.$emit("closeModal");
    },
  },
};
</script>
<style>
@import "../../css/Modal.css";
</style>
