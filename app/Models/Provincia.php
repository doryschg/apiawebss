<?php

namespace awebss\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\SoftDeletes;

class Provincia extends Model
{
    use SoftDeletes;

	protected $table='provincia';

	// Eloquent asume que cada tabla tiene una clave primaria con una columna llamada id.
	// Si éste no fuera el caso entonces hay que indicar cuál es nuestra clave primaria en la tabla:
	protected $primaryKey = 'pro_id';

	// Atributos que se pueden asignar de manera masiva.
	protected $fillable = array('pro_nombre');
	
	protected $hidden = ['created_at','updated_at','userid_at','deleted_at'];


	protected $dates = ['deleted_at'];

	 protected $softDelete = true;

	
	public function municipio()
	{
		
		return $this->hasMany('awebss\Models\Municipio', 'mun_id');
	}
	public function departamento()
	{
		
		return $this->belongsTo('awebss\Models\Departamento','dep_id');
	}  //
}
