const state = {
    action_btn_loading: false,
    inputs_initialized: [],
    filter_options: {},
    field_options: {},
    resource_list_total : 0
};

const getters = {
    action_btn_loading: (state) => state.action_btn_loading,
    inputs_initialized: (state) => state.inputs_initialized,
    filter_options: (state) => state.filter_options,
    field_options: (state) => state.field_options,
    resource_list_total: (state) => state.resource_list_total,
};

const mutations = {
    setActionBtnLoading: (state, payload) => {
        state.action_btn_loading = payload;
    },
    pushInputInitialized: (state, payload) => {
        state.inputs_initialized.push(payload);
    },
    addFilterOptions: (state, payload) => {
        state.filter_options = { ...state.filter_options, ...payload };
    },
    addFieldOptions: (state, payload) => {
        state.field_options = { ...state.field_options, ...payload };
    },
    setResourceListTotal: (state, payload) => {
        state.resource_list_total = payload;
    }
};

export default {
    namespaced: true,
    state,
    getters,
    mutations,
};
