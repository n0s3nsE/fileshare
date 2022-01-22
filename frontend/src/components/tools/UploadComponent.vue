<template>
  <div class="toolbar-upload">
    <button v-bind:disabled="!status">
      <label for="file-open">アップロード</label>
      <input
        type="file"
        name="file"
        multiple
        id="file-open"
        @change="file_change()"
        style="display: none"
        ref="upload_files"
        v-bind:disabled="!status"
      />
    </button>
  </div>
</template>
<script>
import Mixin from "../../mixin/mixin";

export default {
  data() {
    return {
      status: true,
    };
  },
  mixins: [Mixin],
  computed: {
    toolbar_status_getters() {
      return this.$store.getters.get_toolbar_status;
    },
  },
  watch: {
    toolbar_status_getters(value) {
      this.status = value;
    },
  },
  methods: {
    file_change() {
      this.mixin_upload(this.$refs.upload_files.files);
    },
  },
};
</script>
