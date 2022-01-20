<template>
  <div class="toolbar-create">
    <div>
      <button @click="change_status(true)">作成</button>
    </div>
    <div v-if="cfm_status" class="create-folder-modal">
      <div class="modal-main">
        <input
          type="text"
          placeholder="フォルダ名を入力"
          v-model="new_folder_name"
          @keydown.enter="create_folder"
        />
      </div>
      <div class="modal-ctl">
        <button @click="create_folder">作成</button>
        <button @click="change_status(false)">キャンセル</button>
      </div>
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
      this.new_folder_name = "";
    },
    async create_folder() {
      await axios.post(this.create_api_url, {
        new_folder_name: this.new_folder_name,
        path: this.current_path,
      });
      await this.get_itemlist(this.current_path);
      this.change_status(false);
    },
  },
};
</script>

<style>
.create-folder-modal {
  background-color: white;
  width: 300px;
  height: 200px;
  border: 1px solid;
  border-radius: 5px;

  position: absolute;
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%);
  padding: 0;
}
.modal-main {
  height: 75%;
  display: flex;
  justify-content: center;
  align-items: center;
}
.modal-ctl {
  display: flex;
  align-items: center;
  justify-content: end;

  height: 25%;
  background-color: #eeeeee;
  border-top: 1px solid;

  position: absolute;
  bottom: 0;
  right: 0;
  left: 0;
}
.modal-ctl button {
  float: right;
}
</style>
