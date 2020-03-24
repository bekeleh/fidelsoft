<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Libraries\Utils;

/**
 * Class QueryLogging.
 */
class QueryLogging
{
    public function handle(Request $request, Closure $next)
    {
        // Enable query logging for development
        if (Utils::isNinjaDev()) {
            DB::enableQueryLog();
            $timeStart = microtime(true);
        }

        $response = $next($request);

        if (Utils::isNinjaDev()) {
            // hide requests made by debugbar
            if (strstr($request->url(), '_debugbar') === false) {
                $queries = DB::getQueryLog();
                $count = count($queries);
                $timeEnd = microtime(true);
                $time = $timeEnd - $timeStart;
                Log::info($request->method() . ' - ' . $request->url() . ": $count queries - " . $time);
                //Log::info($queries);
            }
        }

        return $response;
    }
}
