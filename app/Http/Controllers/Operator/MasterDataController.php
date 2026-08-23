<?php

namespace App\Http\Controllers\Operator;

use App\Http\Controllers\Controller;
use App\Models\District;
use App\Models\DocumentType;
use App\Models\ReportStatus;
use App\Models\User;

class MasterDataController extends Controller
{
    public function index()
    {
        $stats = [
            'districts' => District::count(),
            'document_types' => DocumentType::count(),
            'report_statuses' => ReportStatus::count(),
            'sub_operators' => User::whereHas('role', function ($query) {
                $query->where('role_name', 'Sub Operator');
            })->count(),
        ];

        return view('operator.master-data.index', compact('stats'));
    }
}
