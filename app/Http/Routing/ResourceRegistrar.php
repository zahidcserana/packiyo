<?php

namespace App\Http\Routing;

class ResourceRegistrar extends \Illuminate\Routing\ResourceRegistrar
{
    protected static $verbs = [
        'create' => 'create',
        'edit' => 'edit'
    ];

    protected function addResourceUpdate($name, $base, $controller, $options)
    {
        if (strpos($controller, 'Api\\') === false) {
            return parent::addResourceUpdate($name, $base, $controller, $options);
        }

        $uri = $this->getResourceUri($name);

        $action = $this->getResourceAction($name, $controller, 'update', $options);

        return $this->router->match(['PUT', 'PATCH'], $uri, $action);
    }

    protected function addResourceDestroy($name, $base, $controller, $options)
    {
        if (strpos($controller, 'Api\\') === false) {
            return parent::addResourceDestroy($name, $base, $controller, $options);
        }

        $uri = $this->getResourceUri($name);

        $action = $this->getResourceAction($name, $controller, 'destroy', $options);

        return $this->router->delete($uri, $action);
    }
}
