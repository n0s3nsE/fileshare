<template>
  <div class="toolbar">
    <upload-button />
    <create-button />
    <delete-button />
  </div>
</template>
<script>
import UploadButton from "./UploadComponent.vue";
import CreateButton from "./CreateComponent.vue";
import DeleteButton from "./DeleteComponent.vue";

export default {
  components: {
    UploadButton,
    CreateButton,
    DeleteButton,
  },
};
</script>
