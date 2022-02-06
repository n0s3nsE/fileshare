<template>
  <div class="toolbar-upload">
    <button :disabled="!status">
      <label for="file-open">アップロード</label>
      <input
        type="file"
        name="file"
        multiple
        id="file-open"
        @change="fileChange()"
        style="display: none"
        ref="uploadFiles"
        :disabled="!status"
      />
    </button>
  </div>
</template>
<script>
import Mixin from "../../../mixin/mixin";

export default {
  data() {
    return {
      status: true,
    };
  },
  mixins: [Mixin],
  computed: {
    toolbarStatusGetters() {
      return this.$store.getters.getToolbarStatus;
    },
  },
  watch: {
    toolbarStatusGetters(value) {
      this.status = value;
    },
  },
  methods: {
    fileChange() {
      this.mixinUpload(this.$refs.uploadFiles.files);
    },
  },
};
</script>
