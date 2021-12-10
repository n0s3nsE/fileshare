<template>
  <div class="contents">
    <div class="toolbar">
      <button>upload</button>
    </div>
    <div class="list-view">
      <table>
        <thead>
          <tr>
            <th>
              <input type="checkbox" @change="select_all()" v-model="selected_all" />
            </th>
            <th>ファイル名</th>
            <th>更新</th>
            <th>サイズ</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="(item, index) in items" :key="index">
            <td>
              <input type="checkbox" @change="select_change()" :value="item.id" v-model="selected_items" />
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
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>

<script>
import Mixin from '../mixin/mixin';

export default {
  data() {
    return {
      items: [],
      uploading_items: [],
      current_path: '',
      selected_items: [],
      selected_all: false,
    };
  },
  mixins: [Mixin],
  mounted() {
    //axios
    //test data
    const receive_data = [
      {
        id: 1, //require, PK
        name: 'test_item1', //require
        updatedAt: '2021-12-08', //require
        size: 1,
        isfolder: false, //require
        islocked: false, //require
      },
      {
        id: 2,
        name: 'test_item2', 
        updatedAt: '2021-12-08',
        size: 1,
        isfolder: false,
        islocked: true,
      },
      {
        id: 3,
        name: 'test_folder',
        updatedAt: '2021-12-08',
        size: null,
        isfolder: true,
        islocked: false,
      },
    ];
    this.items = receive_data;

    this.current_path = this.get_path();
  },
  methods: {
    select_all() {
      if(this.selected_all) {
        this.selected_items = this.items.map((a) => a.id);
      }
      else {
        this.selected_items = [];
      }
    },
    select_change() {
      if(this.selected_all) {
        this.selected_all = false;
      }
    }
  },
};
</script>
