<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserSettings\StoreDashboardSettingsRequest;
use App\Http\Requests\UserSettings\StoreRequest;
use App\Models\UserSetting;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserSettingController extends Controller
{
    public function hideColumns(Request $request)
    {
        UserSetting::saveSettings($request->all());

        return new JsonResponse();
    }

    public function generalSettings()
    {
        $user = Auth::user();
        $timezones = config('settings.timezones');
        $dateFormats = config('settings.dateformats');
        $printers = $user->printers->pluck('hostnameAndName', 'id');

        $dateFormatsReformatted = [];
        foreach( $dateFormats as $dateFormat ){
            $dateFormatsReformatted[$dateFormat] = Carbon::now()->format($dateFormat);
        }

        $settings = [
            'date_format' => user_settings(UserSetting::USER_SETTING_DATE_FORMAT),
            'timezone' => user_settings(UserSetting::USER_SETTING_TIMEZONE),
            'label_printer' => user_settings(UserSetting::USER_SETTING_LABEL_PRINTER_ID),
            'barcode_printer' => user_settings(UserSetting::USER_SETTING_BARCODE_PRINTER_ID),
            'slip_printer' => user_settings(UserSetting::USER_SETTING_SLIP_PRINTER_ID)
        ];

        return view('user_settings.general', [
            'user' => $user,
            'settings' => $settings,
            'timezones' => $timezones,
            'dateFormats' => $dateFormatsReformatted,
            'printers' => $printers
        ]);
    }

    public function edit()
    {
        $timezones = config('settings.timezones');
        $printers = [' ' => __('PDF')] + Auth::user()->printers->pluck('hostnameAndName', 'id')->toArray();

        foreach (config('settings.dateformats') as $dateFormat) {
            $dateFormats[$dateFormat] = now()->format($dateFormat);
        }

        return view('user_settings.edit', [
            'timezones' => $timezones,
            'dateFormats' => $dateFormats,
            'printers' => $printers
        ]);
    }

    public function update(StoreRequest $request)
    {
        $data = $request->validated();

        app('user')->storeSettings(Auth::user(), $data);

        return response()->json([
            'success' => true,
            'message' => __('Settings successfully updated.'),
            'request' => $data
        ]);
    }
    public function updateGeneralSettings(Request $request)
    {
        $data = $request->only([
            'timezone',
            'date_format',
            'label_printer',
            'barcode_printer',
            'order_slip_printer',
            'packing_slip_printer'
        ]);

        UserSetting::saveSettings($data);

        return response()->json([
            'success' => true,
            'message' => __('Settings successfully updated.'),
            'request' => $data
        ]);
    }

    public function dashboardSettings(StoreDashboardSettingsRequest $request)
    {
        UserSetting::saveSettings($request->validated());

        return redirect()->back();
    }
}
