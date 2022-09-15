const state = {
    action_btn_loading: false,
    inputs_initialized: [],
    filter_options : {}
};

const getters = {
    action_btn_loading: (state) => state.action_btn_loading,
    inputs_initialized: (state) => state.inputs_initialized,
    filter_options: (state) => state.filter_options,
};

const mutations = {
    setActionBtnLoading: (state, payload) => {
        state.action_btn_loading = payload;
    },
    pushInputInitialized: (state, payload) => {
        state.inputs_initialized.push(payload);
    },
    addFilterOptions: (state, payload) => {
        state.filter_options = {...state.filter_options,...payload};
    },
};

export default {
    namespaced: true,
    state,
    getters,
    mutations,
};
