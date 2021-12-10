import Vue from "vue";
import Vuex from "vuex";

Vue.use(Vuex);
export default new Vuex.Store({
    state: {
        /*
        upload_queue: {
            id: int,
            name: string,
            progress: int
        }
        */
        upload_queue: [] 
    },
    getters: {
        get_upload_queue: state => {
            return state.upload_queue;
        }
    },
    mutations: {
        add_upload_queue_mutation(state, payload) {
            state.upload_queue.push(payload.file);
        },
        remove_upload_queue_mutation(state, payload) {
            state.upload_queue.splice(state.upload_queue.findIndex((e) => e.id == payload.id), 1);
            console.log(state.upload_queue)
        },
        test(state){
            state.upload_queue[0].progress = 100;
        }
    },
});