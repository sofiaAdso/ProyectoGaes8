<?php

namespace App\Http\Controllers;

use App\Models\Leccion;
use Illuminate\Http\Request;

/**
 * Class LeccionController
 * @package App\Http\Controllers
 */
class LeccionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $leccions = Leccion::paginate(10);

        return view('leccion.index', compact('leccions'))
            ->with('i', (request()->input('page', 1) - 1) * $leccions->perPage());
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $leccion = new Leccion();
        return view('leccion.create', compact('leccion'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        request()->validate(Leccion::$rules);

        $leccion = Leccion::create($request->all());

        return redirect()->route('leccions.index')
            ->with('success', 'Leccion created successfully.');
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $leccion = Leccion::find($id);

        return view('leccion.show', compact('leccion'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $leccion = Leccion::find($id);

        return view('leccion.edit', compact('leccion'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  Leccion $leccion
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Leccion $leccion)
    {
        request()->validate(Leccion::$rules);

        $leccion->update($request->all());

        return redirect()->route('leccions.index')
            ->with('success', 'Leccion updated successfully');
    }

    /**
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function destroy($id)
    {
        $leccion = Leccion::find($id)->delete();

        return redirect()->route('leccions.index')
            ->with('success', 'Leccion deleted successfully');
    }
}
