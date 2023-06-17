<?php

namespace App\Components;

use App\Models\Customer;
use App\Models\CustomerSetting;
use App\Models\UserSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Webpatser\Countries\Countries;

class ProfileComponent extends BaseComponent
{
    public function edit($request)
    {
        $user = auth()->user();

        $customer = Customer::find($request->customer_id);

        return [
            'name' => $user->contactInformation->name,
            'email' => $user->email,
            'image' => $user->picture ? url(Storage::url($user->picture)) : '',
            'printers' => $user->printers->pluck('hostnameAndName', 'id'),
            'user_hash' => $user->userHash(),
            'label_printer' => user_settings(UserSetting::USER_SETTING_LABEL_PRINTER_ID),
            'barcode_printer' => user_settings(UserSetting::USER_SETTING_BARCODE_PRINTER_ID),
            'slip_printer' => user_settings(UserSetting::USER_SETTING_SLIP_PRINTER_ID),
            'exclude_single_line_orders' => user_settings(UserSetting::USER_SETTING_EXCLUDE_SINGLE_LINE_ORDERS),
            'max_amount_to_pick' => customer_settings($customer->id, CustomerSetting::CUSTOMER_SETTING_MAX_AMOUNT_TO_PICK, 100),
            'is_admin' => $user->isAdmin(),
            'countries' => Countries::all(),
            'file' => ''
        ];
    }

    public function password(Request $request)
    {
        if ($request->get('password') !== $request->get('confirm_password')) {
            return [
                'success' => false,
                'message' => 'Passwords don\'t match.'
            ];
        }

        if (auth()->user()->update(['password' => Hash::make($request->get('password'))])) {
            return [
                'success' => true,
                'message' => 'Password changed.'
            ];
        }

        return [
            'success' => false,
            'message' => 'Something went wrong.'
        ];
    }

    public function delete(Request $request)
    {
        $user = auth()->user();

        if (empty($user->picture)) {
            return [
                'success' => false,
                'message' => 'File not found.'
            ];
        }

        File::delete(storage_path("/app/public/{$user->getOriginal('picture')}"));
        $user->update(['picture' => null]);

        return [
            'success' => true,
            'message' => 'File deleted.'
        ];
    }

    public function upload(Request $request)
    {
        $user = auth()->user();

        if (empty($request->file)) {
            return [
                'success' => false,
                'message' => 'Select file.'
            ];
        }

        $user->update(['picture' => $request->file->store('profile', 'public')]);

        return [
            'success' => true,
            'message' => 'File uploaded.'
        ];
    }

    public function update(Request $request)
    {
        $user = auth()->user();

        if (empty($request->email)) {
            return [
                'success' => false,
                'message' => 'Email field is required.'
            ];
        }

        if (!filter_var($request->email, FILTER_VALIDATE_EMAIL)) {
            return [
                'success' => false,
                'message' => 'Enter valid email.'
            ];
        }

        $data = $request->only([
            UserSetting::USER_SETTING_LABEL_PRINTER_ID,
            UserSetting::USER_SETTING_BARCODE_PRINTER_ID,
            UserSetting::USER_SETTING_SLIP_PRINTER_ID,
            UserSetting::USER_SETTING_EXCLUDE_SINGLE_LINE_ORDERS
        ]);

        UserSetting::saveSettings($data);

        if ($user->isAdmin()) {
            $customer = Customer::find($request['customer_id']);
            app('customer')->storeSettings($customer, $request->only([CustomerSetting::CUSTOMER_SETTING_MAX_AMOUNT_TO_PICK]));
        }

        if (!empty($request->name)) {
            $user->contactInformation->update(['name' => $request->name]);
        }

        $user->update(['email' => $request->email]);

        return [
            'success' => true,
            'message' => 'Account information saved.'
        ];
    }

    public function logout(Request $request)
    {
        $user = auth()->user();

        if ($currentAccessToken = $user->currentAccessToken()) {
            $currentAccessToken->delete();
        }

        return response()->json(['message' => 'Logged out.']);
    }
}
