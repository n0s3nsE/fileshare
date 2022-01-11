<template>
  <div class="path">
    <span>
      /
      <a href="/">home</a>
    </span>
    <span v-for="(folder, index) in sp_current_path" :key="index">
      /
      <a :href="folder.url">
        {{ folder.name }}
      </a>
    </span>
    <span> / {{ current_folder }} </span>
  </div>
</template>

<script>
import Mixin from "../mixin/mixin";

export default {
  data() {
    return {
      current_path: "",
      sp_current_path: [],
      current_folder: "",
    };
  },
  mixins: [Mixin],
  mounted() {
    this.split_path(this.get_path());
  },
  methods: {
    split_path(current_path) {
      const sp_path = current_path.split("/").filter((x) => x != "");
      this.current_folder = sp_path[sp_path.length - 1];
      sp_path.pop();

      for (let i = 0; i < sp_path.length; i++) {
        let tmp = "";
        for (let ii = 0; ii < i + 1; ii++) {
          tmp = tmp + "/" + sp_path[ii];
          this.sp_current_path[i] = {
            name: sp_path[i],
            url: tmp,
          };
        }
      }
    },
  },
};
</script>
<style scoped>
.path {
  padding: 8px 0;

  font-size: 24px;
}

a:link,
a:visited {
  color: #545454;
  text-decoration: none;
}
</style>
