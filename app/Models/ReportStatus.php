<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ReportStatus extends Model
{
    protected $fillable = [
        'status_name',
        'description',
    ];

    public function reports(): HasMany
    {
        return $this->hasMany(Report::class, 'report_status_id');
    }

    public function reportVerifications(): HasMany
    {
        return $this->hasMany(
            ReportVerification::class,
            'report_status_id'
        );
    }
}