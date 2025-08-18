<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ImpersonationMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Only add impersonation banner for web requests
        if ($request->expectsJson() || $request->is('impersonate/*')) {
            return $response;
        }

        // Check if we're currently impersonating
        if (session('impersonator_id') && auth()->check()) {
            // Add impersonation banner to the response
            if ($response->headers->get('content-type') && 
                str_contains($response->headers->get('content-type'), 'text/html')) {
                
                $content = $response->getContent();
                
                // Add impersonation banner after the opening body tag
                $banner = $this->getImpersonationBanner();
                $content = str_replace('<body', '<body>' . $banner, $content);
                
                $response->setContent($content);
            }
        }

        return $response;
    }

    /**
     * Get the impersonation banner HTML
     */
    private function getImpersonationBanner(): string
    {
        $impersonatorName = session('impersonator_name');
        $currentUserName = auth()->user()->name;
        $currentUserHotel = auth()->user()->hotel ? auth()->user()->hotel->name : 'Unknown Hotel';
        
        return '
        <div class="impersonation-banner" style="
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            background: linear-gradient(135deg, #ff6b6b, #ee5a24);
            color: white;
            padding: 10px 20px;
            text-align: center;
            z-index: 9999;
            font-weight: 500;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        ">
            <div class="container-fluid">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <i class="icon-base ri ri-user-settings-line me-2"></i>
                        <strong>Impersonation Mode:</strong> 
                        You are currently logged in as <strong>' . e($currentUserName) . '</strong> 
                        at <strong>' . e($currentUserHotel) . '</strong>
                    </div>
                    <div class="col-md-4 text-end">
                        <a href="' . route('impersonate.stop') . '" 
                           class="btn btn-sm btn-light" 
                           style="color: #333; text-decoration: none; padding: 5px 15px; border-radius: 4px; font-size: 12px;">
                            <i class="icon-base ri ri-logout-box-r-line me-1"></i>
                            Stop Impersonating
                        </a>
                    </div>
                </div>
            </div>
        </div>
        <style>
            body { padding-top: 60px !important; }
            .impersonation-banner .btn:hover { 
                background-color: #f8f9fa !important; 
                color: #333 !important; 
            }
        </style>';
    }
}
