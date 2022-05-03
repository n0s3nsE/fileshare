import apiMixin from "./api";

export default {
  data() {
    return {
      files: [],
      chunkSize: 104857600,
    };
  },
  mixins: [apiMixin],
  methods: {
    getPath() {
      return decodeURI(window.location.pathname);
    },
    getItemList(folder) {
      folder = folder.replace(/\/*$/, "");
      this.showFolderAPI(folder)
        .then((response) => {
          this.setItemList(response.data.itemlist);
        })
        .catch((error) => {
          if (error.code === "ECONNABORTED")
            this.addNotification(408, "show", "timeout.");
          else {
            this.setItemList([
              {
                id: 0,
                name: error.response.data.msg,
                iserror: true,
              },
            ]);
            this.$store.commit("toolbarStatusMutation", {
              stat: false,
            });
          }
        });

      this.$store.commit("selectedItemsMutation", {
        items: [],
      });
    },
    setItemList(items) {
      if (items) {
        items = this.convertDt(items);
        items = this.mixinSortItemList(items, "name", true);
      }

      this.$store.commit("setItemListMutation", {
        items: items,
      });
    },
    async getDetail(contentId) {
      const gt = await this.getDetailAPI(contentId)
        .then((response) => {
          return response.data.detail;
        })
        .catch((error) => {
          if (error.code === "ECONNABORTED")
            this.addNotification(408, "getDetail", "timeout.");
          else
            this.addNotification(
              error.status_code,
              "getDetail",
              error.response.data.detail
            );
        });
      return this.convertDt([gt])[0];
    },
    convertDt(dt) {
      return dt.map((i) => {
        const t = new Date(Date.parse(i.updated_at));
        const updated_at =
          t.getFullYear() +
          "/" +
          (t.getMonth() + 1) +
          "/" +
          t.getDate() +
          " " +
          t.getHours() +
          ":" +
          t.getMinutes() +
          ":" +
          t.getMinutes();
        return {
          id: i.id,
          name: i.name,
          size: i.size,
          isfolder: i.isfolder,
          islocked: i.islocked,
          updated_at: updated_at,
        };
      });
    },
    mixinSortItemList(items, sortBy, isAsc) {
      if (!sortBy) return items;

      switch (sortBy) {
        case "name":
          items.sort((a, b) => {
            const nameA = a.name.toUpperCase();
            const nameB = b.name.toUpperCase();
            if (isAsc) {
              return nameA < nameB ? -1 : nameA > nameB ? 1 : 0;
            } else {
              return nameA > nameB ? -1 : nameA < nameB ? 1 : 0;
            }
          });
          break;
        case "update":
          items.sort((a, b) => {
            const updatedA = a.updated_at;
            const updatedB = b.updated_at;
            if (isAsc) {
              return updatedA < updatedB ? -1 : updatedA > updatedB ? 1 : 0;
            } else {
              return updatedA > updatedB ? -1 : updatedA < updatedB ? 1 : 0;
            }
          });
          break;
        case "size":
          items.sort((a, b) => {
            const sizeA = a.size;
            const sizeB = b.size;
            if (isAsc) {
              return sizeA < sizeB ? -1 : sizeA > sizeB ? 1 : 0;
            } else {
              return sizeA > sizeB ? -1 : sizeA < sizeB ? 1 : 0;
            }
          });
          break;
        default:
          return;
      }

      return items;
    },

    mixinUpload(files) {
      this.addUploadQueue(files);
      this.checkSize(files);
    },
    addUploadQueue(files) {
      for (let i = 0; i < files.length; i++) {
        this.$store.commit("addUploadQueueMutation", {
          file: {
            id: i,
            name: files[i].name,
            progress: 0,
          },
        });
      }
    },
    checkSize(files) {
      for (let i = 0; i < files.length; i++) {
        if (files[i].size >= this.chunkSize) {
          this.fileSlice(files[i], i);
        } else {
          this.fileUpload(files[i], i);
        }
      }
    },
    fileUpload(file, index) {
      const formData = new FormData();
      formData.append("name", file.name);
      formData.append("path", this.getPath());
      formData.append("data", file);

      this.fileUploadAPI(formData)
        .then((response) => {
          this.$store.commit("updateProgressMutation", {
            item: {
              id: index,
              progress: 100,
            },
          });

          this.addNotification(response.status, "upload");
        })
        .catch((error) => {
          this.addNotification(
            error.response.status,
            "upload",
            error.response.data.detail
          );
        });
    },
    fileSlice(file, index) {
      const sliceCount = Math.ceil(file.size / this.chunkSize);
      const sliceFile = [];

      for (let i = 0; i < sliceCount; i++) {
        if (i + 1 === sliceCount) {
          sliceFile.push({
            name: file.name,
            path: file.path,
            data: file.slice(i * this.chunkSize, file.size),
          });
        } else {
          sliceFile.push({
            name: file.name,
            path: file.path,
            data: file.slice(i * this.chunkSize, (i + 1) * this.chunkSize),
          });
        }
      }

      this.chunkUpload(sliceFile, sliceCount, index);
    },
    async chunkUpload(file, sl_ct, index) {
      const sliceTmpname = Math.random().toString(36).slice(-8);
      let finishChunk = 0;

      await Promise.all(
        file.map(async (f, i) => {
          const formData = new FormData();
          formData.append("name", f.name);
          formData.append("path", this.getPath());
          formData.append("tmp_name", sliceTmpname);
          formData.append("index", i);
          formData.append("data", f.data);

          await this.fileUploadAPI(formData)
            .then(() => {
              finishChunk += 1;
              this.$store.commit("updateProgressMutation", {
                item: {
                  id: index,
                  progress: Math.floor((finishChunk / sl_ct) * 100) - 1,
                },
              });
            })
            .catch((error) => {
              this.addNotification(
                error.response.status,
                "upload",
                error.response.data.detail
              );
            });
        })
      );
      this.sendEndchunkFlag(file[0].name, sliceTmpname, index);
    },
    async sendEndchunkFlag(name, tmp_name, index) {
      await this.sendEndchunkFlagAPI(name, tmp_name, this.getPath())
        .then((response) => {
          this.$store.commit("updateProgressMutation", {
            item: {
              id: index,
              progress: 100,
            },
          });
          this.addNotification(response.status, "upload");
        })
        .catch((error) => {
          if (error.code === "ECONNABORTED")
            this.addNotification(408, "upload", `'${name}' timeout.`);
          this.addNotification(
            error.response.status,
            "upload",
            error.response.data.detail
          );
        });
    },
    addNotification(status_code, type, detail = undefined) {
      this.$store.commit("addNotificationMutation", {
        notification: {
          status_code: status_code,
          type: type,
          detail: detail,
        },
      });
    },
  },
};
