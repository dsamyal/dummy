<?php

namespace App\Http\Middleware;

use Closure;
use Jaybizzle\CrawlerDetect\CrawlerDetect;

class CrawlerCheck
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $CrawlerDetect = new CrawlerDetect;

        // Check the user agent of the current 'visitor'
        if($CrawlerDetect->isCrawler()) {
            // true if crawler user agent detected
            exit ('Request cancelled. We detected bot activity from your side.');
        }

        return $next($request);
    }
}
