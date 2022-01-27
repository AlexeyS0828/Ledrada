const state = () => ({
    all: {},
    fetchingStats: false,
    lastLength: null,
    lastDays: null,
    lastFrequency: null,
})

const getters = {
    getAllScreens(state) {
        return state.all;
    },
    getFetchingStatsStatus(state) {
        return state.fetchingStats;
    },
    getScreenPrice: (state) => (screenId) => {
        if (!state.all[screenId]) {
            return null;
        }

        return state.all[screenId].price_per_day ?? null;
    },
    getTotalPrice(state, getters, rootState, rootGetters) {
        const screensInCart = rootGetters['cart/getCart']
        const days = rootGetters['cart/getPeriodDays']
        let sum = 0

        for(let screen of screensInCart) {
            sum+= state.all[screen.id]?.price_per_day ?? 0
        }

        return sum * days
    },
    getEstimatedReplays: (state) => (screenId) => {
        if (!state.all[screenId]) {
            return null;
        }

        return state.all[screenId].replays ?? null;
    }
}

const actions = {
    async statsValid({state}, {length, days, frequency}) {
        if (!Object.keys(state.all).length) {
            return false;
        }

        if (!state.lastDays || !state.lastLength || !state.lastFrequency) {
            return false;
        }

        if (days !== state.lastDays) {
            return false;
        }

        if (Math.abs(state.lastLength - length) > 4) {
            return false;
        }

        return state.lastFrequency === frequency;
    },
    async getStats({commit, dispatch, rootGetters}) {
        const screens = rootGetters["screens/getAllScreens"].map(screen => screen.id).join(',')
        const periodDays = rootGetters['cart/getPeriodDays']
        const currentVideo = rootGetters['video/getCurrentVideo']
        const frequency = 1;

        if (!periodDays || !currentVideo) {
            return;
        }

        const length = currentVideo.length.toFixed(0)

        if (await dispatch('statsValid', {length, days: periodDays, frequency})) {
            return;
        }

        const q = `screens/${screens}?length=${length}&frequency=1&days=${periodDays}`

        commit('setFetchingStatsStatus', true)

        return this._vm.$axios.get(q).then(({data}) => {
            commit('setScreens', data)
            commit('setLastStats', {length, days: periodDays, frequency})
        }).finally(() => {
            commit('setFetchingStatsStatus', false)
        })
    }
}

const mutations = {
    setScreens(state, screens) {
        state.all = screens
    },
    setLastStats(state, {length, days, frequency}) {
        state.lastLength = length
        state.lastDays = days
        state.lastFrequency = frequency
    },
    setFetchingStatsStatus(state, status) {
        state.fetchingStats = status
    }
}

export default {
    namespaced: true,
    state,
    getters,
    actions,
    mutations
}
