<template>
  <div v-if="status" class="modal-notification-detail">
    <div class="modal-notification-main">
      <ul>
        <li v-for="(n, i) in notifications" :key="i">
          {{ n.status_code === 200 ? "成功" : "失敗" + "|" + n.detail }}
        </li>
      </ul>
    </div>
    <div class="modal-notification-ctl">
      <button @click="close_modal">完了</button>
    </div>
  </div>
</template>

<script>
export default {
  data() {
    return {
      status: false,
      notifications: [],
    };
  },
  computed: {
    ndms_getters() {
      return this.$store.getters.get_notification_detail_modal_status;
    },
    notification_getters() {
      return this.$store.getters.get_notification;
    },
  },
  watch: {
    ndms_getters() {
      this.status = this.ndms_getters;
      if (this.status) {
        this.get_notifications();
      }
    },
  },
  mounted() {
    this.get_notifications();
  },
  methods: {
    get_notifications() {
      this.notifications = this.notification_getters;
    },
    close_modal() {
      this.$store.commit("change_ndms_mutation", {
        status: false,
      });
      this.$store.commit("remove_notification_mutation");
    },
  },
};
</script>

<style>
.modal-notification-detail {
  width: 700px;
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%);

  border: black solid 1px;
  position: absolute;
  background-color: white;
  color: black;
}
.modal-notification-main {
  height: 250px;
  padding: 8px;
  overflow-y: scroll;
  scrollbar-width: none;
}
.modal-notification-ctl {
  display: flex;
  align-items: center;
  justify-content: end;

  height: 50px;
  background-color: #eeeeee;
  border-top: #c3c3c3 1px solid;

  position: absolute;
  bottom: 0;
  right: 0;
  left: 0;
}
.modal-notification-ctl button {
  width: 96px;
  height: 36px;
  margin-right: 8px;
  background-color: #6070ff;
  color: white;
}
.modal-notification-ctl button:hover {
  background-color: #4652bd;
}
</style>
