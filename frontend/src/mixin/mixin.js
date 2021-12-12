import axios from "axios";

export default {
  data() {
    return {
      api_url: "http://127.0.0.1:8000/api",
      response_data: []
    }
  },
  methods: {
    get_path() {
      return decodeURI(window.location.pathname);
    },
    //api
    get_itemlist(folder) {
      axios.get(this.api_url + "/show" + folder)
        .then(function(response){
          this.set_itemlist(response.data.itemlist);
        }.bind(this));
    },
    set_itemlist(items) {
      this.$store.commit("set_itemlist_mutation", {
        items: items
      });
    }
  },
};
