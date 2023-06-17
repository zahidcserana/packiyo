<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

/**
 * App\Models\UserSetting
 *
 * @property int $id
 * @property int $user_id
 * @property string $key
 * @property string $value
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property string|null $deleted_at
 * @method static Builder|UserSetting newModelQuery()
 * @method static Builder|UserSetting newQuery()
 * @method static Builder|UserSetting query()
 * @method static Builder|UserSetting whereCreatedAt($value)
 * @method static Builder|UserSetting whereDeletedAt($value)
 * @method static Builder|UserSetting whereId($value)
 * @method static Builder|UserSetting whereKey($value)
 * @method static Builder|UserSetting whereUpdatedAt($value)
 * @method static Builder|UserSetting whereUserId($value)
 * @method static Builder|UserSetting whereValue($value)
 * @mixin \Eloquent
 */
class UserSetting extends Model
{
    const USER_SETTING_DASHBOARD_FILTER_DATE_START = 'dashboard_filter_date_start';
    const USER_SETTING_DASHBOARD_FILTER_DATE_END = 'dashboard_filter_date_end';
    const USER_SETTING_TIMEZONE = 'timezone';
    const USER_SETTING_DATE_FORMAT = 'date_format';
    const USER_SETTING_LABEL_PRINTER_ID = 'label_printer';
    const USER_SETTING_BARCODE_PRINTER_ID = 'barcode_printer';
    const USER_SETTING_SLIP_PRINTER_ID = 'slip_printer';
    const USER_SETTING_CACHE_PREFIX = 'user_setting';
    const USER_SETTING_DEFAULT_TIME_FORMAT = 'H:i:s';
    const USER_SETTING_LOT_PRIORITY = 'lot_priority';
    const USER_SETTING_EXCLUDE_SINGLE_LINE_ORDERS = 'exclude_single_line_orders';

    const USER_SETTING_KEYS = [
        self::USER_SETTING_DASHBOARD_FILTER_DATE_START,
        self::USER_SETTING_DASHBOARD_FILTER_DATE_END,
        self::USER_SETTING_TIMEZONE,
        self::USER_SETTING_DATE_FORMAT,
        self::USER_SETTING_DATE_FORMAT,
        self::USER_SETTING_LABEL_PRINTER_ID,
        self::USER_SETTING_BARCODE_PRINTER_ID,
        self::USER_SETTING_SLIP_PRINTER_ID,
        self::USER_SETTING_EXCLUDE_SINGLE_LINE_ORDERS,
        self::USER_SETTING_LOT_PRIORITY
    ];

    protected $fillable = ['user_id', 'key', 'value'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    static function saveSettings($settings)
    {
        $userId = Auth::user()->id;
        foreach ($settings as $setting => $value) {
            UserSetting::updateOrCreate(
                ['user_id' => $userId, 'key' => $setting],
                ['value' => $value]
            );

            Cache::put(static::USER_SETTING_CACHE_PREFIX.'_' . $userId . '_' . $setting, $value);
        }
    }
}
