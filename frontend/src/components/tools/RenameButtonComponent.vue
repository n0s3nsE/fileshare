<template>
  <div>
    <button @click="open_modal" :disabled="modal_status">名前を変更</button>
    <rename-modal v-if="modal_status" @close_modal="close_modal" />
  </div>
</template>

<script>
import RenameModal from "../modals/RenameModal.vue";

export default {
  components: {
    RenameModal,
  },
  data() {
    return {
      modal_status: false,
    };
  },
  computed: {
    selected_item_getters() {
      return this.$store.getters.get_selected_items[0];
    },
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
