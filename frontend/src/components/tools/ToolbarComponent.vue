<template>
  <div class="toolbar">
    <upload-button />
    <create-button />
    <delete-button v-if="selected_items.length > 0" />
    <rename-button v-if="selected_items.length === 1" />
  </div>
</template>
<script>
import UploadButton from "./UploadComponent.vue";
import CreateButton from "./CreateComponent.vue";
import DeleteButton from "./DeleteComponent.vue";
import RenameButton from "./RenameButtonComponent.vue";

export default {
  components: {
    UploadButton,
    CreateButton,
    DeleteButton,
    RenameButton,
  },
  data() {
    return {
      selected_items: [],
    };
  },
  computed: {
    selected_items_getters() {
      return this.$store.getters.get_selected_items;
    },
  },
  mounted() {
    this.selected_items = this.selected_items_getters;
  },
  watch: {
    selected_items_getters(value) {
      this.selected_items = value;
    },
  },
};
</script>

<style>
.toolbar {
  height: 39px;
  border-bottom: #666666 1px solid;
  display: flex;
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
