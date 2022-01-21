<template>
  <div>
    <div class="list-view-name-icon">
      <svg
        width="22"
        height="26"
        viewBox="0 0 22 26"
        fill="none"
        xmlns="http://www.w3.org/2000/svg"
      >
        <path
          d="M21.5 2V24C21.5 24.8284 20.8284 25.5 20 25.5H2C1.17157 25.5 0.5 24.8284 0.5 24V2C0.5 1.17157 1.17157 0.5 2 0.5H8.46154H15H20C20.8284 0.5 21.5 1.17157 21.5 2Z"
          fill="white"
          stroke="#3C3C3C"
        />
        <line x1="4" y1="5.5" x2="18" y2="5.5" stroke="#3C3C3C" />
        <line x1="4" y1="14.5" x2="18" y2="14.5" stroke="#3C3C3C" />
        <line x1="4" y1="19.5" x2="18" y2="19.5" stroke="#3C3C3C" />
        <line x1="4" y1="10.5" x2="18" y2="10.5" stroke="#3C3C3C" />
      </svg>
    </div>
    <div class="list-view-name-link">
      <a :href="'?id=' + item.id + '&type=' + item.name.split('.').pop()">
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
import LockButton from "./LockbuttonComponent.vue";

export default {
  components: {
    LockButton,
  },
  props: ["item"],
};
</script>

<style>
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
