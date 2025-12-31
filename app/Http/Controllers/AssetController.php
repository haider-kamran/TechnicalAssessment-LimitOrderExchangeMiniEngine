<?php

namespace App\Http\Controllers;

use App\Repositories\AssetRepository;
use Illuminate\Http\Request;
use Inertia\Inertia;

class AssetController extends Controller
{
    protected AssetRepository $repo;

    public function __construct(AssetRepository $repo)
    {
        $this->repo = $repo;
    }

    public function index()
    {
        $assets = AssetRepository::GetData()->get();

        return Inertia::render('Assets/Index', [
            'assets' => $assets
        ]);
    }

    public function create()
    {
        return Inertia::render('Assets/Create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'symbol' => 'required|string',
            'amount' => 'required|numeric|min:0',
        ]);

        $this->repo->create($data);

        return redirect()->route('assets.index')
            ->with('success', 'Asset created successfully');
    }

    public function edit($id)
    {
        $asset = $this->repo->find($id);

        return Inertia::render('Assets/Edit', [
            'asset' => $asset
        ]);
    }

    public function update(Request $request, $id)
    {
        $data = $request->validate([
            'symbol' => 'required|string',
            'amount' => 'required|numeric|min:0',
        ]);

        $this->repo->update($id, $data);

        return redirect()->route('assets.index')
            ->with('success', 'Asset updated successfully');
    }

    public function destroy($id)
    {
        $this->repo->delete($id);

        return redirect()->route('assets.index')
            ->with('success', 'Asset deleted successfully');
    }
}
