<?php

namespace App\Http\Controllers;

use App\Models\Ejercicio;
use Illuminate\Http\Request;

/**
 * Class EjercicioController
 * @package App\Http\Controllers
 */
class EjercicioController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $ejercicios = Ejercicio::paginate(10);

        return view('ejercicio.index', compact('ejercicios'))
            ->with('i', (request()->input('page', 1) - 1) * $ejercicios->perPage());
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $ejercicio = new Ejercicio();
        return view('ejercicio.create', compact('ejercicio'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        request()->validate(Ejercicio::$rules);

        $ejercicio = Ejercicio::create($request->all());

        return redirect()->route('ejercicios.index')
            ->with('success', 'Ejercicio creado exitosamente.');
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $ejercicio = Ejercicio::find($id);

        return view('ejercicio.show', compact('ejercicio'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $ejercicio = Ejercicio::find($id);

        return view('ejercicio.edit', compact('ejercicio'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  Ejercicio $ejercicio
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Ejercicio $ejercicio)
    {
        request()->validate(Ejercicio::$rules);

        $ejercicio->update($request->all());

        return redirect()->route('ejercicios.index')
            ->with('success', 'Ejercicio actualizado exitosamente');
    }

    /**
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function destroy($id)
    {
        $ejercicio = Ejercicio::find($id)->delete();

        return redirect()->route('ejercicios.index')
            ->with('success', 'Ejercicio eliminado exitosamente');
    }
}
