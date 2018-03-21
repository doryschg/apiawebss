<?php

namespace awebss\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\SoftDeletes;

class Departamento extends Model
{
    use SoftDeletes;

	protected $table='departamento';

	
	protected $primaryKey = 'dep_id';

	// Atributos que se pueden asignar de manera masiva.
	protected $fillable = array('dep_nombre','dep_abreviacion');
	
	protected $hidden = ['created_at','updated_at','userid_at','deleted_at'];

	protected $dates=['deleted_at'];

	 protected $softDelete = true;

	public function provincia()
	{
		
		return $this->belongsTo('awebss\Provincia');
	}
}
