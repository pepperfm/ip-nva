<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\Master;

class ResolveCurrentMaster
{
    public function handle(Request $request, \Closure $next): Response
    {
        $masterId = $request->header('X-Master-Id');
        if (!empty($masterId)) {
            $request->attributes->set('current_master', Master::find($masterId));
        }

        return $next($request);
    }
}
