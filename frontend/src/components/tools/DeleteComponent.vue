<template>
  <div class="toolbar-delete">
    <button @click="open_modal" :disabled="modal_status">削除</button>
    <delete-modal v-if="modal_status" @close_modal="close_modal" />
  </div>
</template>
<script>
import DeleteModal from "../modals/DeleteModal.vue";

export default {
  components: {
    DeleteModal,
  },
  data() {
    return {
      modal_status: false,
    };
  },
  methods: {
    open_modal() {
      this.modal_status = true;
    },
    close_modal() {
      this.modal_status = false;
    },
  },
};
</script>
