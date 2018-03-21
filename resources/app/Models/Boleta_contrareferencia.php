<?php


namespace awebss\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\SoftDeletes;

class Boleta_contrareferencia extends Model
{

	use SoftDeletes;

    protected $table='boleta_contrareferencia';
	
	protected $primaryKey = 'bc_id';

	protected $fillable = array('fe_id_origen','fe_id_destino','fe_id_contacto','bc_cod','bc_servicio_referente','bc_dias_internacion','bc_peso','bc_temp','bc_pa_sistolica','bc_fc','bc_fr','bc_diagnostico_egreso','bc_complicaciones','bc_examenes_dx','bc_exa_interconsultas','bc_tratamientos','bc_seguimento_trat','bc_recomendaciones','bc_referencia_fue','bc_acomp','bc_fecha_llegada','bc_hora_llegada','bc_estado_contrareferencia','bc_pa_diastolica','bc_talla');
	
	protected $hidden = ['updated_at','userid_at','deleted_at'];

	protected $dates = ['deleted_at'];

	 protected $softDelete = true;

	public function referencia()
	{	
		return $this->belongsTo('awebss\Models\Boleta_referencia','br_id');
	}

	/*public function funcionario()
	{	
		return $this->belongsTo('awebss\Models\Funcionario','fun_id');
	}
*/

	 public function scopeVerifica_referencia($query, $br_id){

        return $query->where('br_id',$br_id)->select('bc_id')->get();
    }



}
