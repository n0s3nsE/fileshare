import Vue from "vue";
import Vuex from "vuex";

Vue.use(Vuex);
export default new Vuex.Store({
    state: {
        itemList: [],
        uploadQueue: [],
        selectedItems: [],
        toolbarStatus: true,
        notification: [],
        notificationDetailModalStatus: false,
    },
    getters: {
        getItemList: state => {
            return state.itemList;
        },
        getUploadQueue: state => {
            return state.uploadQueue;
        },
        getSelectedItems: state => {
            return state.selectedItems;
        },
        getToolbarStatus: state => {
            return state.toolbarStatus;
        },
        getNotification: state => {
            return state.notification;
        },
        getNotificationDetailModalStatus: state => {
            return state.notificationDetailModalStatus;
        }
    },
    mutations: {
        setItemListMutation(state, payload) {
            state.itemList = payload.items;
        },
        addUploadQueueMutation(state, payload) {
            state.uploadQueue.push(payload.file);
        },
        removeUploadQueueMutation(state, payload) {
            state.uploadQueue.splice(state.uploadQueue.findIndex((e) => e.id == payload.id), 1);
        },
        selectedItemsMutation(state, payload) {
            state.selectedItems = payload.items;
        },
        updateProgressMutation(state, payload){
            for(let i = 0; i < state.uploadQueue.length; i++){
                if(state.uploadQueue[i].id === payload.item.id){
                    state.uploadQueue[i].progress = payload.item.progress;
                } 
            }
        },
        toolbarStatusMutation(state, payload){
            state.toolbarStatus = payload.stat;
        },
        addNotificationMutation(state, payload) {
            if(state.notification.length > 0){
                if(state.notification[0].type !== payload.notification.type){
                    state.notification = [];
                }
            }
            state.notification.push({
                status_code: payload.notification.status_code,
                type: payload.notification.type,
                detail: payload.notification.detail,
            });
        },
        removeNotificationMutation(state) {
            state.notification = [];
        },
        changeNdmsMutation(state, payload) {
            state.notificationDetailModalStatus = payload.status;
        }
    },
});