<?php

namespace App\AuditResolvers;

use OwenIt\Auditing\Contracts\Auditable;
use OwenIt\Auditing\Contracts\Resolver;

class CustomMessageResolver implements Resolver
{
    public static function resolve(Auditable $auditable)
    {
        if (method_exists($auditable, 'custom_message')) {
            return $auditable->custom_message;
        }

        return null;
    }
}
