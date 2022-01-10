<template>
  <div>
    <button @click="send_locksignal">svg lockicon{{ item_id }}</button>
  </div>
</template>

<script>
import axios from "axios";
import Mixin from "../../mixin/mixin";
export default {
  props: ["item_id"],
  data() {
    return {
      lock_api_url: "http://127.0.0.1:8000/api/lock",
    };
  },
  mixins: Mixin,
  methods: {
    send_locksignal() {
      axios
        .get(this.lock_api_url + "/" + this.item_id)
        .then(() => this.get_itemlist(this.current_path))
        .catch(() => {});
    },
  },
};
</script>
