<template>
  <div class="path">
    <div>
      <svg
        class="path-folder-icon"
        width="26"
        height="22"
        viewBox="0 0 26 22"
        fill="none"
        xmlns="http://www.w3.org/2000/svg"
      >
        <path d="M0 0H10L15 3H26V22H0V0Z" fill="#eeeeee" />
      </svg>
      <span>
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

<style>
.path {
  display: flex;
  justify-content: center;
  align-items: center;
  height: 40px;
  width: 100%;
  font-size: 24px;

  color: #eeeeee;
  background: #222222;
}

.path-folder-icon {
  margin-right: 8px;
}

.path a:link,
.path a:visited {
  color: #e6e6e6;
  text-decoration: none;
}
</style>
