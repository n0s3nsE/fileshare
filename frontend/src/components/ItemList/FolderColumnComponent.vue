<template>
  <div class="list-view-name">
    <div class="list-view-name-icon">
      <svg
        width="26"
        height="22"
        viewBox="0 0 26 22"
        fill="none"
        xmlns="http://www.w3.org/2000/svg"
      >
        <path d="M0 0H10L15 3H26V22H0V0Z" fill="#3C3C3C" />
      </svg>
    </div>
    <div class="list-view-name-link">
      <a :href="folder_url">
        {{ item.name }}
      </a>
    </div>
    <lock-button
      :item_id="item.id"
      :islocked="item.islocked"
      class="list-view-name-lockbutton"
    />
  </div>
</template>

<script>
import Mixin from "../../mixin/mixin";
import LockButton from "./LockbuttonComponent.vue";

export default {
  components: {
    LockButton,
  },
  props: ["item"],
  data() {
    return {
      folder_url: "",
    };
  },
  mixins: [Mixin],
  mounted() {
    const url = this.get_path();
    if (url !== "/") {
      this.folder_url = url + "/" + this.item.name;
    } else {
      this.folder_url = url + this.item.name;
    }
  },
};
</script>

<style>
.list-view-name {
  display: flex;
}
.list-view-name-icon {
  margin: auto 8px;
}

.list-view-name-lockbutton {
  margin-left: auto;
  margin-top: auto;
  margin-bottom: auto;
}

.list-view-name-link {
  max-width: 450px;
  overflow: hidden;
  text-overflow: ellipsis;
  line-height: 50px;
}
</style>
