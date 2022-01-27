import Vuex from 'vuex'
import video from './modules/video'
import cart from './modules/cart'
import screens from './modules/screens'
import stats from './modules/stats'
import createPersistedState from 'vuex-persistedstate';
import Vue from "vue";
import SecureLS from "secure-ls";

const ls = new SecureLS({isCompression: false});

Vue.use(Vuex);

export default new Vuex.Store({
    plugins: [createPersistedState({
        key: 'ledradar',
        storage: {
            getItem: key => ls.get(key),
            setItem: (key, value) => ls.set(key, value),
            removeItem: key => ls.remove(key)
        }
    })],
    modules: {
        video,
        cart,
        screens,
        stats,
    },
})
