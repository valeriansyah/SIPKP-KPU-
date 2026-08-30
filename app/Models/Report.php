<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Report extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'report_status_id',
        'report_number',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function reportStatus(): BelongsTo
    {
        return $this->belongsTo(ReportStatus::class, 'report_status_id');
    }

    public function deceased(): HasOne
    {
        return $this->hasOne(Deceased::class);
    }

    public function documents(): HasMany
    {
        return $this->hasMany(Document::class);
    }

    public function reportVerifications()
    {
        return $this->hasMany(ReportVerification::class);
    }

    public function revisionItems()
    {
        return $this->hasMany(ReportRevisionItem::class);
    }
}
