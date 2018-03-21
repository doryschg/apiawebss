<?php

namespace awebss\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\SoftDeletes;

class Municipio extends Model
{
    use SoftDeletes;

    //// Nombre de la tabla en Posgres.
	protected $table='municipio';

	// Eloquent asume que cada tabla tiene una clave primaria con una columna llamada id.
	// Si éste no fuera el caso entonces hay que indicar cuál es nuestra clave primaria en la tabla:
	protected $primaryKey = 'mun_id';

	// Atributos que se pueden asignar de manera masiva.
	protected $fillable = array('mun_nombre','mun_cod_sice','pro_cod_sice');
	
	// Aquí ponemos los campos que no queremos que se devuelvan en las consultas.
	protected $hidden = ['created_at','updated_at','userid_at','deleted_at'];

	protected $dates = ['deleted_at'];

	 protected $softDelete = true;

	
	public function region()
	{
		
		return $this->belongsTo('awebss\Models\Region','reg_id');
	}
	public function provincia()
	{
		
		return $this->belongsto('awebss\Models\Provincia','pro_id');
	}
}
