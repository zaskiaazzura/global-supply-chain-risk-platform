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
        $ports = Port::with('country')->latest()->paginate(15);
        return view('admin.ports.index', compact('ports'));
    }

    public function create()
    {
        $countries = Country::orderBy('name')->get();
        return view('admin.ports.create', compact('countries'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:150',
            'code' => 'nullable|string|max:10',
            'country_id' => 'required|exists:countries,id',
            'city' => 'nullable|string|max:100',
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'type' => 'required|string|max:50',
            'size' => 'nullable|string|max:20',
        ]);

        Port::create($request->all());

        return redirect()->route('admin.ports.index')
            ->with('success', 'Pelabuhan berhasil ditambahkan!');
    }

    public function edit(Port $port)
    {
        $countries = Country::orderBy('name')->get();
        return view('admin.ports.edit', compact('port', 'countries'));
    }

    public function update(Request $request, Port $port)
    {
        $request->validate([
            'name' => 'required|string|max:150',
            'code' => 'nullable|string|max:10',
            'country_id' => 'required|exists:countries,id',
            'city' => 'nullable|string|max:100',
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'type' => 'required|string|max:50',
            'size' => 'nullable|string|max:20',
        ]);

        $port->update($request->all());

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