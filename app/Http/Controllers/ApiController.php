<?php

namespace App\Http\Controllers;

class ApiController extends Controller
{
    protected function resourceAbilityMap()
    {
        $resourceAbilityMap = parent::resourceAbilityMap();

        $resourceAbilityMap['store'] = 'batchStore';
        $resourceAbilityMap['update'] = 'batchUpdate';
        $resourceAbilityMap['destroy'] = 'batchDelete';

        return $resourceAbilityMap;
    }

    protected function resourceMethodsWithoutModels()
    {
        $methods = parent::resourceMethodsWithoutModels();

        $methods = array_merge($methods, ['update', 'destroy']);

        return $methods;
    }
}
