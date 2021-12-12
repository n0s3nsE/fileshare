<template>
  <div class="contents">
    <div class="list-view">
      <button @click="test()">add</button>
      <table>
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
            <th>ロック</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="(item, index) in items" :key="index">
            <td>
              <input
                type="checkbox"
                @change="select_change()"
                :value="item.id"
                v-model="selected_items"
              />
            </td>
            <td v-if="item.isfolder">
              <a :href="current_path + item.name">
                {{ item.name }}
              </a>
            </td>
            <td v-else>
              <a :href="'/content' + current_path + item.name">
                {{ item.name }}
              </a>
            </td>
            <td>{{ item.updatedAt }}</td>
            <td>{{ item.size }}</td>
            <td>{{ item.islocked }}</td>
          </tr>
          <tr
            v-for="(item, index) in upload_queue"
            :key="'upload-queue-' + index"
          >
            <td></td>
            <td>{{ item.name }}</td>
            <td>{{ item.progress }}</td>
            <td></td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>

<script>
import Mixin from "../mixin/mixin";

export default {
  data() {
    return {
      items: [],
      upload_queue: [],
      current_path: "",
      selected_items: [],
      selected_all: false,
    };
  },
  mixins: [Mixin],
  mounted() {
    this.current_path = this.get_path() + "/";
    this.upload_queue = this.upload_queue_getters;

    this.items = this.itemlist_getters;
    this.get_itemlist(this.current_path);
  },
  computed: {
    itemlist_getters() {
      return this.$store.getters.get_itemlist;
    },
    upload_queue_getters() {
      return this.$store.getters.get_upload_queue;
    },
  },
  watch: {
    itemlist_getters() {
      this.items = this.itemlist_getters;
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
    selected_items: function () {
      this.$store.commit("selected_items_mutation", {
        items: this.selected_items,
      });
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
