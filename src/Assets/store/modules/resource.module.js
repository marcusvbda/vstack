import { waitForEl } from "../../js/components/libs/helpers";

const state = {
    action_btn_loading: false,
    inputs_initialized: [],
    filter_options: {},
    field_options: {},
    resource_list_total : 0,
    resource_list_current_page : 1,
    resource_list_per_page : 10,
    resource_list_next_cursor : null,
    resource_list_previous_cursor : null,
    resource_id : null,
    resource_total_text : 'Resultados encontrados : ',
    report_mode : false,
    resource_list_template: {
        no_data: '',
        top: '',
        table: '',
    },
    resource_list_payload : null
};

const getters = {
    action_btn_loading: (state) => state.action_btn_loading,
    inputs_initialized: (state) => state.inputs_initialized,
    filter_options: (state) => state.filter_options,
    field_options: (state) => state.field_options,
    resource_list_total: (state) => state.resource_list_total,
    resource_list_current_page: (state) => state.resource_list_current_page,
    resource_list_per_page: (state) => state.resource_list_per_page,
    resource_list_next_cursor: (state) => state.resource_list_next_cursor,
    resource_list_previous_cursor: (state) => state.resource_list_previous_cursor,
    resource_list_template: (state) => state.resource_list_template,
    resource_list_payload: (state) => state.resource_list_payload,
    resource_id: (state) => state.resource_id,
    report_mode: (state) => state.report_mode,
    resource_total_text: (state) => state.resource_total_text,
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
    },
    setResourceListCurrentPage: (state, payload) => {
        state.resource_list_current_page = payload;
    },
    setResourceListPerPage: (state, payload) => {
        state.resource_list_per_page = payload;
    },
    setResourceListNextCursor: (state, payload) => {
        state.resource_list_next_cursor = payload;
    },
    setResourceListPreviousCursor: (state, payload) => {
        state.resource_list_previous_cursor = payload;
    },
    setResourceListTemplate: (state, payload) => {
        state.resource_list_template = {...state.resource_list_template, ...payload};
    },
    setResourceListPayload: (state, payload) => {
        state.resource_list_payload = payload;
    },
    setResourceId: (state, payload) => {
        state.resource_id = payload;
    },
    setReportMode: (state, payload) => {
        state.report_mode = payload;
    },
    setResourceTotalText: (state, payload) => {
        state.resource_total_text = payload;
    }

};

const actions = {
    loadResourceData: async ({ commit,state },cursor = null) => {
        const setVisibleLoadingEl = (el,value) => {
            waitForEl(el).then(() => document.querySelector(el).style.display = value);
        };

        commit("setResourceListTemplate",({ top: "" }));
        commit("setResourceListTemplate",({ table: "" }));

        setVisibleLoadingEl('#loading-section #top-loader','block');
        setVisibleLoadingEl('#loading-section #table-loader','block');

        const axios = VueApp.getAxiosClient();
        const route = `/admin/${state.resource_id}/${
            state.report_mode ? 'report' : 'list'
        }/get-list-data`;
        if(cursor) state.resource_list_payload.params.cursor = cursor
        axios.get(route, state.resource_list_payload)
            .then(({ data }) => {
                const top_template = data.top.join('');
                commit("setResourceListTemplate",({ top: top_template }));
                setVisibleLoadingEl('#loading-section #top-loader','none');
                const table_template = data.table.join('');
                commit("setResourceListTemplate",{ table: table_template });
                setVisibleLoadingEl('#loading-section #table-loader','none');
            })
            .catch((error) => {
                console.log(error);
            });

        if(!cursor){
            const count_route = `/admin/${state.resource_id}/count/get-list-data`;
            axios.get(count_route, state.resource_list_payload)
                .then(({ data }) => {
                    commit("setResourceListTotal",data.count);
                    commit("setResourceListPerPage",data.per_page);
                    commit("setResourceTotalText",data.resource_total_text);
                })
                .catch((error) => {
                    console.log(error);
                });
        }
    }
}

export default {
    namespaced: true,
    state,
    getters,
    mutations,
    actions
};
