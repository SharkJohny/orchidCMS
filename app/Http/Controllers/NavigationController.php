<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Navigation;
use App\Models\NavigationItem;

class NavigationController extends Controller
{
    public function index()
    {
        $navigations = Navigation::with('items')->get();
        return view('navigations.index', compact('navigations'));
    }

    public function create()
    {
        return view('navigations.create');
    }

    public function store(Request $request)
    {
        $navigation = Navigation::create($request->all());
        return redirect()->route('navigations.index');
    }

    public function edit(Navigation $navigation)
    {
        return view('navigations.edit', compact('navigation'));
    }

    public function update(Request $request, Navigation $navigation)
    {
        $navigation->update($request->all());
        return redirect()->route('navigations.index');
    }

    public function destroy(Navigation $navigation)
    {
        $navigation->delete();
        return redirect()->route('navigations.index');
    }
}
