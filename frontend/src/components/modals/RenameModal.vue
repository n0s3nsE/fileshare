<template>
  <div class="modal-window rename-modal">
    <div class="modal-main">
      <input
        type="text"
        placeholder="新たなファイル名"
        @keydown.enter="renameItem"
        ref="newName"
      />
    </div>
    <div class="modal-ctl">
      <button @click="renameItem">
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
      isLoading: false,
    };
  },
  mixins: [Mixin],
  computed: {
    selectedItemGetters() {
      return this.$store.getters.getSelectedItems[0];
    },
  },
  methods: {
    async renameItem() {
      this.selectedItem = this.selectedItemGetters;
      this.isLoading = true;
      await axios
        .post(
          this.renameAPI,
          {
            id: this.selectedItem,
            new_name: this.$refs.newName.value,
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
