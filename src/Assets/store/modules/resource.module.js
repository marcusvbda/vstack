import { waitForEl } from "../../js/components/libs/helpers";

const state = {
    action_btn_loading: false,
    inputs_initialized: [],
    filter_options: {},
    field_options: {},
    resource_list_total : 0,
    resource_list_current_page : 1,
    resource_list_per_page : 10,
    resource_list_is_loading : true,
    resource_list_next_cursor : null,
    resource_list_previous_cursor : null,
    previous_loading:false,
    next_loading:false,
    resource_id : null,
    resource_total_text : 'Resultados encontrados : ',
    cursor : '',
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
    resource_list_is_loading: (state) => state.resource_list_is_loading,
    resource_total_text: (state) => state.resource_total_text,
    previous_loading: (state) => state.previous_loading,
    next_loading: (state) => state.next_loading,
    cursor: (state) => state.cursor,
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
    },
    setResourceListIsLoading: (state, payload) => {
        state.resource_list_is_loading = payload;
    },
    setPreviousLoading: (state, payload) => {
        state.previous_loading = payload;
    },
    setNextLoading: (state, payload) => {
        state.next_loading = payload;
    },
    setCursor : (state, payload) => {
        state.cursor = payload;
    }
};

const actions = {
    loadResourceData: async ({ commit,state },cursor = null) => {
        const removeLoadingEl = (el) => {
            waitForEl(el).then(() => document.querySelector(el).remove());
        };

        if(cursor) commit("setResourceListIsLoading",true);

        const axios = VueApp.getAxiosClient();

        const route = `/admin/${state.resource_id}/${
            state.report_mode ? 'report' : 'list'
        }/get-list-data`;

        if(cursor) state.resource_list_payload.params.cursor = cursor.value
        else {
            if(state.cursor) state.resource_list_payload.params.cursor = state.cursor
        }
        axios.get(route, state.resource_list_payload)
            .then(({ data }) => {
                const top_template = data.top.join('');
                commit("setResourceListTemplate",({ top: top_template }));
                if(!cursor) removeLoadingEl('#loading-section #top-loader');
                const table_template = data.table.join('');
                commit("setResourceListTemplate",{ table: table_template });
                if(!cursor) removeLoadingEl('#loading-section #table-loader');
                commit("setResourceListIsLoading",false);
                if(cursor) cursor.callback()
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
