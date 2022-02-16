<template>
  <div class="toolbar-create">
    <div>
      <button @click="openModal()" :disabled="modalStatus">フォルダ作成</button>
    </div>
    <create-modal v-if="modalStatus" @closeModal="closeModal" />
  </div>
</template>

<script>
import CreateModal from "../../modals/CreateModal.vue";
export default {
  components: {
    CreateModal,
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
