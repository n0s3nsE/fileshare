<template>
  <div class="toolbar-create">
    <div>
      <button @click="open_modal()" :disabled="modalStatus">作成</button>
    </div>
    <create-modal v-if="modalStatus" @close_modal="close_modal" />
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
    open_modal() {
      this.modalStatus = true;
    },
    close_modal() {
      this.modalStatus = false;
    },
  },
};
</script>
