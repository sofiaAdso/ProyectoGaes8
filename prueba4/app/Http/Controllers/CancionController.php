<?php

namespace App\Http\Controllers;

use App\Models\Cancion;
use Illuminate\Http\Request;

/**
 * Class CancionController
 * @package App\Http\Controllers
 */
class CancionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $cancions = Cancion::paginate(10);

        return view('cancion.index', compact('cancions'))
            ->with('i', (request()->input('page', 1) - 1) * $cancions->perPage());
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $cancion = new Cancion();
        return view('cancion.create', compact('cancion'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        request()->validate(Cancion::$rules);

        $cancion = Cancion::create($request->all());

        return redirect()->route('cancions.index')
            ->with('success', 'Cancion creada exitosamente.');
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $cancion = Cancion::find($id);

        return view('cancion.show', compact('cancion'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $cancion = Cancion::find($id);

        return view('cancion.edit', compact('cancion'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  Cancion $cancion
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Cancion $cancion)
    {
        request()->validate(Cancion::$rules);

        $cancion->update($request->all());

        return redirect()->route('cancions.index')
            ->with('success', 'Cancion actualizada exitosamente');
    }

    /**
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function destroy($id)
    {
        $cancion = Cancion::find($id)->delete();

        return redirect()->route('cancions.index')
            ->with('success', 'Cancion eliminada exitosamente');
    }
}
