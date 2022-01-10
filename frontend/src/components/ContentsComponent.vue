<template>
  <div class="contents">
    <div class="list-view">
      <div v-if="empty_folder">
        <span v-if="upload_queue.length === 0">空のフォルダ</span>
        <table v-if="upload_queue.length > 0">
          <thead>
            <tr>
              <th></th>
              <th>ファイル名</th>
              <th>更新</th>
              <th>サイズ</th>
            </tr>
          </thead>
          <tbody>
            <tr
              v-for="(item, index) in upload_queue"
              :key="'upload-queue-' + index"
            >
              <td></td>
              <td>{{ item.name }}</td>
              <td>{{ item.progress }}</td>
            </tr>
          </tbody>
        </table>
      </div>
      <div v-else-if="not_exists_folder">存在しないフォルダ</div>
      <table v-else>
        <thead>
          <tr>
            <th>
              <input
                type="checkbox"
                @change="select_all()"
                v-model="selected_all"
              />
            </th>
            <th>ファイル名</th>
            <th>更新</th>
            <th>サイズ</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="(item, index) in items" :key="index">
            <td>
              <input
                v-if="!item.islocked"
                type="checkbox"
                @change="select_change()"
                :value="item.id"
                v-model="selected_items"
              />
            </td>
            <td v-if="item.isfolder">
              <div>
                <a :href="current_path + item.name">
                  {{ item.name }}
                </a>
                <rename-text-box v-if="rename_flag === item.id" />
              </div>
              <lock-button :item_id="item.id" />
            </td>
            <td v-else>
              <div>
                <a
                  :href="
                    '?view=' + item.id + '&ext=' + item.name.split('.').pop()
                  "
                >
                  {{ item.name }}
                </a>
                <rename-text-box v-if="rename_flag === item.id" />
              </div>
              <lock-button :item_id="item.id" />
            </td>
            <td>{{ item.updated_at }}</td>
            <td v-if="item.size < 1024">{{ item.size }}KB</td>
            <td v-else>{{ Math.round((item.size / 1024) * 10) / 10 }}MB</td>
          </tr>
          <tr
            v-for="(item, index) in upload_queue"
            :key="'upload-queue-' + index"
          >
            <td></td>
            <td>{{ item.name }}</td>
            <td>{{ item.progress }}</td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>
<script>
import Mixin from "../mixin/mixin";
import RenameTextBox from "./tools/RenameComponent.vue";
import LockButton from "./tools/LockbuttonComponent.vue";
export default {
  components: {
    RenameTextBox,
    LockButton,
  },
  data() {
    return {
      items: [],
      upload_queue: [],
      current_path: "",
      selected_items: [],
      selected_all: false,
      rename_flag: null,
      empty_folder: false,
      not_exists_folder: false,
    };
  },
  mixins: [Mixin],
  mounted() {
    if (this.get_path() === "/") {
      this.current_path = this.get_path();
    } else {
      this.current_path = this.get_path() + "/";
    }
    this.upload_queue = this.upload_queue_getters;
    this.items = this.itemlist_getters;
    this.selected_items = this.selected_items_getters;
    this.get_itemlist(this.current_path);
  },
  computed: {
    itemlist_getters() {
      return this.$store.getters.get_itemlist;
    },
    upload_queue_getters() {
      return this.$store.getters.get_upload_queue;
    },
    rename_flag_getters() {
      return this.$store.getters.get_rename_flag;
    },
    selected_items_getters() {
      return this.$store.getters.get_selected_items;
    },
  },
  watch: {
    itemlist_getters() {
      this.items = this.itemlist_getters;
      if (this.items === undefined) {
        this.empty_folder = true;
      } else if (this.items[0].iserror) {
        this.not_exists_folder = true;
      } else {
        this.empty_folder = false;
        this.not_exists_folder = false;
      }
      this.selected_all = false;
    },
    upload_queue: {
      handler: function (value) {
        this.upload_queue = value;
        const fi = value.findIndex((e) => e.progress == 100);
        if (fi >= 0) {
          //upload completed
          this.get_itemlist(this.current_path);
          this.$store.commit("remove_upload_queue_mutation", {
            id: value[fi].id,
          });
        }
      },
      deep: true,
    },
    selected_items() {
      this.$store.commit("selected_items_mutation", {
        items: this.selected_items,
      });
    },
    selected_items_getters(value) {
      this.selected_items = value;
    },
    rename_flag_getters(value) {
      this.rename_flag = value;
    },
  },
  methods: {
    select_all() {
      if (this.selected_all) {
        this.selected_items = this.items.map((a) => a.id);
      } else {
        this.selected_items = [];
      }
    },
    select_change() {
      if (this.selected_all) {
        this.selected_all = false;
      }
    },
  },
};
</script>
