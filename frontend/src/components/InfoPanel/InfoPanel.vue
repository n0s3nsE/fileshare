<template>
  <div class="info-panel">
    <div v-if="thum" class="info-panel-thum">
      <img :src="'http://127.0.0.1:8000/api/preview/' + this_file.id" />
    </div>
    <div :class="[thum ? 'info-panel-main-thum' : 'info-panel-main-nothum']">
      <p>ファイル名: {{ this_file.name }}</p>
      <p>サイズ: {{ size_convert(this_file.size) }}</p>
      <p>更新日時: {{ this_file.updated_at }}</p>
      <a :href="'http://127.0.0.1:8000/api/preview/' + this_file.id" download=""
        >ダウンロード</a
      >
    </div>
  </div>
</template>
<script>
import axios from "axios";
import Mixin from "../../mixin/mixin";

export default {
  props: ["this_file_id", "thum"],
  data() {
    return {
      content_detail_api_url: "http://127.0.0.1:8000/api/detail",
      this_file: "",
    };
  },
  mixins: [Mixin],
  mounted() {
    this.get_detail();
  },
  methods: {
    async get_detail() {
      const gt = await axios
        .get(this.content_detail_api_url + "/" + this.this_file_id)
        .then((response) => {
          return response.data.detail;
        });
      this.this_file = this.convart_dt([gt])[0];
    },
    size_convert(size) {
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
  margin: 0px 8px;

  background: #222222;
}
.info-panel-thum img {
  height: 100%;
  width: 100%;
  object-fit: contain;
}

.info-panel-main-thum {
  height: 50%;
  width: 100%;
  overflow-y: scroll;
  scrollbar-width: none;
  overflow-wrap: anywhere;
  margin: 0px 8px;
}

.info-panel-main-nothum {
  height: 100%;
  width: 100%;
  overflow-y: scroll;
  scrollbar-width: none;
  overflow-wrap: anywhere;
  margin: 0px 8px;

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
