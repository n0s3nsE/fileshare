<template>
  <div>
    <button @click="openModal" :disabled="modalStatus">名前を変更</button>
    <rename-modal v-if="modalStatus" @closeModal="closeModal" />
  </div>
</template>

<script>
import RenameModal from "../../modals/RenameModal.vue";

export default {
  components: {
    RenameModal,
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
