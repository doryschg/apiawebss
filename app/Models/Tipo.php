<?php

namespace awebss\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\SoftDeletes;

class Tipo extends Model
{
    use SoftDeletes;

    protected $table="tipo";

    protected $primaryKey = 'tip_id';

	// Atributos que se pueden asignar de manera masiva.
	protected $fillable = array('tip_nombre','tip_descripcion');

	protected $hidden = ['created_at','updated_at','userid_at','deleted_at'];

	protected $dates = ['deleted_at'];

	 protected $softDelete = true;



	public function establecimiento_salud()
	{
		return $this->Hasmany('awebss\Establecimiento_saldu');
	}

}
