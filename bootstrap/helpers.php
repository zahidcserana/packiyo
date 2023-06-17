<?php

use Carbon\Carbon;
use Illuminate\Support\Facades\Route;
use App\Models\UserSetting;
use App\Models\CustomerSetting;
use Illuminate\Support\Facades\Cache;


if (!function_exists('dot')) {
    function dot($key) {
        $key = str_replace(['[', ']'], ['.', ''], $key);

        return $key;
    }
}

if (!function_exists('route_method')) {
    function route_method()
    {
        if (empty(Route::current())) {
            return '';
        }

        return Route::current()->methods()[0] ?? '';
    }
}


if (!function_exists('user_settings')) {
    function user_settings($key = null, $default = null)
    {
        $userId = auth()->user()->id;

        if ($key == null) {
            $settings = [];

            foreach (UserSetting::USER_SETTING_KEYS as $key) {
                $settings[$key] = user_settings($key);
            }

            return $settings;
        } else {
            $cacheKey = 'user_setting_' . $userId . '_' . $key;
            $setting = Cache::get($cacheKey);

            if (!$setting) {
                $userSetting = UserSetting::firstOrCreate([
                    'user_id' => $userId,
                    'key' => $key
                ]);

                if (!$userSetting->value && $default) {
                    $userSetting->update([
                        'value' => $default
                    ]);
                }

                $setting = $userSetting->value;

                Cache::put($cacheKey, $setting);
            }

            return $setting;
        }
    }
}

if (!function_exists('customer_settings')) {
    function customer_settings($customerId, $key = null, $default = null)
    {
        if (!$customerId) {
            return $default;
        }

        if (is_null($key)) {
            $settings = [];

            foreach (CustomerSetting::CUSTOMER_SETTING_KEYS as $settingKey) {
                $settings[$settingKey] = customer_settings($customerId, $settingKey);
            }

            return $settings;
        } else {
            $cacheKey = 'customer_setting_' . $customerId . '_' . $key;
            $setting = Cache::get($cacheKey);

            if (!$setting) {
                $customerSetting = CustomerSetting::firstOrCreate([
                    'customer_id' => $customerId,
                    'key' => $key
                ]);

                if (!$customerSetting->value && $default) {
                    $customerSetting->update([
                        'value' => $default
                    ]);
                }

                $setting = $customerSetting->value;

                Cache::put($cacheKey, $setting);
            }

            return $setting;
        }
    }
}

if (!function_exists('user_date_time')) {
    function user_date_time($date, $dateHours = false): string
    {
        $dateFormat = user_settings( UserSetting::USER_SETTING_DATE_FORMAT, env('DEFAULT_DATE_FORMAT'));
        $timeZone = user_settings( UserSetting::USER_SETTING_TIMEZONE, env('DEFAULT_TIME_ZONE'));

        if (!$dateFormat) {
            $dateFormat = 'Y-m-d';
        }

        return Carbon::parse($date)->timezone($timeZone)->format($dateFormat.($dateHours ? ' '.UserSetting::USER_SETTING_DEFAULT_TIME_FORMAT : ''));
    }
}

if (!function_exists('menu_item_visible')) {
    function menu_item_visible($menuItem): bool
    {
        $customer = app('user')->getSessionCustomer();

        if ($customer && $customer->parent && in_array($menuItem, config('settings.client_excluded_menu_items'))) {
           return false;
        }

        return true;
    }
}

if (!function_exists('dimension_height')) {
    function dimension_height($customer, $type = '')
    {
        $defaultHeight = [
            'label' => 192,
            'document' => 297,
            'barcode' => 30,
        ];

        $height = [
            'label' => customer_settings($customer->id, CustomerSetting::CUSTOMER_SETTING_LABEL_SIZE_HEIGHT),
            'document' => customer_settings($customer->id, CustomerSetting::CUSTOMER_SETTING_DOCUMENT_SIZE_HEIGHT),
            'barcode' => customer_settings($customer->id, CustomerSetting::CUSTOMER_SETTING_BARCODE_SIZE_HEIGHT),
        ];

        if (empty($height[$type])) {
            return $defaultHeight[$type] * 2.83464567;
        }

        return calculate_dimension($customer, $height[$type]);
    }
}

if (!function_exists('dimension_width')) {
    function dimension_width($customer, $type = '')
    {
        $defaultWidth = [
            'label' => 102,
            'document' => 210,
            'barcode' => 100,
        ];

        $width = [
            'label' => customer_settings($customer->id, CustomerSetting::CUSTOMER_SETTING_LABEL_SIZE_WIDTH),
            'document' => customer_settings($customer->id, CustomerSetting::CUSTOMER_SETTING_DOCUMENT_SIZE_WIDTH),
            'barcode' => customer_settings($customer->id, CustomerSetting::CUSTOMER_SETTING_BARCODE_SIZE_WIDTH),
        ];

        if (empty($width[$type])) {
            return $defaultWidth[$type] * 2.83464567;
        }

        return calculate_dimension($customer, $width[$type]);
    }
}


if (!function_exists('calculate_dimension')) {
    function calculate_dimension($customer, $value)
    {
        $dimension = customer_settings($customer->id, CustomerSetting::CUSTOMER_SETTING_DIMENSIONS_UNIT);

        if ($dimension == 'in') {
            return $value * 72;
        } else if ($dimension == 'cm') {
            return $value * 28.3465;
        } else {
            return $value * 2.83464567;
        }
    }
}
