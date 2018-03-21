<?php

namespace awebss\Models;

use Illuminate\Database\Eloquent\Model;

class Crecimiento_signos_vitales extends Model
{
    protected $table='crecimiento_signos_vitales';

	protected $primaryKey = 'csv_id';


	protected $fillable = array('csv_talla','csv_peso','csv_peso_talla','csv_imc_calculado','csv_talla_edad','csv_temp','csv_fc','csv_pa','csv_fr','csv_fecha_control','csv_observacion','csv_edad_control');
	
	protected $hidden = ['created_at','updated_at','userid_at','deleted_at'];

	protected $dates=['deleted_at'];

	// protected $softDelete = true;

}
