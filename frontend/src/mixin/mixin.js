import axios from "axios";

export default {
  data() {
    return {
      show_api_url: "http://127.0.0.1:8000/api/show",
    }
  },
  methods: {
    get_path() {
      return decodeURI(window.location.pathname);
    },
    //api
    get_itemlist(folder) {
      axios.get(this.show_api_url + folder)
        .then(function(response){
          this.set_itemlist(response.data.itemlist);
        }.bind(this));
        this.$store.commit("selected_items_mutation", {
          items: [],
        })
    },
    set_itemlist(items) {
      this.$store.commit("set_itemlist_mutation", {
        items: items
      });
    }
  },
};
