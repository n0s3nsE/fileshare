<template>
  <div class="toolbar-upload">
    <button>
      <label for="file-open">アップロード</label>
      <input
        type="file"
        name="file"
        multiple
        id="file-open"
        @change="file_change()"
        style="display: none"
        ref="upload_files"
      />
    </button>
    <button @click="test()">test</button>
  </div>
</template>
<script>
export default {
  data() {
    return {
      files: [],
    };
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
    },
    test() {
      this.$store.commit("test");
    },
  },
};
</script>
