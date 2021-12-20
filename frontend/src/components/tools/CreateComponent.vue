<template>
  <div class="toolbar-create">
    <div>
      <button @click="change_status(true)">作成</button>
    </div>
    <div v-if="cfm_status" class="create-folder-modal">
      <input
        type="text"
        placeholder="フォルダ名を入力"
        v-model="new_folder_name"
      />
      <button @click="create_folder">作成</button>
      <button @click="change_status(false)">キャンセル</button>
    </div>
  </div>
</template>

<script>
import axios from "axios";
import Mixin from "../../mixin/mixin";
export default {
  data() {
    return {
      cfm_status: false,
      new_folder_name: "",
      current_path: "",
      create_api_url: "http://127.0.0.1:8000/api/create",
    };
  },
  mixins: [Mixin],
  mounted() {
    this.current_path = this.get_path();
  },
  methods: {
    change_status(stat) {
      this.cfm_status = stat;
    },
    async create_folder() {
      await axios.post(this.create_api_url, {
        new_folder_name: this.new_folder_name,
        path: this.current_path,
      });
      await this.get_itemlist(this.current_path);
    },
  },
};
</script>
