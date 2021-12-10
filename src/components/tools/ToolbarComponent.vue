<template>
  <div class="toolbar">
    <upload />
    <create />
  </div>
</template>
<script>
import upload from "./UploadComponent.vue";
import create from "./CreateComponent.vue";

export default {
  components: {
    upload,
    create,
  },
};
</script>
