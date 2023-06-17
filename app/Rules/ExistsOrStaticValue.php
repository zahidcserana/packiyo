<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\DB;

class ExistsOrStaticValue implements Rule
{
    private $table;
    private $column;
    private $staticValue;

    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct($table, $column, $staticValue)
    {
        $this->table = $table;
        $this->column = $column;
        $this->staticValue = $staticValue;
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        if ($value == $this->staticValue) {
            return true;
        }

        return DB::table($this->table)
            ->where($this->column, $value)
            ->exists();
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'Invalid :attribute';
    }
}
