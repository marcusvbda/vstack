@php
    $breadcrumbs = $resource->breadcrumbLabels();
    
    $prepend = config('vstack.prepend_breadcrumb', ['Dashboard' => '/admin']);
    
    if ($resource->id === 'audits') {
        if (request()->has('resource_id') && request()->has('code')) {
            $parentResource = ResourcesHelpers::find(request()->resource_id);
            $prepend = array_merge($prepend, [
                [
                    'title' => $parentResource->label(),
                    'route' => $parentResource->route(),
                ],
            ]);
        }
    }
    
    $bc[] = [
        'title' => $breadcrumbs['list'],
        'route' => @$resource->route(),
    ];
    $bc = array_merge($prepend, $bc);
    if (@$report_mode) {
        $bc[] = [
            'title' => $breadcrumbs['report'],
            'route' => @$resource->report_route(),
        ];
    }
    if (@$raw_type) {
        $bc[] = [
            'title' => $breadcrumbs[$raw_type],
            'route' => @$resource->route($raw_type),
        ];
    }
@endphp
<vstack-breadcrumb :items='@json($bc)'></vstack-breadcrumb>
