import Vue from "vue";
import Vuex from "vuex";

Vue.use(Vuex);
export default new Vuex.Store({
    state: {
        itemlist: [],
        upload_queue: [],
        selected_items: [],
        toolbar_status: true,
        notification: [],
    },
    getters: {
        get_itemlist: state => {
            return state.itemlist;
        },
        get_upload_queue: state => {
            return state.upload_queue;
        },
        get_selected_items: state => {
            return state.selected_items;
        },
        get_toolbar_status: state => {
            return state.toolbar_status;
        },
        get_notification: state => {
            return state.notification;
        }
    },
    mutations: {
        set_itemlist_mutation(state, payload) {
            state.itemlist = payload.items;
        },
        add_upload_queue_mutation(state, payload) {
            state.upload_queue.push(payload.file);
        },
        remove_upload_queue_mutation(state, payload) {
            state.upload_queue.splice(state.upload_queue.findIndex((e) => e.id == payload.id), 1);
        },
        selected_items_mutation(state, payload) {
            state.selected_items = payload.items;
        },
        update_progress(state, payload){
            for(let i = 0; i < state.upload_queue.length; i++){
                if(state.upload_queue[i].id === payload.item.id){
                    state.upload_queue[i].progress = payload.item.progress;
                } 
            }
        },
        toolbar_status_mutation(state, payload){
            state.toolbar_status = payload.stat;
        },
        add_notification_mutation(state, payload) {
            state.notification.push({
                status_code: payload.notification.status_code,
                detail: payload.notification.detail,
            });
        },
        remove_notification_mutation(state) {
            state.notification = [];
        }
    },
});