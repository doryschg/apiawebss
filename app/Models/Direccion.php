<?php

namespace awebss\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\SoftDeletes;

class Direccion extends Model
{
    use SoftDeletes;

    protected $table='direccion';

	// Eloquent asume que cada tabla tiene una clave primaria con una columna llamada id.
	// Si éste no fuera el caso entonces hay que indicar cuál es nuestra clave primaria en la tabla:
	protected $primaryKey = 'dir_id';

	// Atributos que se pueden asignar de manera masiva.
	protected $fillable = array('dir_zona_comunidad','dir_avenida_calle','dir_numero','dir_tipo');
	
	// Aquí ponemos los campos que no queremos que se devuelvan en las consultas.
	protected $hidden = ['created_at','updated_at','userid_at','deleted_at'];

	protected $dates=['deleted_at'];

	 protected $softDelete = true;

	
	public function municipio()
	{
		
		return $this->hasMany('awebss\Models\Municipio','mun_id');
	}
	public function persona()
	{
		
		return $this->belongstoMany('awebss\Models\Persona','per_id');
	}



}
