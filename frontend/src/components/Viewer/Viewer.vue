<template>
  <div>
    <img
      v-if="ext_type.img.indexOf(param_ext) > -1"
      :src="view_api_url + '/' + param_id"
    />
    <video
      v-else-if="ext_type.video.indexOf(param_ext) > -1"
      :src="view_api_url + '/' + param_id"
    />
    <div v-else>
      <p>プレビュー非対応なファイル形式</p>
      <a :href="view_api_url + '/' + param_id">Download</a>
    </div>
  </div>
</template>
<script>
export default {
  props: ["param"],
  data() {
    return {
      param_id: null,
      view_api_url: "http://127.0.0.1:8000/api/preview",
      ext_type: {
        video: ["mp4", "avi", "wav", "mov", "wmv"],
        img: ["jpg", "jpeg", "png", "bmp", "gif", "svg"],
      },
    };
  },
  created() {
    this.param_id = this.param.split("&")[0].split("=")[1];
    this.param_ext = this.param.split("&")[1].split("=")[1];
  },
};
</script>
