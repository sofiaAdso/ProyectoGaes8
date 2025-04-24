<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Cancion
 *
 * @property $id
 * @property $titulo
 * @property $artista
 * @property $descripcion
 * @property $nivel
 * @property $created_at
 * @property $updated_at
 *
 * @package App
 * @mixin \Illuminate\Database\Eloquent\Builder
 */
class Cancion extends Model
{
    
    static $rules = [
		'titulo' => 'required',
		'artista' => 'required',
		'descripcion' => 'required',
		'nivel' => 'required',
    ];

    protected $perPage = 20;

    /**
     * Attributes that should be mass-assignable.
     *
     * @var array
     */
    protected $fillable = ['titulo','artista','descripcion','nivel'];



}
