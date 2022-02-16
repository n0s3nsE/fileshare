<template>
  <div v-if="notifications.length" class="notification-modal">
    <div class="notification-modal-main" @click="openDetailModal">
      <p v-if="errors.length > 0">
        {{ type[errors[0].type] }}失敗 [クリックで詳細]
      </p>
      <p v-else>{{ type[notifications[0].type] }}成功</p>
    </div>
    <div class="notification-modal-ctl">
      <button @click="closeModal">
        <svg
          width="13"
          height="13"
          viewBox="0 0 13 13"
          fill="none"
          xmlns="http://www.w3.org/2000/svg"
        >
          <path
            fill-rule="evenodd"
            clip-rule="evenodd"
            d="M6.25 8.25L11 13L12.4142 11.5858L7.66421 6.83579L12.5 2L11.0858 0.585787L6.25 5.42157L1.41421 0.585785L0 2L4.83579 6.83579L0.0857865 11.5858L1.5 13L6.25 8.25Z"
            fill="white"
          />
        </svg>
      </button>
    </div>
  </div>
</template>

<script>
export default {
  data() {
    return {
      notifications: [],
      errors: [],
      type: {
        upload: "アップロード",
        delete: "削除",
        rename: "更新",
        create: "作成",
        lock: "ロック",
      },
    };
  },
  computed: {
    notificationGetters() {
      return this.$store.getters.getNotification;
    },
  },
  watch: {
    notificationGetters() {
      this.notifications = this.notificationGetters;
      this.errors = this.notifications.filter(
        (i) => i.status_code !== 200 && i.status_code !== 204
      );
    },
  },
  created() {
    this.notifications = this.notificationGetters;
  },
  methods: {
    closeModal() {
      this.$store.commit("removeNotificationMutation");
    },
    openDetailModal() {
      if (this.notifications.filter((i) => i.status_code !== 200).length > 0) {
        this.$store.commit("changeNdmsMutation", {
          status: true,
        });
      }
    },
  },
};
</script>
<style>
.notification-modal {
  display: flex;
  justify-content: space-between;
  align-items: center;

  background-color: #3c3c3c;
  color: white;
  width: 350px;
  height: 29px;

  position: absolute;
  bottom: 0;
  left: 50%;
  transform: translate(-50%);
  padding: 0;
}

.notification-modal-main {
  width: 321px;
  padding-left: 16px;
}

.notification-modal-main:hover {
  background-color: #4c4c4c;
}

.notification-modal-main p {
  margin: 0px;
}

.notification-modal-ctl {
  width: 29px;
  height: 29px;
}

.notification-modal-ctl:hover {
  background: #1c1c1c;
}

.notification-modal-ctl button {
  display: flex;
  align-items: center;
  justify-content: center;

  color: white;
  background: none;
  width: 29px;
  height: 29px;
}
</style>
