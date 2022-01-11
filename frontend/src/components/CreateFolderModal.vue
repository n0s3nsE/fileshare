<template>
  <div class="createfolder-modal">
    <div class="createfolder-modal-input">
      <p>フォルダ名</p>
      <input
        type="text"
        placeholder="フォルダ名を入力"
        v-model="new_folder_name"
        @keydown.enter="create_folder"
      />
    </div>
    <div class="createfolder-modal-controll">
      <button class="createfolder-modal-controll-button" @click="create_folder">
        作成
      </button>
      <button
        class="createfolder-modal-controll-button"
        @click="change_status(false)"
      >
        キャンセル
      </button>
    </div>
  </div>
</template>

<script>
import axios from "axios";
import Mixin from "../mixin/mixin";
export default {
  data() {
    return {
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
      this.$store.commit("createfoldermodal_mutation", {
        status: stat,
      });
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

<style scoped>
.createfolder-modal {
  width: 400px;
  border: #545454 1px solid;
  border-radius: 8px;
  background: white;

  top: 50%;
  left: 50%;
  position: absolute;
  transform: translate(-50%, -50%);
}
.createfolder-modal-input {
  padding: 32px 0 32px 16px;
}
.createfolder-modal-input p {
  font-size: 12px;
  margin: 0%;
}
.createfolder-modal-controll {
  border-top: #545454 solid 1px;

  height: 32px;
  display: flex;
  justify-content: flex-end;
  padding: 8px 8px 8px 0px;
}
.createfolder-modal-controll > button {
  margin: 0 0 0 8px;
}
</style>
