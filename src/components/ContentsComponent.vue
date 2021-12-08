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
              <input type="checkbox" @change="all_check()" />
            </th>
            <th>ファイル名</th>
            <th>更新</th>
            <th>サイズ</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="(item, index) in items" :key="index">
            <td>
              <input type="checkbox" @change="change_check(index)" />
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
    };
  },
  mixins: [Mixin],
  mounted() {
    //axios
    //test function
    this.items = [
      {
        name: 'test_item1', //require
        updatedAt: '2021-12-08', //require
        isfolder: false, //require
        size: 1,
        islocked: false, //require
      },
      {
        name: 'test_item2', //require
        updatedAt: '2021-12-08', //require
        isfolder: false, //require
        size: 1,
        islocked: true, //require
      },
      {
        name: 'test_folder', //require
        updatedAt: '2021-12-08', //require
        isfolder: true, //require
        size: 1,
        islocked: false, //require
      },
    ];
    this.current_path = this.get_path() + '/';
  },
  methods: {
    change_check(index) {
      this.items[index].ischecked = !this.items[index].ischecked;
    },
  },
};
</script>
