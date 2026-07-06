<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Port;
use App\Models\Country;
use Illuminate\Http\Request;

class PortController extends Controller
{
    public function index()
    {
        $ports = Port::with('country')->latest()->paginate(10);
        return view('admin.ports.index', compact('ports'));
    }

    public function create()
    {
        $countries = Country::all();
        return view('admin.ports.create', compact('countries'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:150',
            'code' => 'required|string|max:10|unique:ports',
            'country_id' => 'required|exists:countries,id',
            'city' => 'nullable|string|max:100',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'type' => 'required|string|max:50',
            'size' => 'nullable|string|max:20',
        ]);

        Port::create($validated);

        return redirect()->route('admin.ports.index')
            ->with('success', 'Pelabuhan berhasil ditambahkan!');
    }

    public function edit(Port $port)
    {
        $countries = Country::all();
        return view('admin.ports.edit', compact('port', 'countries'));
    }

    public function update(Request $request, Port $port)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:150',
            'code' => 'required|string|max:10|unique:ports,code,' . $port->id,
            'country_id' => 'required|exists:countries,id',
            'city' => 'nullable|string|max:100',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'type' => 'required|string|max:50',
            'size' => 'nullable|string|max:20',
        ]);

        $port->update($validated);

        return redirect()->route('admin.ports.index')
            ->with('success', 'Pelabuhan berhasil diupdate!');
    }

    public function destroy(Port $port)
    {
        $port->delete();
        return redirect()->route('admin.ports.index')
            ->with('success', 'Pelabuhan berhasil dihapus!');
    }
}