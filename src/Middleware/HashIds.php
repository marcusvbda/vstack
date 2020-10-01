<?php

namespace marcusvbda\vstack\Middleware;

use Closure;

class HashIds
{
    public function handle($request, Closure $next, ...$ids)
    {
        try {
            foreach ($ids as $requestKey) {
                $decoded = \Hashids::decode($request->route($requestKey));
                $request->route()->setParameter($requestKey, $decoded[0]);
            }
        } catch (\Exception $e) {
            if ($request->ajax()) {
                return response()->json([
                    'error' => true,
                    'message' => 'Índice não encontrado.',
                ]);
            }
            return abort(404);
        }

        return $next($request);
    }
}
