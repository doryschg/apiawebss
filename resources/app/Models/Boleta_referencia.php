<?php

namespace awebss\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\SoftDeletes;

use Carbon;

class Boleta_referencia extends Model
{
    use SoftDeletes;

protected $table='boleta_referencia';

	protected $primaryKey = 'br_id';
	
	protected $fillable = array('fe_id_destino','fe_id_contacto','br_cod','br_frec_cardiaca','br_frec_resp','br_pa_sistolica','br_temperatura','br_peso','br_resumen','br_resultado_examen','br_diagnostico','br_tratamiento_inicial','br_acomp','br_motivo','br_subsector','br_fecha_llegada','br_hora_llegada','br_fecha_recepcion','br_hora_recepcion','br_seguro','br_estado_referencia','br_pa_diastolica','br_talla','br_servicio_referente','br_servicio_destino');
	
	protected $hidden = ['updated_at','userid_at','deleted_at'];

	protected $dates = ['created_at','deleted_at'];

	protected $softDelete = true;

	public function paciente()
	{
		return $this->hasmany('awebss\Models\Paciente','pac_id');
	}
		public function establecimiento_salud()
	{
		
		return $this->belongsTo('awebss\Models\Establecimiento_salud','es_id');
	}

	public function funcionario_establecimiento()
	{
		
		return $this->belongsTo('awebss\Models\funcionario_establecimiento','fe_id');
	}
	public function contrareferencia()
	{
		
		return $this->hasOne('awebss\Models\Boleta_contrareferencia','bc_id');
	}


	public function scopeGenerar_codigo($query,$pac_id,$es_codigo)
 {
        $codigo=$es_codigo."/".$pac_id;

        return $codigo;

}
}
