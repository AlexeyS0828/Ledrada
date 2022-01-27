const state = () => ({
    all: []
})

const getters = {
    getAllScreens(state) {
        return state.all;
    }
}

const actions = {
}

const mutations = {
    setScreens(state, screens) {
        state.all = screens
    }
}

export default {
    namespaced: true,
    state,
    getters,
    actions,
    mutations
}
