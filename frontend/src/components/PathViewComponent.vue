<template>
  <div class="path">
    <span v-for="(folder, index) in sp_current_path" :key="index">
      /
      <a :href="get_url(index)">
        {{ folder }}
      </a>
    </span>
    <span> / {{ current_folder }} </span>
  </div>
</template>

<script>
import Mixin from '../mixin/mixin';

export default {
  data() {
    return {
      current_path: '',
      sp_current_path: [],
      current_folder: '',
    };
  },
  mixins: [Mixin],
  mounted: function () {
    this.current_path = this.get_path();
    this.split_path();
  },
  methods: {
    split_path() {
      this.sp_current_path = this.current_path
        .split('/')
        .filter((x) => x != '');
      this.current_folder =
        this.sp_current_path[this.sp_current_path.length - 1];
      this.sp_current_path.pop();
    },
    get_url(index) {
      let temp = '';
      for (let i = 0; i < index; i++) {
        temp += '/' + this.sp_current_path[i];
      }
      return temp;
    },
  },
};
</script>
