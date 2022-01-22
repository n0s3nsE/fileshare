<template>
  <div class="modal-window delete-modal">
    <div class="modal-main">
      <p>削除しますか？</p>
    </div>
    <div class="modal-ctl">
      <button @click="delete_items" :disabled="deleting">
        <div v-if="!deleting">削除</div>
        <vue-loading
          v-else
          type="spin"
          color="#333"
          :size="{ width: '22px', height: '22px' }"
        />
      </button>

      <button @click="close_modal" :disabled="deleting">キャンセル</button>
    </div>
  </div>
</template>

<script>
import axios from "axios";
import Mixin from "../../mixin/mixin";
import { VueLoading } from "vue-loading-template";

export default {
  components: {
    VueLoading,
  },
  data() {
    return {
      selected_items: [],
      current_path: "",
      deleting: false,
      delete_api_url: "http://127.0.0.1:8000/api/delete",
    };
  },
  mixins: [Mixin],
  computed: {
    selected_items_getters() {
      return this.$store.getters.get_selected_items;
    },
  },
  mounted() {
    this.current_path = this.get_path();
    this.selected_items = this.selected_items_getters;
  },
  methods: {
    async delete_items() {
      this.deleting = true;
      await axios.post(this.delete_api_url, {
        delete_items: this.selected_items,
      });
      this.reload_itemlist();
    },
    reload_itemlist() {
      this.close_modal();
      this.get_itemlist(this.current_path);
    },
    close_modal() {
      this.deleting = false;
      this.$emit("close_modal");
    },
  },
};
</script>
<style>
@import "../../css/Modal.css";
</style>
