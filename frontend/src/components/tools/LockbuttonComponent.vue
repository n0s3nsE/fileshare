<template>
  <div>
    <button @click="send_locksignal">
      <div v-if="islocked">
        <svg
          width="18"
          height="26"
          viewBox="0 0 18 26"
          fill="none"
          xmlns="http://www.w3.org/2000/svg"
        >
          <rect y="8" width="18" height="18" fill="#3C3C3C" />
          <path
            d="M9.5 17.937C10.3626 17.715 11 16.9319 11 16C11 14.8954 10.1046 14 9 14C7.89543 14 7 14.8954 7 16C7 16.9319 7.63739 17.715 8.5 17.937V21H9.5V17.937Z"
            fill="white"
          />
          <path
            d="M17 8C17 3.58172 13.4183 0 9 0C4.58172 0 1 3.58172 1 8H3C3 4.68629 5.68629 2 9 2C12.3137 2 15 4.68629 15 8H17Z"
            fill="#3C3C3C"
          />
        </svg>
      </div>
      <div v-else>
        <svg
          width="18"
          height="26"
          viewBox="0 0 18 26"
          fill="none"
          xmlns="http://www.w3.org/2000/svg"
        >
          <rect y="11" width="18" height="15" fill="#3C3C3C" />
          <path
            d="M9.5 18.937C10.3626 18.715 11 17.9319 11 17C11 15.8954 10.1046 15 9 15C7.89543 15 7 15.8954 7 17C7 17.9319 7.63739 18.715 8.5 18.937V22H9.5V18.937Z"
            fill="white"
          />
          <path
            d="M17 8C17 3.58172 13.4183 0 9 0C4.58172 0 1 3.58172 1 8H3C3 4.68629 5.68629 2 9 2C12.3137 2 15 4.68629 15 8H17Z"
            fill="#3C3C3C"
          />
          <rect x="15" y="8" width="2" height="3" fill="#3C3C3C" />
        </svg>
      </div>
    </button>
  </div>
</template>

<script>
import axios from "axios";
import Mixin from "../../mixin/mixin";

export default {
  props: ["item_id", "islocked"],
  data() {
    return {
      lock_api_url: "http://127.0.0.1:8000/api/lock",
    };
  },
  mixins: [Mixin],
  methods: {
    send_locksignal() {
      axios
        .get(this.lock_api_url + "/" + this.item_id)
        .then(() => {
          this.get_itemlist(this.get_path());
        })
        .catch((error) => {
          console.log(error);
        });
    },
  },
};
</script>
