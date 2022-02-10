<template>
  <div class="info-panel">
    <div v-if="thum" class="info-panel-thum">
      <svg
        v-if="thisFile.isfolder"
        width="100%"
        height="100%"
        viewBox="0 0 26 22"
        fill="none"
        xmlns="http://www.w3.org/2000/svg"
      >
        <path d="M0 0H10L15 3H26V22H0V0Z" fill="#3C3C3C" />
      </svg>
      <img
        v-else-if="thisFile.isfolder === 0"
        :src="contentPreviewAPI + '/' + thisFile.id"
      />
    </div>
    <div :class="[thum ? 'info-panel-main-thum' : 'info-panel-main-nothum']">
      <p>ファイル名: {{ thisFile.name }}</p>
      <p>サイズ: {{ sizeConvert(thisFile.size) }}</p>
      <p>更新日時: {{ thisFile.updated_at }}</p>
      <a :href="contentPreviewAPI + '/' + thisFile.id" download=""
        >ダウンロード</a
      >
    </div>
  </div>
</template>
<script>
import axios from "axios";
import Mixin from "../../mixin/mixin";

export default {
  props: {
    thisFileId: Object,
    thum: Boolean,
  },
  data() {
    return {
      contentDetailAPI: process.env.VUE_APP_API_BASE_URL_DEV + "/detail",
      contentPreviewAPI: process.env.VUE_APP_API_BASE_URL_DEV + "/preview",
      thisFile: "",
    };
  },
  mixins: [Mixin],
  mounted() {
    this.getDetail();
  },
  methods: {
    async getDetail() {
      const gt = await axios
        .get(this.contentDetailAPI + "/" + this.thisFileId)
        .then((response) => {
          return response.data.detail;
        });

      this.thisFile = this.convertDt([gt])[0];
    },
    sizeConvert(size) {
      if (size >= 1024) {
        return Math.round((size / 1024) * 10) / 10 + "MB";
      } else {
        return size + "KB";
      }
    },
  },
};
</script>

<style>
.info-panel {
  height: 100%;
  width: 100%;
}

.info-panel-thum {
  height: 50%;
  width: 100%;
}

.info-panel-thum img {
  height: 100%;
  width: 100%;
  object-fit: contain;
  background: #222222;
}

.info-panel-main-thum {
  height: 50%;
  width: 100%;
  overflow-y: scroll;
  scrollbar-width: none;
  overflow-wrap: anywhere;
  padding: 8px 8px 0px 8px;
}

.info-panel-main-nothum {
  height: 100%;
  width: 100%;
  overflow-y: scroll;
  scrollbar-width: none;
  overflow-wrap: anywhere;

  background: #222222;
  color: #eeeeee;
}

.info-panel-main-nothum a,
.info-panel-main-nothum a:visited {
  color: #eeeeee;
  text-decoration: none;
}

.info-panel-main-thum a,
.info-panel-main-thum a:visited {
  color: black;
  text-decoration: none;
}
</style>
