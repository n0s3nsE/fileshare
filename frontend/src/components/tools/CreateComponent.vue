<template>
  <div class="toolbar-create">
    <div>
      <button @click="open_modal()" :disabled="modal_status">作成</button>
    </div>
    <create-modal v-if="modal_status" @close_modal="close_modal" />
  </div>
</template>

<script>
import CreateModal from "../modals/CreateModal.vue";
export default {
  components: {
    CreateModal,
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
