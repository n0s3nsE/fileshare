import Vue from "vue";
import Vuex from "vuex";

Vue.use(Vuex);
export default new Vuex.Store({
    state: {
        /*
        itemlist: {
            id: int, //require, PK
            name: string, //require
            updatedAt: datetime, //require
            size: int,
            isfolder: boolean, //require
            islocked: boolean, //require
        },
        upload_queue: {
            id: int,
            name: string,
            progress: int
        }
        */
        itemlist: [],
        upload_queue: [],
        selected_items: [],
        rename_flag: null,
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
        get_rename_flag: state => {
            return state.rename_flag;
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
        rename_flag_mutation(state, payload) {
            state.rename_flag = payload.id;
        },
        //upload progress test func
        test(state){
            state.upload_queue[0].progress = 100;
        }
    },
});