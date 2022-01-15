<template>
  <div>
    <header class="preview-header">
      <div><button>←</button></div>
      <div>
        <p>{{ this_file.name }}</p>
      </div>
    </header>
    <div v-if="!error_flag" class="preview-main">
      <div class="preview">
        <img
          v-if="file_type.img.indexOf(param_type) > -1"
          :src="view_api_url + '/' + param_id"
        />
        <video
          v-else-if="file_type.video.indexOf(param_type) > -1"
          :src="view_api_url + '/' + param_id"
        />
        <div v-else>
          <p>プレビュー非対応なファイル形式</p>
          <a :href="view_api_url + '/' + param_id">Download</a>
        </div>
      </div>
      <side-panel :this_file_id="param_id" />
    </div>
    <div v-else class="preview-main">不正なパラメーター</div>
    <div class="preview-controller">
      <a
        v-if="before_item"
        :href="'?id=' + before_item.id + '&type=' + before_item.type"
        >←</a
      >
      <a
        v-if="next_item"
        :href="'?id=' + next_item.id + '&type=' + next_item.type"
        >→</a
      >
    </div>
  </div>
</template>
<script>
import Mixin from "../../mixin/mixin";
import SidePanel from "../SidePanelComponent.vue";

export default {
  props: ["param"],
  data() {
    return {
      param_id: null,
      view_api_url: "http://127.0.0.1:8000/api/preview",
      file_type: {
        video: ["mp4", "avi", "wav", "mov", "wmv"],
        img: ["jpg", "jpeg", "png", "bmp", "gif", "svg"],
      },
      this_file: "",
      next_item: null,
      before_item: null,
      error_flag: false,
    };
  },
  components: {
    SidePanel,
  },
  mixins: [Mixin],
  computed: {
    itemlist_getters() {
      return this.$store.getters.get_itemlist;
    },
  },
  created() {
    const split_param = this.split_param(this.param);
    this.param_id = split_param.id;
    this.param_type = split_param.type;

    if (
      typeof this.param_id !== "undefined" &&
      typeof this.param_type !== "undefined"
    ) {
      this.get_itemlist(this.get_path());
    } else {
      this.error_flag = true;
    }
  },
  watch: {
    itemlist_getters() {
      this.create_itemlist(this.itemlist_getters);
    },
  },
  methods: {
    split_param(param) {
      const sp_param = param.split("&").map((i) => i.split("="));
      return Object.fromEntries(sp_param);
    },
    create_itemlist(items) {
      const items_length = items.length;
      const filter_id = (item, index) => {
        if (item.id === parseInt(this.param_id)) {
          this.this_file = item;

          if (index - 1 >= 0) {
            this.before_item = {
              id: items[index - 1].id,
              type: item.name.split(".").pop(),
            };
          }
          if (index + 1 < items_length) {
            this.next_item = {
              id: items[index + 1].id,
              type: item.name.split(".").pop(),
            };
          }
        } else {
          return false;
        }
      };
      items.filter(filter_id);
    },
  },
};
</script>
