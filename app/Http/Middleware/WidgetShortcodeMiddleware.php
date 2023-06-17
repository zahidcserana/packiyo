<?php

namespace App\Http\Middleware;

use App\Models\UserWidget;
use Closure;
use Illuminate\Support\Facades\Auth;

class WidgetShortcodeMiddleware
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
        $response = $next($request);

        if (!method_exists($response, 'content')) {
            return $response;
        }

        $widgetList = UserWidget::WIDGET_LIST;

        if ($request->route()) {
            if ($request->route()->getName() === 'user_widgets.get_widgets') {

                $data = json_decode($response->content());

                if ($data) {
                    $grid_stack_data = json_decode($data);

                    foreach ($grid_stack_data as $key => $widget) {
                        foreach ($widgetList as $shortcode => $data) {
                            if ($widget->content === $shortcode) {
                                $grid_stack_data[$key]->content = view($data['view'])->render();
                                $grid_stack_data[$key]->content = preg_replace("/\r|\n/", '',  $grid_stack_data[$key]->content);
                            }
                        }
                    }

                    $response->setContent(json_encode($grid_stack_data));

                    return $response;
                }
            }

            if ($request->route()->getName() ==='home' && Auth::user()) {
                $content = $response->content();

                foreach ($widgetList as $shortcode => $data) {
                    $content = str_replace($shortcode, preg_replace("/\r|\n/", '', view($data['view']))  , $content);
                }
                $response->setContent($content);

                return $response;
            }
        }


        return $response;
    }
}
