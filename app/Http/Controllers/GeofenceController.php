<?php

namespace App\Http\Controllers;

use App\Models\Geofence;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GeofenceController extends Controller
{
    public function index()
    {
        $geofences = Auth::user()->geofences;
        return view('geofences.index', compact('geofences'));
    }
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'radius' => 'required|integer|min:10|max:5000',
        ]);

        Geofence::create([
            'user_id' => Auth::id(),
            'name' => $request->name,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'radius' => $request->radius,
        ]);

        return back()->with('status', 'Geofence added successfully.');
    }

    public function destroy(Geofence $geofence)
    {
        if ($geofence->user_id !== Auth::id()) {
            abort(403);
        }

        $geofence->delete();

        return redirect()->route('dashboard')->with('status', 'Geofence deleted successfully!');
    }

    public function show(Geofence $geofence)
    {
        if ($geofence->user_id !== Auth::id()) {
            abort(403);
        }
        return view('geofences.show', compact('geofence'));
    }

    public function edit(Geofence $geofence)
    {
        if ($geofence->user_id !== Auth::id()) {
            abort(403);
        }
        return view('geofences.edit', compact('geofence'));
    }

    public function update(Request $request, Geofence $geofence)
    {
        if ($geofence->user_id !== Auth::id()) {
            abort(403);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'radius' => 'required|integer|min:10|max:5000',
        ]);

        $geofence->update($request->all());

        return redirect()->route('dashboard')->with('status', 'Geofence updated successfully.');
    }
}
