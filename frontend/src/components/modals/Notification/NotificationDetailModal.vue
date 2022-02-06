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
      <button @click="closeModal">完了</button>
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
    ndmsGetters() {
      return this.$store.getters.getNotificationDetailModalStatus;
    },
    notificationGetters() {
      return this.$store.getters.getNotification;
    },
  },
  watch: {
    ndmsGetters() {
      this.status = this.ndmsGetters;
      if (this.status) {
        this.getNotifications();
      }
    },
  },
  mounted() {
    this.getNotifications();
  },
  methods: {
    getNotifications() {
      this.notifications = this.notificationGetters;
    },
    closeModal() {
      this.$store.commit("changeNdmsMutation", {
        status: false,
      });
      this.$store.commit("removeNotificationMutation");
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
