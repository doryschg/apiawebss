<?php

namespace awebss\Models;

use Illuminate\Database\Eloquent\Model;

//use Illuminate\Database\Eloquent\SoftDeletes;

class Configuracion_turno extends Model
{
    // use SoftDeletes;
    protected $table='configuracion_turno';

	protected $primaryKey = 'ct_id';

	protected $fillable = array('ct_dia','ct_turno','ct_ini_turno','ct_fin_turno','ct_ficha_total','ct_ficha_sesar','ct_promedio');
	
	protected $hidden = ['created_at','updated_at','userid_at','deleted_at'];

	//protected $dates=['deleted_at'];

	 //protected $softDelete = true;

	public function configuracion_horario()
	{
		return $this->belongsto('awebss\Models\Configuracion_horario','ch_id');
	}

	public function atiende_diariamente()
	{
		
		return $this->hasMany('awebss\Models\Atiende_diariamente','ad_id');
	}
}
