<template>
  <div class="preview-info">
    <p>ファイル名: {{ this_file.name }}</p>
    <p v-if="this_file.size < 1024">サイズ: {{ this_file.size }}KB</p>
    <p v-else>サイズ: {{ Math.round((this_file.size / 1024) * 10) / 10 }}MB</p>
    <p>更新日時: {{ this_file.updated_at }}</p>
    <a :href="'http://127.0.0.1:8000/api/preview/' + this_file.id" download=""
      >ダウンロード</a
    >
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
  },
};
</script>

<style>
.preview-info {
  height: 100%;
  width: 100%;
  overflow-y: scroll;
  overflow-wrap: anywhere;
  margin: 0 8px;

  background: #222222;
  color: #eeeeee;
}
.preview-info a,
.preview-info a:visited {
  color: #eeeeee;
  text-decoration: none;
}
</style>
