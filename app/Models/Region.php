<?php

namespace awebss\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\SoftDeletes;

class Region extends Model
{
    use SoftDeletes;

    //// Nombre de la tabla en Postgres.
	protected $table='region';

	// Eloquent asume que cada tabla tiene una clave primaria con una columna llamada id.
	// Si éste no fuera el caso entonces hay que indicar cuál es nuestra clave primaria en la tabla:
	protected $primaryKey = 'reg_id';

	// Atributos que se pueden asignar de manera masiva.
	protected $fillable = array('reg_nombre');
	
	// Aquí ponemos los campos que no queremos que se devuelvan en las consultas.
	protected $hidden = ['created_at','updated_at','userid_at','deleted_at'];

	protected $dates = ['deleted_at'];

	 protected $softDelete = true;

	public function municipio()
	{
		// 1 municipio pertenece a una Región.
		// $this hace referencia al objeto que tengamos en ese momento de Región.
		return $this->hasMany('awebss\Modelos\Municipio','mun_id');
	}

}
