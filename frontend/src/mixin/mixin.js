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
    
    get_itemlist(folder) {
      folder = folder.replace(/\/*$/, "");
      
      axios.get(this.show_api_url + folder)
        .then((response) => { 
          if(response.data.itemlist)
          {
            this.set_itemlist(this.convart_dt(response.data.itemlist));
          }
          else
          {
            this.set_itemlist(response.data.itemlist);
          }
        })
        .catch((error) => {
          console.log(error);
          this.set_itemlist([{
            id: 0,
            name: error.response.data.msg,
            iserror: true
          }]);
          this.$store.commit("toolbar_status_mutation", {
            stat: false,
          });
        });

        this.$store.commit("selected_items_mutation", {
          items: [],
        })
    },
    set_itemlist(items) {
      this.$store.commit("set_itemlist_mutation", {
        items: items
      });
    },
    convart_dt(dt){
      return dt.map(i => {
        const t = new Date(Date.parse(i.updated_at));
        const updated_at = t.getFullYear() + "/" + (t.getMonth() + 1) + "/" + t.getDate() + " " + t.getHours() + ":" + t.getMinutes() +":" + t.getMinutes();
        return {
          id: i.id,
          name: i.name,
          size: i.size,
          isfolder: i.isfolder,
          islocked: i.islocked,
          updated_at: updated_at
        }
      });
      

    }
  },
};
