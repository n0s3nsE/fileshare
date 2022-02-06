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
      <span v-for="(folder, index) in spCurrentFolder" :key="index">
        /
        <a :href="folder.url">
          {{ folder.name }}
        </a>
      </span>
      <span> / {{ currentFolder }} </span>
    </div>
  </div>
</template>

<script>
import Mixin from "../../mixin/mixin";

export default {
  data() {
    return {
      currentPath: "",
      spCurrentFolder: [],
      currentFolder: "",
    };
  },
  mixins: [Mixin],
  mounted() {
    this.splitPath(this.getPath());
  },
  methods: {
    splitPath(currentPath) {
      const spPath = currentPath.split("/").filter((x) => x != "");
      this.currentFolder = spPath[spPath.length - 1];
      spPath.pop();

      for (let i = 0; i < spPath.length; i++) {
        let tmp = "";
        for (let ii = 0; ii < i + 1; ii++) {
          tmp = tmp + "/" + spPath[ii];
          this.spCurrentFolder[i] = {
            name: spPath[i],
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
