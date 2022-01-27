const state = () => ({
    current: null,
})

const getters = {
    getCurrentVideo(state) {
        return state.current;
    }
}

const actions = {
}

const mutations = {
    setCurrent(state, current) {
        state.current = current
    }
}

export default {
    namespaced: true,
    state,
    getters,
    actions,
    mutations
}
