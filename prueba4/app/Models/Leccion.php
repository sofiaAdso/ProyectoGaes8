<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Leccion
 *
 * @property $id
 * @property $nombre_leccion
 * @property $descripcion
 * @property $nivel
 * @property $instrumento
 * @property $tablatura
 * @property $created_at
 * @property $updated_at
 *
 * @package App
 * @mixin \Illuminate\Database\Eloquent\Builder
 */
class Leccion extends Model
{
    
    static $rules = [
		'nombre_leccion' => 'required',
		'descripcion' => 'required',
		'nivel' => 'required',
		'instrumento' => 'required',
		'tablatura' => 'required',
    ];

    protected $perPage = 20;

    /**
     * Attributes that should be mass-assignable.
     *
     * @var array
     */
    protected $fillable = ['nombre_leccion','descripcion','nivel','instrumento','tablatura'];



}
