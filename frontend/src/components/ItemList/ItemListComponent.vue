<template>
  <div class="main">
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
            <td class="list-view-checkbox">
              <div>
                <input
                  v-if="!item.islocked"
                  type="checkbox"
                  @change="select_change()"
                  :value="item.id"
                  v-model="selected_items"
                />
              </div>
            </td>
            <td v-if="item.isfolder">
              <folder-column :item="item" />
            </td>
            <td v-else :item="item">
              <file-column :item="item" />
            </td>
            <td class="list-view-updatedat">
              {{ item.updated_at }}
            </td>
            <td class="list-view-size">
              {{ size_convert(item.size) }}
            </td>
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
    <side-panel
      v-if="selected_items.length === 1"
      :this_file_id="selected_items[0]"
      :thum="true"
    />
  </div>
</template>
<script>
import Mixin from "../../mixin/mixin";
import FolderColumn from "./FolderColumnComponent.vue";
import FileColumn from "./FileColumnComponent.vue";

export default {
  components: {
    FolderColumn,
    FileColumn,
  },
  data() {
    return {
      items: [],
      upload_queue: [],
      current_path: "",
      selected_items: [],
      selected_all: false,
      empty_folder: false,
      not_exists_folder: false,
    };
  },
  mixins: [Mixin],
  mounted() {
    this.current_path = this.get_path();
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
    size_convert(size) {
      if (size >= 1024) {
        return Math.round((size / 1024) * 10) / 10 + "MB";
      } else {
        return size + "KB";
      }
    },
  },
};
</script>

<style>
.main {
  height: calc(100vh - 96px);
  width: 100%;
  display: flex;
}

table {
  width: 100%;
  border-collapse: collapse;
}

thead th {
  background: rgb(245, 245, 245);
  position: sticky;
  top: 0px;
}

tr {
  height: 50px;
  border-bottom: #666666 solid 1px;
}

.list-view {
  height: 100%;
  width: 75%;
  overflow-y: scroll;
  white-space: nowrap;
}

.list-view-checkbox div {
  display: flex;
  justify-content: center;
}

.list-view-updatedat {
  width: 85px;
  padding: 0px 8px;
}

.list-view-size {
  width: 50px;
  text-align: center;
}

.list-view-name-link a:link,
.list-view-name-link a:visited {
  color: #2b2b2b;
  text-decoration: none;
}
</style>
