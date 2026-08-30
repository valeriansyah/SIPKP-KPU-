<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReportRevisionItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'report_id',
        'revision_type',
        'field_name',
        'document_type_id',
        'label',
        'is_resolved',
    ];

    protected $casts = [
        'is_resolved' => 'boolean',
    ];

    public function report()
    {
        return $this->belongsTo(Report::class);
    }

    public function documentType()
    {
        return $this->belongsTo(DocumentType::class);
    }
}
