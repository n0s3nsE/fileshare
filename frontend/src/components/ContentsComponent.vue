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
            <td v-if="item.isfolder" class="list-view-name">
              <div class="list-view-name-icon">
                <svg
                  width="26"
                  height="22"
                  viewBox="0 0 26 22"
                  fill="none"
                  xmlns="http://www.w3.org/2000/svg"
                >
                  <path d="M0 0H10L15 3H26V22H0V0Z" fill="#3C3C3C" />
                </svg>
              </div>
              <div class="list-view-name-link">
                <a :href="current_path + item.name">
                  {{ item.name }}
                </a>
              </div>
              <lock-button
                :item_id="item.id"
                :islocked="item.islocked"
                class="list-view-name-lockbutton"
              />
            </td>
            <td v-else class="list-view-name">
              <div class="list-view-name-icon">
                <svg
                  width="22"
                  height="26"
                  viewBox="0 0 22 26"
                  fill="none"
                  xmlns="http://www.w3.org/2000/svg"
                >
                  <path
                    d="M21.5 2V24C21.5 24.8284 20.8284 25.5 20 25.5H2C1.17157 25.5 0.5 24.8284 0.5 24V2C0.5 1.17157 1.17157 0.5 2 0.5H8.46154H15H20C20.8284 0.5 21.5 1.17157 21.5 2Z"
                    fill="white"
                    stroke="#3C3C3C"
                  />
                  <line x1="4" y1="5.5" x2="18" y2="5.5" stroke="#3C3C3C" />
                  <line x1="4" y1="14.5" x2="18" y2="14.5" stroke="#3C3C3C" />
                  <line x1="4" y1="19.5" x2="18" y2="19.5" stroke="#3C3C3C" />
                  <line x1="4" y1="10.5" x2="18" y2="10.5" stroke="#3C3C3C" />
                </svg>
              </div>
              <div class="list-view-name-link">
                <a
                  :href="
                    '?id=' + item.id + '&type=' + item.name.split('.').pop()
                  "
                >
                  {{ item.name }}
                </a>
              </div>
              <lock-button
                :item_id="item.id"
                :islocked="item.islocked"
                class="list-view-name-lockbutton"
              />
            </td>
            <td class="list-view-updatedat">
              {{ item.updated_at }}
            </td>
            <td v-if="item.size < 1024" class="list-view-size">
              {{ item.size }}KB
            </td>
            <td v-else class="list-view-size">
              {{ Math.round((item.size / 1024) * 10) / 10 }}MB
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
import Mixin from "../mixin/mixin";
import LockButton from "./tools/LockbuttonComponent.vue";
import SidePanel from "./SidePanelComponent.vue";

export default {
  components: {
    LockButton,
    SidePanel,
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

thead {
  background: white;
  border-bottom: #666666 solid 2px;
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

.list-view-name {
  display: flex;
}

.list-view-name-icon {
  margin: auto 8px;
}

.list-view-name-lockbutton {
  margin-left: auto;
  margin-top: auto;
  margin-bottom: auto;
}

.list-view-name-link {
  max-width: 450px;
  overflow: hidden;
  text-overflow: ellipsis;
  line-height: 50px;
}

.list-view-updatedat {
  width: 85px;
  padding: 0px 8px;
}

.list-view-size {
  width: 50px;
  text-align: center;
}

a:link,
a:visited {
  color: #2b2b2b;
  text-decoration: none;
}
</style>
