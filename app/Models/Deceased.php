<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Deceased extends Model
{
    use SoftDeletes, HasFactory;

    protected $table = 'deceased';

    protected $fillable = [
        'report_id',
        'district_id',
        'nik',
        'family_card_number',
        'name',
        'gender',
        'birth_place',
        'birth_date',
        'address',
        'death_place',
        'death_date',
    ];

    protected $casts = [
        'birth_date' => 'date',
        'death_date' => 'date',
    ];

    public function district(): BelongsTo
    {
        return $this->belongsTo(District::class);
    }

    public function report(): BelongsTo
    {
        return $this->belongsTo(Report::class);
    }
}
