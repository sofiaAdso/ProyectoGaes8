<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Ejercicio
 *
 * @property $id
 * @property $titulo
 * @property $descripcion
 * @property $instrucciones
 * @property $nivel
 * @property $tipo_ejercicio
 * @property $created_at
 * @property $updated_at
 *
 * @package App
 * @mixin \Illuminate\Database\Eloquent\Builder
 */
class Ejercicio extends Model
{
    
    static $rules = [
		'titulo' => 'required',
		'descripcion' => 'required',
		'instrucciones' => 'required',
		'nivel' => 'required',
		'tipo_ejercicio' => 'required',
    ];

    protected $perPage = 20;

    /**
     * Attributes that should be mass-assignable.
     *
     * @var array
     */
    protected $fillable = ['titulo','descripcion','instrucciones','nivel','tipo_ejercicio'];



}
