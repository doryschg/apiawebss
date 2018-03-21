<?php

namespace awebss\Models;

use Illuminate\Database\Eloquent\Model;

//use Illuminate\Database\Eloquent\SoftDeletes;

class Atiende_diariamente extends Model
{
    //use SoftDeletes;

    protected $table="atiende_diariamente";

    protected $primaryKey = 'ad_id';

	// Atributos que se pueden asignar de manera masiva.
	protected $fillable = array('ad_fecha_atiende','ad_hora_inicio','ad_numero_ficha','ad_estado');

	protected $hidden = ['created_at','updated_at','userid_at','deleted_at'];

	//protected $dates = ['deleted_at'];

	//protected $softDelete = true;
	

	public function turno()
	{
		return $this->belongsTo('awebss\Models\Configuracion_turno','ct_id');
	}

	public function cita()
	{
		return $this->belongsTo('awebss\Models\Cita','cit_id');
	}
	

}
