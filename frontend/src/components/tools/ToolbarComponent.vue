<template>
  <div class="toolbar">
    <upload-button />
    <create-button />
    <delete-button v-if="selectedItems.length > 0 && !isLocked" />
    <rename-button v-if="selectedItems.length === 1 && !isLocked" />
  </div>
</template>
<script>
import UploadButton from "./utils/UploadComponent.vue";
import CreateButton from "./utils/CreateComponent.vue";
import DeleteButton from "./utils/DeleteComponent.vue";
import RenameButton from "./utils/RenameButtonComponent.vue";
import Mixin from "../../mixin/mixin";

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
      isLocked: true,
    };
  },
  mixins: [Mixin],
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
      this.checkIsLocked();
    },
  },
  methods: {
    async checkIsLocked() {
      if (this.selectedItems.length === 0) return;
      const gd = await this.getDetail(this.selectedItems[0]);
      gd.islocked ? (this.isLocked = true) : (this.isLocked = false);
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
