<div class="row d-flex align-items-end mb-2">
    <div class="col-12 d-flex align-items-end justify-content-start flex-column">
        <div class="w-100 mt-2">
            <resource-filter-tags 
                id="resource-filter-tags" 
                ref="tags_filter" :resource_filters="{{ json_encode($filters) }}" 
                :get_params='@json($_data)'
            >
            </resource-filter-tags>
        </div>
        @php
        $globalFilterData = [
            'filter_route' => route('resource.index',['resource'=>$resource->id]),
            'query' => $_data,
            'value' => @$_data['_'] ? $_data['_'] : '',
        ];
        @endphp
        <div class="d-flex flex-column align-items-start justify-content-between w-100 resource-pagination" data-aos="fade-left">
            @if ($report_mode && $resource->canCreateReportTemplates())
                <div class="d-flex flex-column w-100">
                    @include("vStack::resources.partials._save_filter_btn")
                </div>
            @endif
            <div class="d-flex flex-row ml-auto align-items-center f-12 pagination-content">
                @if ($data->count() > 0)
                    <div class="d-flex flex-row align-items-center justify-content-center pagination-row">
                        {!! $resource->resultsFoundLabel() !!} {{ $data->count() }}
                        <div class="nav-scroller py-1 mb-2"> 
                            <div class="ml-3" id="resource-pagination">
                                {{ $data->appends($_data)->links() }}
                            </div>
                        </div>
                    </div>
                @endif
                @include("vStack::resources.partials._filter_btn")
                @if ($resource->search())
                    <resource-filter-global 
                        class="ml-2 mb-2" 
                        :data="{{ json_encode($globalFilterData) }}"
                    >
                    </resource-filter-global>
                @endif
            </div>
        </div>
    </div>
</div>
