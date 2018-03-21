<?php

namespace awebss\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\SoftDeletes;

class Imagen extends Model
{
    use SoftDeletes;
    
    protected $table='imagen';

	// Eloquent asume que cada tabla tiene una clave primaria con una columna llamada id.
	// Si éste no fuera el caso entonces hay que indicar cuál es nuestra clave primaria en la tabla:
	protected $primaryKey = 'ima_id';

	// Atributos que se pueden asignar de manera masiva.
	protected $fillable = array('ima_nombre','ima_enlace','ima_tipo');
	
	// Aquí ponemos los campos que no queremos que se devuelvan en las consultas.
	protected $hidden = ['created_at','updated_at','userid_at','deleted_at'];

	protected $dates = ['deleted_at'];

	 protected $softDelete = true;


	public function persona()
	{
		
		return $this->belongsTo('awebss\Models\Imagen','ima_id');
	}
	
}
