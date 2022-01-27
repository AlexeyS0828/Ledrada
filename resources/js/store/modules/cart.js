import dayjs from "../../dayjs";

const datesDayDiff = (date1, date2) => {
    const startDate = dayjs(date1, 'YYYY-MM-DD', true)
    const endDate = dayjs(date2, 'YYYY-MM-DD', true)

    return endDate.diff(startDate, 'day') + 1
}


const state = () => ({
    screens: [],
    period: [],
    email: null,
})

const getters = {
    getCart(state) {
        return state.screens;
    },
    getPeriod(state) {
        return state.period;
    },
    getEmail(state) {
        return state.email;
    },
    getPeriodDays(state) {
        if (!state.period.length) {
            return 0
        }

        if (state.period.length === 1) {
            return 1;
        }

        return datesDayDiff(state.period[0], state.period[1])
    }
}

const actions = {}

const mutations = {
    setPeriod(state, period) {
        if (period.length === 2) {
            if (period[0] === period[1]) {
                state.period = [period[0]]

                return
            }

            if (datesDayDiff(period[0], period[1]) <= 0) {
                state.period = [period[1], period[0]]

                return
            }
        }
        state.period = period
    },
    addToCart(state, screen) {
        state.screens.push(screen)
    },
    setEmail(state, email) {
        state.email = email
    },
    emptyCart(state) {
      state.screens = [];
    },
    removeFromCart(state, screen) {
        const cartScreenIndex = state.screens.findIndex(cartScreen => cartScreen.id === screen.id)
        state.screens.splice(cartScreenIndex, 1)
    }
}

export default {
    namespaced: true,
    state,
    getters,
    actions,
    mutations
}
