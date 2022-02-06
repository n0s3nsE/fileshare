<template>
  <div class="toolbar">
    <upload-button />
    <create-button />
    <delete-button v-if="selectedItems.length > 0" />
    <rename-button v-if="selectedItems.length === 1" />
  </div>
</template>
<script>
import UploadButton from "./utils/UploadComponent.vue";
import CreateButton from "./utils/CreateComponent.vue";
import DeleteButton from "./utils/DeleteComponent.vue";
import RenameButton from "./utils/RenameButtonComponent.vue";

export default {
  components: {
    UploadButton,
    CreateButton,
    DeleteButton,
    RenameButton,
  },
  data() {
    return {
      selectedItems: [],
    };
  },
  computed: {
    selectedItemsGetters() {
      return this.$store.getters.getSelectedItems;
    },
  },
  mounted() {
    this.selectedItems = this.selectedItemsGetters;
  },
  watch: {
    selectedItemsGetters(value) {
      this.selectedItems = value;
    },
  },
};
</script>

<style>
.toolbar {
  height: 39px;
  display: flex;

  padding: 8px 0px;
}
.toolbar button {
  background: #6070ff;
  border: none;
  border-radius: 0;
  color: white;

  margin-right: 8px;
  padding: 8px;
}
.toolbar button:hover {
  background: #4652bd;
}
</style>
