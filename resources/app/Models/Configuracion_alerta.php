<?php

namespace awebss\Models;

use Illuminate\Database\Eloquent\Model;

//use Illuminate\Database\Eloquent\SoftDeletes;

class Configuracion_alerta extends Model
{
    
//use SoftDeletes;
    protected $table='configuracion_alerta';

	protected $primaryKey = 'ca_id';

	protected $fillable = array('ca_edad_envio','ca_intervalo_inicio_envio','ca_intervalo_fin_envio','ca_frecuencia','ca_fecha_campania','ca_sexo');
	
	protected $hidden = ['created_at','updated_at','userid_at','deleted_at'];

	protected $dates=['deleted_at'];

	 //protected $softDelete = true;


	public function mensaje()
	{
		return $this->belongsTo('awebss\Models\Mensaje','men_id');
	}

	public function enfermedad()
	{
		
		return $this->belongsTo('awebss\Models\Enfermedad','enf_id');
	}


}
