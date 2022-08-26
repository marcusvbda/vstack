const state = {
    action_btn_loading: false,
    inputs_initialized: [],
};

const getters = {
    action_btn_loading: (state) => state.action_btn_loading,
    inputs_initialized: (state) => state.inputs_initialized,
};

const mutations = {
    setActionBtnLoading: (state, payload) => {
        state.action_btn_loading = payload;
    },
    pushInputInitialized: (state, payload) => {
        state.inputs_initialized.push(payload);
    },
};

export default {
    namespaced: true,
    state,
    getters,
    mutations,
};
