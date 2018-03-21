<?php

namespace awebss\Models;

use Illuminate\Database\Eloquent\Model;

//use Illuminate\Database\Eloquent\SoftDeletes;

class Consultorio extends Model
{

	//use SoftDeletes;
	
    protected $table='consultorio';

	protected $primaryKey = 'con_id';


	protected $fillable = array('con_nombre','con_tipo','con_descripcion','con_cod');
	
	protected $hidden = ['created_at','updated_at','userid_at','deleted_at'];

	protected $dates=['deleted_at'];

	// protected $softDelete = true;

	public function servicio_consultorio()
	
	{
		return $this->belongsToMany('awebss\Models\servicio_consultorio','sc_id');
	}

	public function configuracion_horario()
	{
		return $this->belongsTo('awebss\Models\configuracion_horario','ch_id');
	}
	
}
