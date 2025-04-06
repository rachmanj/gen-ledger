<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CompanySetting extends Model
{
    protected $fillable = [
        'company_name',
        'fiscal_year_start_month',
        'fiscal_year_start_day',
        'default_currency'
    ];

    protected $casts = [
        'fiscal_year_start_month' => 'integer',
        'fiscal_year_start_day' => 'integer'
    ];
} 