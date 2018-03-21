<?php

namespace awebss\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\SoftDeletes;

class Telefono extends Model
{
    use SoftDeletes;

    protected $table='telefono_est';

	// Eloquent asume que cada tabla tiene una clave primaria con una columna llamada id.
	// Si éste no fuera el caso entonces hay que indicar cuál es nuestra clave primaria en la tabla:
	protected $primaryKey = 'te_id';

	// Atributos que se pueden asignar de manera masiva.
	protected $fillable = array('te_numero');
	
	protected $hidden = ['created_at','updated_at','userid_at','deleted_at'];

	protected $dates = ['deleted_at'];

	 protected $softDelete = true;


	public function establecimiento_salud()
	{
		
		return $this->hasMany('awebss\Models\Establecimiento_salud','es_id');
	}  
}


