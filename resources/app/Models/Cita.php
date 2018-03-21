<?php

namespace awebss\Models;

use Illuminate\Database\Eloquent\Model;

//use Illuminate\Database\Eloquent\SoftDeletes;

class Cita extends Model
{
    // use SoftDeletes;

    protected $table='cita';

	protected $primaryKey = 'cit_id';

	protected $fillable = array('cit_fecha_consulta','cit_hora_consulta','cit_nro_ficha','cit_motivo_consulta','cit_estado_asistencia','cit_estado_pago','cit_estado_confirmacion','cit_tipo','cit_es_id','cit_se_id','cit_con_id','cit_fe_id','cit_calificar_es','cit_calificar_se','cit_calificar_con','cit_calificar_fe');
	
	protected $hidden = ['created_at','updated_at','userid_at','deleted_at'];

	protected $dates=['deleted_at'];

	//protected $softDelete = true;

	public function atiende_diariamente()
	{
		return $this->hasOne('awebss\Models\Atiende_diariamente','ad_id');
	}

	public function paciente()
	{
		return $this->hasMany('awebss\Models\Paciente','pac_id');
	}
}
