<?php

namespace awebss\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\SoftDeletes;

class Red extends Model
{
    use SoftDeletes;

protected $table="red";

    protected $primaryKey = 'red_id';

	// Atributos que se pueden asignar de manera masiva.
	protected $fillable = array('red_nombre','red_descripcion');

	protected $hidden = ['created_at','updated_at','userid_at','deleted_at'];

	protected $dates = ['deleted_at'];

	 protected $softDelete = true;
	
	public function establecimiento_salud()
	{
		return $this->hasMany('awebss\Establecimiento_salud');
	}


}
