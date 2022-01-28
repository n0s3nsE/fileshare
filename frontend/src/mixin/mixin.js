import axios from "axios";

export default {
  data() {
    return {
      show_api_url: "http://127.0.0.1:8000/api/show",
      files: [],
      upload_api_url: "http://127.0.0.1:8000/api/upload",
      chunk_size: 104857600,
      axios_headers: {
        "Access-Control-Allow-Origin": "*",
      },
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
      

    },

    mixin_upload(files) {
      this.add_upload_queue(files);
      this.check_size(files);
    },
    add_upload_queue(files) {
      for (let i = 0; i < files.length; i++) {
        this.$store.commit("add_upload_queue_mutation", {
          file: {
            id: i,
            name: files[i].name,
            progress: 0,
          },
        });
      }
    },
    check_size(files) {
      for (let i = 0; i < files.length; i++) {
        if (files[i].size >= this.chunk_size) {
          this.file_slice(files[i], i);
        } else {
          this.file_upload(files[i], i);
        }
      }
    },
    file_upload(file, index) {
      const formData = new FormData();
      formData.append("name", file.name);
      formData.append("path", this.get_path());
      formData.append("data", file);

      axios.post(this.upload_api_url, formData).then((response) => {
        this.$store.commit("update_progress", {
          item: {
            id: index,
            progress: 100,
          },
        });
        
        this.add_notification(response.status, "アップロード成功");
      }).catch((error) => {
        this.add_notification(error.response.status, error.response.data.msg);
      });
    },
    file_slice(file, index) {
      const slice_count = Math.ceil(file.size / this.chunk_size);
      const slice_file = [];

      for (let i = 0; i < slice_count; i++) {
        if (i + 1 === slice_count) {
          slice_file.push({
            name: file.name,
            path: file.path,
            data: file.slice(i * this.chunk_size, file.size),
          });
        } else {
          slice_file.push({
            name: file.name,
            path: file.path,
            data: file.slice(i * this.chunk_size, (i + 1) * this.chunk_size),
          });
        }
      }

      this.chunk_upload(slice_file, slice_count, index);
    },
    async chunk_upload(file, sl_ct, index) {
      const slice_tmpname = Math.random().toString(36).slice(-8);
      let finish_chunk = 0;

      await Promise.all(
        file.map(async (f, i) => {
          const formData = new FormData();
          formData.append("name", f.name);
          formData.append("path", this.get_path());
          formData.append("tmp_name", slice_tmpname);
          formData.append("index", i);
          formData.append("data", f.data);

            await axios.post(this.upload_api_url, formData)
            .then(() => {
              finish_chunk += 1;
              this.$store.commit("update_progress", {
                item: {
                  id: index,
                  progress: Math.floor((finish_chunk / sl_ct) * 100) - 1,
                },
              })
            }).catch((error) => {
              this.add_notification(error.response.status, error.response.data.detail);
            });
        })
      );
      this.send_endchunk_flag(file[0].name, slice_tmpname, index);
    },
    async send_endchunk_flag(name, tmp_name, index) {
        await axios.post(
          this.upload_api_url,
          {
            name: name,
            tmp_name: tmp_name,
            path: this.get_path(),
            endflag: true,
          },
          this.axios_headers
        )
        .then((response) => {
        this.$store.commit("update_progress", {
          item: {
            id: index,
            progress: 100,
          },
        });
        this.add_notification(response.status, "アップロード成功");
        })
        .catch((error) => {
          this.add_notification(error.response.status, error.response.data.detail);
        });
    },
    add_notification(status_code, detail=undefined) {
      this.$store.commit("add_notification_mutation", {
        notification: {
          status_code: status_code,
          detail: detail,
        },
      });
    }
  },
};
