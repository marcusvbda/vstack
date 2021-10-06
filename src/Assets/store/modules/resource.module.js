const state = {
    action_btn_loading: false
};

const getters = {
    action_btn_loading: state => state.action_btn_loading
};

const mutations = {
    setActionBtnLoading: (state, payload) => {
        state.action_btn_loading = payload;
    }
};

export default {
    namespaced: true,
    state,
    getters,
    mutations
};
