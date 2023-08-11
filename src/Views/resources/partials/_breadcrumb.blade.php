@php
    $breadcrumbs = $resource->breadcrumbLabels();
    $hash = request()->has('hash') ? '#' . request()->hash : null;
    
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
    
    $hashDecoded = $hash ? json_decode(base64_decode(@request()->hash), true) : null;
    
    if ($hashDecoded) {
        $related_resource = ResourcesHelpers::find(data_get($hashDecoded, 'related_resource'));
        $hasHcode = \marcusvbda\vstack\Hashids::encode(data_get($hashDecoded, 'related_resource_id'));
        $bc[] = [
            'title' => $related_resource->label(),
            'route' => $related_resource->route(),
        ];
        $bc[] = [
            'title' => "#$hasHcode",
            'route' => data_get($hashDecoded, 'redirect_url'),
        ];
    } else {
        $bc[] = [
            'title' => $breadcrumbs['list'],
            'route' => @$resource->route(),
        ];
    }
    
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
