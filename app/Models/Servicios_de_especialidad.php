<?php

namespace awebss\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\SoftDeletes;

class Servicios_de_especialidad extends Model
{
    use SoftDeletes;

    protected $table="servicios_de_especialidad";

    protected $primaryKey = 'se_id';

	// Atributos que se pueden asignar de manera masiva.
	protected $fillable = array('se_nombre');
	protected $hidden = ['created_at','updated_at','userid_at','deleted_at'];

	protected $dates = ['deleted_at'];

	 protected $softDelete = true;
	
	public function servicio()
	{
		return $this->belongsTo('awebss\Models\Servicio','ser_id');
	}
}
