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
      <button @click="renameItem">保存</button>
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
      selectedItem: null,
      renameAPI: "http://127.0.0.1:8000/api/rename",
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
      await axios
        .post(this.renameAPI, {
          id: this.selectedItem,
          new_name: this.$refs.newName.value,
        })
        .then((response) => {
          this.addNotification(response.status, "rename");
        })
        .catch((error) => {
          this.addNotification(
            error.response.status,
            "rename",
            error.response.data.detail
          );
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
