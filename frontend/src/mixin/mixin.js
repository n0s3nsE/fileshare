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
      folder = folder.replace(/\/*$/, "");
      
      axios.get(this.show_api_url + folder)
        .then(function(response){
          this.set_itemlist(response.data.itemlist);
        }.bind(this))
        .catch(function(error){
          this.set_itemlist([{
            id: 0,
            name: error.response.data.msg,
            iserror: true
          }]);
          this.$store.commit("toolbar_status_mutation", {
            stat: false,
          });
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
