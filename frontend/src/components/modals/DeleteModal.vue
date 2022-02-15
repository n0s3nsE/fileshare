<template>
  <div class="modal-window delete-modal">
    <div class="modal-main">
      <p>削除しますか？</p>
    </div>
    <div class="modal-ctl">
      <button @click="deleteItems" :disabled="isLoading">
        <vue-loading
          v-if="isLoading"
          type="spin"
          color="#333"
          :size="{ width: '22px', height: '22px' }"
        />
        <span v-else>削除</span>
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
      selectedItems: [],
      currentPath: "",
      deleteAPI: process.env.VUE_APP_API_BASE_URL + "/delete",
      isLoading: false,
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
      this.isLoading = true;
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
          this.isLoading = false;
        })
        .catch((error) => {
          if (error.code === "ECONNABORTED")
            this.addNotification(408, "delete", "timeout");
          else {
            error.response.data.detail.map((d) => {
              this.addNotification(error.response.status, "delete", d);
            });
          }
          this.isLoading = false;
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
