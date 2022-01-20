<template>
  <div class="preview-sidepanel">
    <div class="preview-thumbnail">
      <img
        v-if="typeof this_file.id !== 'undefined' && thum"
        :src="'http://127.0.0.1:8000/api/preview/' + this_file.id"
      />
    </div>
    <div class="preview-info">
      <p>ファイル名: {{ this_file.name }}</p>
      <p v-if="this_file.size < 1024">サイズ: {{ this_file.size }}KB</p>
      <p v-else>
        サイズ: {{ Math.round((this_file.size / 1024) * 10) / 10 }}MB
      </p>
      <p>更新日時: {{ this_file.updated_at }}</p>
      <a :href="'http://127.0.0.1:8000/api/preview/' + this_file.id" download=""
        >ダウンロード</a
      >
    </div>
  </div>
</template>
<script>
import axios from "axios";
import Mixin from "../mixin/mixin";

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
    this.test();
  },
  methods: {
    async test() {
      const get_detail = await axios
        .get(this.content_detail_api_url + "/" + this.this_file_id)
        .then((response) => {
          return response.data.detail;
        });
      this.this_file = this.convart_dt([get_detail])[0];
    },
  },
};
</script>

<style>
.preview-sidepanel {
  height: calc(100vh - 96px);
  width: 25%;
}
.preview-thumbnail {
  height: 50%;
  width: 100%;
  margin: 0 8px;
}
.preview-thumbnail img {
  height: 100%;
  width: 100%;
  object-fit: contain;
}
.preview-info {
  height: 50%;
  width: 100%;
  overflow-y: scroll;
  scrollbar-width: thin;
  overflow-wrap: anywhere;
  margin: 0 8px;
}
.preview-info::-webkit-scrollbar {
  display: none;
  scrollbar-width: thin;
}
</style>
