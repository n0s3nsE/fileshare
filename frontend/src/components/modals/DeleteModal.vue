<template>
  <div class="modal-window delete-modal">
    <div class="modal-main">
      <p>削除しますか？</p>
    </div>
    <div class="modal-ctl">
      <button @click="deleteItems">削除</button>
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
      selectedItems: [],
      currentPath: "",
      deleteAPI: process.env.VUE_APP_API_BASE_URL_DEV + "/delete",
    };
  },
  mixins: [Mixin],
  computed: {
    selectedItemsGetters() {
      return this.$store.getters.getSelectedItems;
    },
  },
  mounted() {
    this.currentPath = this.getPath();
    this.selectedItems = this.selectedItemsGetters;
  },
  methods: {
    async deleteItems() {
      await axios
        .post(
          this.deleteAPI,
          {
            delete_items: this.selectedItems,
          },
          this.axiosConfig
        )
        .then((response) => {
          this.addNotification(response.status, "delete");
        })
        .catch((error) => {
          if (error.code === "ECONNABORTED")
            this.addNotification(408, "delete", "timeout");
          else {
            error.response.data.detail.map((d) => {
              this.addNotification(error.response.status, "delete", d);
            });
          }
        });
      this.closeModal();
      this.reloadItemlist();
    },
    reloadItemlist() {
      this.getItemList(this.currentPath);
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
