<template>
  <div class="main">
    <div class="list-contents">
      <div v-if="notExistsFolder">存在しないフォルダ</div>
      <div v-else class="list-view" @dragenter="dragEnter">
        <table>
          <thead>
            <tr>
              <th>
                <input
                  v-if="!emptyFolder"
                  type="checkbox"
                  @change="selectAll()"
                  v-model="selectedAll"
                />
              </th>
              <th @click="sortItemList('name')">ファイル名</th>
              <th @click="sortItemList('update')">更新</th>
              <th @click="sortItemList('size')">サイズ</th>
            </tr>
          </thead>
          <tbody>
            <tr v-if="emptyFolder && uploadQueue.length === 0">
              <td></td>
              <td><div class="empty">空のフォルダ</div></td>
              <td></td>
              <td></td>
            </tr>

            <tr v-else v-for="(item, index) in items" :key="index">
              <td class="list-view-checkbox">
                <div>
                  <input
                    v-if="!item.islocked"
                    type="checkbox"
                    @change="selectChange()"
                    :value="item.id"
                    v-model="selectedItems"
                  />
                </div>
              </td>
              <td v-if="item.isfolder" @click="clickTableCell(item.id)">
                <folder-column :item="item" />
              </td>
              <td v-else @click="clickTableCell(item.id)">
                <file-column :item="item" />
              </td>
              <td class="list-view-updatedat" @click="clickTableCell(item.id)">
                {{ item.updated_at }}
              </td>
              <td class="list-view-size" @click="clickTableCell(item.id)">
                {{ sizeConvert(item.size) }}
              </td>
            </tr>

            <tr
              v-for="(item, index) in uploadQueue"
              :key="'upload-queue-' + index"
            >
              <td>
                <vue-loading
                  type="spin"
                  color="#333"
                  :size="{ width: '22px', height: '22px' }"
                />
              </td>
              <td>{{ item.name }}</td>
              <td></td>
              <td>{{ item.progress }}%</td>
            </tr>
          </tbody>
        </table>

        <div
          v-if="dragStat"
          class="drop-zone"
          :class="[dragStat ? 'drop-zone-enable' : null]"
          @dragleave="dragLeave"
          @dragover.prevent
          @drop.prevent="itemDropped"
        />
      </div>
    </div>
    <info-panel
      v-if="selectedItems.length === 1"
      class="item-info"
      :thisFileId="selectedItems[0].toString()"
      :thum="true"
    />
  </div>
</template>
<script>
import Mixin from "../../mixin/mixin";
import FolderColumn from "./FolderColumnComponent.vue";
import FileColumn from "./FileColumnComponent.vue";
import InfoPanel from "../InfoPanel/InfoPanel.vue";
import { VueLoading } from "vue-loading-template";

export default {
  components: {
    FolderColumn,
    FileColumn,
    InfoPanel,
    VueLoading,
  },
  data() {
    return {
      items: [],
      uploadQueue: [],
      currentPath: "",
      selectedItems: [],
      selectedAll: false,
      emptyFolder: false,
      notExistsFolder: false,
      dragStat: false,
      isAsc: true,
    };
  },
  mixins: [Mixin],
  mounted() {
    this.currentPath = this.getPath();
    this.uploadQueue = this.uploadQueueGetters;
    this.items = this.itemListGetters;
    this.selectedItems = this.selectedItemsGetters;
    this.getItemList(this.currentPath);
  },
  computed: {
    itemListGetters() {
      return this.$store.getters.getItemList;
    },
    uploadQueueGetters() {
      return this.$store.getters.getUploadQueue;
    },
    selectedItemsGetters() {
      return this.$store.getters.getSelectedItems;
    },
  },
  watch: {
    itemListGetters() {
      this.items = this.itemListGetters;
      if (this.items === undefined) {
        this.emptyFolder = true;
      } else if (this.items[0].iserror) {
        this.notExistsFolder = true;
      } else {
        this.emptyFolder = false;
        this.notExistsFolder = false;
      }
      this.selectedAll = false;
    },
    uploadQueue: {
      handler: function (value) {
        this.uploadQueue = value;
        const fi = value.findIndex((e) => e.progress == 100);
        if (fi >= 0) {
          this.getItemList(this.currentPath);
          this.$store.commit("removeUploadQueueMutation", {
            id: value[fi].id,
          });
        }
      },
      deep: true,
    },
    selectedItems() {
      this.$store.commit("selectedItemsMutation", {
        items: this.selectedItems,
      });
    },
    selectedItemsGetters(value) {
      this.selectedItems = value;
    },
  },
  methods: {
    clickTableCell(id) {
      this.selectChange();
      if (this.selectedItems[0] === id) this.selectedItems = [];
      else {
        this.selectedItems = [];
        this.$nextTick(() => {
          this.selectedItems.push(id);
        });
      }
    },
    sortItemList(sortedBy) {
      switch (sortedBy) {
        case "name":
          this.items = this.isAsc
            ? this.mixinSortItemList(this.items, "name", false)
            : this.mixinSortItemList(this.items, "name", true);
          this.isAsc = !this.isAsc;
          break;
        case "update":
          this.items = this.isAsc
            ? this.mixinSortItemList(this.items, "update", false)
            : this.mixinSortItemList(this.items, "update", true);
          this.isAsc = !this.isAsc;
          break;
        case "size":
          this.items = this.isAsc
            ? this.mixinSortItemList(this.items, "size", false)
            : this.mixinSortItemList(this.items, "size", true);
          this.isAsc = !this.isAsc;
          break;
        default:
          return;
      }
    },
    selectAll() {
      if (this.selectedAll) {
        this.selectedItems = this.items
          .filter((item) => !item.islocked)
          .map((item) => item.id);
      } else {
        this.selectedItems = [];
      }
    },
    selectChange() {
      if (this.selectedAll) {
        this.selectedAll = false;
      }
    },
    sizeConvert(size) {
      if (size >= 1024) {
        return Math.round((size / 1024) * 10) / 10 + "MB";
      } else {
        return size + "KB";
      }
    },
    dragEnter() {
      this.dragStat = true;
    },
    dragLeave() {
      this.dragStat = false;
    },
    itemDropped(e) {
      this.mixinUpload(e.dataTransfer.files);
      this.dragStat = false;
    },
  },
};
</script>

<style>
.main {
  height: calc(100vh - 107px);
  width: 100%;
  display: flex;
}

table {
  width: 100%;
  border-collapse: collapse;
  empty-cells: hide;
}

thead th {
  background: #f5f5f5;
  position: sticky;
  top: 0px;
}

thead th:hover {
  background: #e1e1e1;
}

tr {
  height: 50px;
  border-bottom: #666666 solid 1px;
}

.list-contents {
  height: 100%;
  width: 75%;
  overflow-y: scroll;
  scrollbar-width: none;
  white-space: nowrap;
}

.list-view {
  height: 100%;
  width: 100%;
  position: relative;
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

.item-info {
  height: calc(100vh - 107px);
  width: 25%;
}

.empty {
  display: flex;
  justify-content: center;
  line-height: 50px;
}

.drop-zone {
  height: 100%;
  width: 100%;
  top: 0px;

  position: absolute;
}

.drop-zone-enable {
  background: #22222244;
}
</style>
