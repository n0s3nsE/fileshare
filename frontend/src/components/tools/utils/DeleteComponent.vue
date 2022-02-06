<template>
  <div class="toolbar-delete">
    <button @click="openModal" :disabled="modalStatus">削除</button>
    <delete-modal v-if="modalStatus" @closeModal="closeModal" />
  </div>
</template>
<script>
import DeleteModal from "../../modals/DeleteModal.vue";

export default {
  components: {
    DeleteModal,
  },
  data() {
    return {
      modalStatus: false,
    };
  },
  methods: {
    openModal() {
      this.modalStatus = true;
    },
    closeModal() {
      this.modalStatus = false;
    },
  },
};
</script>
