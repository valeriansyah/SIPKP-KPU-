<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class District extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'code',
    ];

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function deceased(): HasMany
    {
        return $this->hasMany(Deceased::class);
    }

    public function reports()
    {
        return $this->hasManyThrough(
            Report::class,
            Deceased::class,
            'district_id', // Foreign key on Deceased table
            'id', // Foreign key on Report table (not applicable actually, Report has no foreign key to Deceased, Deceased has foreign key to Report!)
            'id', // Local key on District table
            'report_id' // Local key on Deceased table
        );
    }
}
