<?php

namespace App\Http\Middleware;

use App\Models\Customer;
use Closure;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class Is3PLCustomerMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param  \Closure(Request): (Response|RedirectResponse)  $next
     * @return Response|RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        /** @var Customer|null $customer */
        $customer = app()->user->getSessionCustomer();

        if (is_null($customer)) {
            return $next($request);
        }

        if ($customer->isParent()) {
            return $next($request);
        }

        abort(403);
    }
}
