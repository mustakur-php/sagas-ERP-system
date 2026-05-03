<?php

namespace App\Http\Controllers\HR;

use App\Http\Controllers\Controller;
use App\Models\HrWorkLocation;
use Illuminate\Http\Request;

class HrWorkLocationController extends Controller
{
    public function index()
    {
        $locations = HrWorkLocation::latest()->get();

        return view('hr.work-locations.index', compact('locations'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'latitude' => ['required', 'numeric', 'between:-90,90'],
            'longitude' => ['required', 'numeric', 'between:-180,180'],
            'radius_meters' => ['required', 'integer', 'min:5', 'max:5000'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $data['is_active'] = $request->boolean('is_active');

        HrWorkLocation::create($data);

        return redirect()
            ->route('hr.work-locations.index')
            ->with('success', 'تم إضافة موقع العمل بنجاح');
    }

    public function destroy(HrWorkLocation $work_location)
    {
        $work_location->delete();

        return redirect()
            ->route('hr.work-locations.index')
            ->with('success', 'تم حذف موقع العمل بنجاح');
    }
}   