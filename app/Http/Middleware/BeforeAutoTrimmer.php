<?php

namespace App\Http\Middleware;

use Closure;

class BeforeAutoTrimmer
{
    public function handle($request, Closure $next)
    {
        $input = $request->all();
        if ($input) {
            array_walk_recursive($input, function (&$item) {
                $item = is_string($item) ? trim($item) : $item;
            });
            $request->merge($input);
        }
        return $next($request);
    }
}
