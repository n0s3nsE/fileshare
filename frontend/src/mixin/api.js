import axios from "axios";

export default {
  data() {
    const apiBaseURL = process.env.VUE_APP_API_BASE_URL;
    return {
      contentDetailAPI: apiBaseURL + "/detail",
      createAPI: apiBaseURL + "/create",
      deleteAPI: apiBaseURL + "/delete",
      lockAPI: apiBaseURL + "/lock",
      renameAPI: apiBaseURL + "/rename",
      showAPI: apiBaseURL + "/show",
      uploadAPI: apiBaseURL + "/upload",

      axiosConfig: {
        timeout: 10000,
        headers: {},
      },
    };
  },
  methods: {
    showFolderAPI(folder) {
      return axios.get(this.showAPI + folder, this.axiosConfig);
    },
    getDetailAPI(contentId) {
      return axios.get(
        this.contentDetailAPI + "/" + contentId,
        this.axiosConfig
      );
    },
    fileUploadAPI(formData) {
      return axios.post(this.uploadAPI, formData);
    },
    sendEndchunkFlagAPI(name, tmp_name, path) {
      return axios.post(
        this.uploadAPI,
        {
          name: name,
          tmp_name: tmp_name,
          path: path,
          endflag: true,
        },
        this.axiosConfig
      );
    },
    sendLockSignalAPI() {
      return axios.get(this.lockAPI + "/" + this.itemId, this.axiosConfig);
    },
    createFolderAPI(newFolderName, currentPath) {
      return axios.post(
        this.createAPI,
        {
          new_folder_name: newFolderName,
          path: currentPath,
        },
        this.axiosConfig
      );
    },
    deleteItemsAPI(selectedItems) {
      return axios.post(
        this.deleteAPI,
        {
          delete_items: selectedItems,
        },
        this.axiosConfig
      );
    },
    renameItemAPI(selectedItem, newName) {
      return axios.post(
        this.renameAPI,
        {
          id: selectedItem,
          new_name: newName,
        },
        this.axiosConfig
      );
    },
  },
};
