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
      chunk_progress: 0,
      chunk_size: 104857600,
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
      this.check_size();
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
    },
    check_size() {
      for (let i = 0; i < this.files.length; i++) {
        if (this.files[i].size >= this.chunk_size) {
          this.file_slice(this.files[i], i);
        } else {
          this.file_upload(this.files[i], i);
        }
      }
    },
    file_upload(file, index) {
      const formData = new FormData();
      formData.append("name", file.name);
      formData.append("path", this.get_path());
      formData.append("data", file);

      axios.post(this.upload_api_url, formData).then(() => {
        this.$store.commit("update_progress", {
          item: {
            id: index,
            progress: 100,
          },
        });
      });
    },
    async file_slice(file) {
      const slice_count = Math.ceil(file.size / this.chunk_size);
      const slice_file = [];
      this.chunk_progress = slice_count;

      for (let i = 0; i < slice_count; i++) {
        if (i + 1 === slice_count) {
          slice_file.push({
            name: file.name,
            path: file.path,
            data: file.slice(i * this.chunk_size, file.size),
          });
        } else {
          slice_file.push({
            name: file.name,
            path: file.path,
            data: file.slice(i * this.chunk_size, (i + 1) * this.chunk_size),
          });
        }
      }

      this.chunk_upload(slice_file, slice_count);
    },
    async chunk_upload(file, sl_ct) {
      const slice_tmpname = Math.random().toString(36).slice(-8);
      let finish_chunk = 0;

      await Promise.all(
        file.map(async (f, index) => {
          const formData = new FormData();
          formData.append("name", f.name);
          formData.append("path", this.get_path());
          formData.append("tmp_name", slice_tmpname);
          formData.append("data", f.data);

          try {
            await axios.post(this.upload_api_url, formData);
            console.log(index);
            finish_chunk += 1;

            this.$store.commit("update_progress", {
              item: {
                id: index,
                progress: (finish_chunk / sl_ct) * 100,
              },
            });
          } catch (error) {}
        })
      );
    },
    /* chunk_upload(file, index) {
      const slice_count = Math.ceil(file.size / this.chunk_size);
      const slice_tmpname = Math.random().toString(36).slice(-8);

      for (let i = 0; i < slice_count; i++) {
        let slice_file;

        if (i + 1 === slice_count) {
          slice_file = file.slice(i * this.chunk_size, file.size);
        } else {
          slice_file = file.slice(
            i * this.chunk_size,
            (i + 1) * this.chunk_size
          );
        }

        const formData = new FormData();
        formData.append("name", file.name);
        formData.append("path", this.get_path());
        formData.append("tmp_name", slice_tmpname);
        formData.append("index", i);
        formData.append("data", slice_file);

        axios.post(this.upload_api_url, formData).then(() => {
          this.$store.commit("update_progress", {
            item: {
              id: index,
              progress: (100 / slice_count) * (i + 1),
            },
          });
        });
      }
    }, */
  },
};
</script>
