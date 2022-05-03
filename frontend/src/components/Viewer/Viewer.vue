<template>
  <div class="preview">
    <header class="preview-header">
      <div class="back-button">
        <button @click="returnPage()">
          <svg
            xmlns="http://www.w3.org/2000/svg"
            width="30"
            height="24"
            viewBox="0 0 30 24"
            fill="none"
          >
            <path
              d="M0.93934 10.9393C0.353553 11.5251 0.353553 12.4749 0.93934 13.0607L10.4853 22.6066C11.0711 23.1924 12.0208 23.1924 12.6066 22.6066C13.1924 22.0208 13.1924 21.0711 12.6066 20.4853L4.12132 12L12.6066 3.51472C13.1924 2.92893 13.1924 1.97918 12.6066 1.3934C12.0208 0.807612 11.0711 0.807612 10.4853 1.3934L0.93934 10.9393ZM30 10.5L2 10.5V13.5L30 13.5V10.5Z"
              fill="#eeeeee"
            />
          </svg>
        </button>
      </div>
      <div>
        <p>{{ thisFile.name }}</p>
      </div>
    </header>
    <div class="preview-container">
      <div class="preview-main" v-if="!errorFlag">
        <img
          v-if="fileType.img.indexOf(paramType) > -1"
          :src="previewAPI + '/' + paramId"
        />
        <video
          controls
          v-else-if="fileType.video.indexOf(paramType) > -1"
          :src="previewAPI + '/' + paramId"
        />
        <div v-else>
          <p>プレビュー非対応なファイル形式</p>
          <a :href="previewAPI + '/' + paramId">Download</a>
        </div>
      </div>
      <div v-else class="preview-main">不正なパラメーター</div>

      <div class="preview-controller">
        <button
          class="preview-controller-before"
          :disabled="!beforeItem"
          @click="gotoBefore"
        >
          <svg
            xmlns="http://www.w3.org/2000/svg"
            width="30"
            height="24"
            viewBox="0 0 30 24"
            fill="none"
          >
            <path
              d="M0.93934 10.9393C0.353553 11.5251 0.353553 12.4749 0.93934 13.0607L10.4853 22.6066C11.0711 23.1924 12.0208 23.1924 12.6066 22.6066C13.1924 22.0208 13.1924 21.0711 12.6066 20.4853L4.12132 12L12.6066 3.51472C13.1924 2.92893 13.1924 1.97918 12.6066 1.3934C12.0208 0.807612 11.0711 0.807612 10.4853 1.3934L0.93934 10.9393ZM30 10.5L2 10.5V13.5L30 13.5V10.5Z"
              fill="#eeeeee"
            />
          </svg>
        </button>
        <button>
          <p>拡大縮小のやつ</p>
        </button>
        <button
          class="preview-controller-next"
          :disabled="!nextItem"
          @click="gotoNext"
        >
          <svg
            width="30"
            height="24"
            viewBox="0 0 30 24"
            fill="none"
            xmlns="http://www.w3.org/2000/svg"
          >
            <path
              d="M29.0607 10.9393C29.6464 11.5251 29.6464 12.4749 29.0607 13.0607L19.5147 22.6066C18.9289 23.1924 17.9792 23.1924 17.3934 22.6066C16.8076 22.0208 16.8076 21.0711 17.3934 20.4853L25.8787 12L17.3934 3.51472C16.8076 2.92893 16.8076 1.97918 17.3934 1.3934C17.9792 0.807612 18.9289 0.807612 19.5147 1.3934L29.0607 10.9393ZM0 10.5L28 10.5V13.5L0 13.5L0 10.5Z"
              fill="#eeeeee"
            />
          </svg>
        </button>
      </div>
      <preview-info-panel class="preview-info-panel" :thisFileId="paramId" />
    </div>
  </div>
</template>

<script>
import Mixin from "../../mixin/mixin";
import PreviewInfoPanel from "../InfoPanel/InfoPanel.vue";

export default {
  props: {
    param: String,
  },
  data() {
    return {
      paramId: null,
      previewAPI: process.env.VUE_APP_API_BASE_URL + "/preview",
      fileType: {
        video: ["mp4", "avi", "wav", "mov", "wmv"],
        img: ["jpg", "jpeg", "png", "bmp", "gif", "svg", "jfif"],
      },
      thisFile: "",
      nextItem: null,
      beforeItem: null,
      errorFlag: false,
    };
  },
  components: {
    PreviewInfoPanel,
  },
  mixins: [Mixin],
  computed: {
    itemListGetters() {
      return this.$store.getters.getItemList;
    },
  },
  created() {
    const splitParam = this.splitParam(this.param);
    this.paramId = splitParam.id;
    this.paramType = splitParam.type.toLowerCase();

    if (
      typeof this.paramId !== "undefined" &&
      typeof this.paramType !== "undefined"
    ) {
      this.getItemList(this.getPath());
    } else {
      this.errorFlag = true;
    }
  },
  watch: {
    itemListGetters() {
      this.createItemList(this.itemListGetters);
    },
  },
  methods: {
    splitParam(param) {
      const sp_param = param.split("&").map((i) => i.split("="));
      return Object.fromEntries(sp_param);
    },
    createItemList(itemList) {
      const items = itemList.filter((i) => !i.isfolder);
      const itemsLength = items.length;

      const filterId = (item, index) => {
        if (item.id === parseInt(this.paramId)) {
          this.thisFile = item;

          if (index - 1 >= 0) {
            this.beforeItem = {
              id: items[index - 1].id,
              type: items[index - 1].name.split(".").pop(),
            };
          }
          if (index + 1 < itemsLength) {
            this.nextItem = {
              id: items[index + 1].id,
              type: items[index + 1].name.split(".").pop(),
            };
          }
        } else {
          return false;
        }
      };

      items.filter(filterId);
    },
    returnPage() {
      window.location.href = this.getPath();
    },
    gotoBefore() {
      window.location.href = `?id=${this.beforeItem.id}&type=${this.beforeItem.type}`;
    },
    gotoNext() {
      window.location.href = `?id=${this.nextItem.id}&type=${this.nextItem.type}`;
    },
  },
};
</script>

<style>
.preview {
  display: flex;
  flex-flow: column;
  background: #111111;
  padding: 0;
}

.preview-header {
  display: flex;
  align-items: center;
  height: 56px;
  top: 0px;

  background: #222222;
  color: #eeeeee;
}
.preview-header div {
  height: 100%;
  padding: 0px 8px;
}
.preview-header button {
  background: none;
}
.back-button {
  display: flex;
  align-items: center;
  transition: background 0.3s;
}
.back-button:hover {
  background: #111111;
}

.preview-container {
  display: flex;
  height: calc(100vh - 56px);
  min-height: 400px;
}

.preview-main {
  height: calc(100% - 56px);
  width: 80%;
}

.preview-info-panel {
  height: 100%;
  width: 20%;
}

.preview-main img,
.preview-main video {
  height: 100%;
  width: 100%;
  object-fit: contain;
}

.preview-controller {
  display: flex;
  align-items: center;
  justify-content: center;
  position: absolute;
  bottom: 0px;
  height: 56px;
  width: 80%;
  background: #222222;
}

.preview-controller button {
  height: 56px;
  width: 56px;
  border-radius: 50%;
  background: none;
  text-decoration: none;
}

.preview-controller button:hover {
  background: #111111;
  text-decoration: none;
  transition: background 0.3s;
}

.preview-controller button:disabled {
  background: #222222;
  opacity: 0.5;
}

.preview-controller-before {
  margin-right: 32px;
}

.preview-controller-next {
  margin-right: 32px;
}
</style>
