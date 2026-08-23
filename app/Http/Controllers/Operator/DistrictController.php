<?php

namespace App\Http\Controllers\Operator;

use App\Http\Controllers\Controller;
use App\Http\Requests\Operator\StoreDistrictRequest;
use App\Http\Requests\Operator\UpdateDistrictRequest;
use App\Models\District;

class DistrictController extends Controller
{
    public function index()
    {
        $districts = District::withCount(['users', 'deceased'])->latest()->paginate(10);
        return view('operator.master-data.districts.index', compact('districts'));
    }

    public function create()
    {
        return view('operator.master-data.districts.create');
    }

    public function store(StoreDistrictRequest $request)
    {
        District::create($request->validated());
        return redirect()->route('operator.master-data.districts.index')->with('success', 'Kabupaten/Kota berhasil ditambahkan.');
    }

    public function edit(District $district)
    {
        return view('operator.master-data.districts.edit', compact('district'));
    }

    public function update(UpdateDistrictRequest $request, District $district)
    {
        $district->update($request->validated());
        return redirect()->route('operator.master-data.districts.index')->with('success', 'Kabupaten/Kota berhasil diperbarui.');
    }

    public function destroy(District $district)
    {
        if ($district->users()->count() > 0 || $district->deceased()->count() > 0) {
            return back()->with('error', 'Kabupaten/Kota tidak dapat dihapus karena sedang digunakan oleh entitas lain.');
        }

        $district->delete();
        return redirect()->route('operator.master-data.districts.index')->with('success', 'Kabupaten/Kota berhasil dihapus.');
    }
}
