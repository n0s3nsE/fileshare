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
import axios from "axios";
import Mixin from "../../mixin/mixin";

export default {
  data() {
    return {
      files: [],
      upload_api_url: "http://127.0.0.1:8000/api/upload",
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
      this.files = this.$refs.upload_files.files;
      this.add_upload_queue();
    },
    add_upload_queue() {
      for (let i = 0; i < this.files.length; i++) {
        this.$store.commit("add_upload_queue_mutation", {
          file: {
            id: i,
            name: this.files[i].name,
            progress: 0,
          },
        });
      }
      this.file_upload();
    },
    file_upload() {
      for (let i = 0; i < this.files.length; i++) {
        const formData = new FormData();
        formData.append("name", this.files[i].name);
        formData.append("path", this.get_path());
        formData.append("data", this.files[i]);

        axios.post(this.upload_api_url, formData).then(() => {
          this.$store.commit("update_progress", {
            item: {
              id: i,
              progress: 100,
            },
          });
        });
      }
    },
  },
};
</script>
