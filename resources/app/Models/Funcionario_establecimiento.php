<?php

namespace awebss\Models;

use Illuminate\Database\Eloquent\Model;

class Funcionario_establecimiento extends Model
{
    
    protected $table='funcionario_establecimiento';

	protected $primaryKey = 'fe_id';

	protected $fillable = array('fe_memorandum','fe_inicio_trabajo','fe_fin_trabajo','fe_cargo','fe_estado_laboral','fe_carga_laboral','fe_estado');
	
	protected $hidden = ['created_at','updated_at','userid_at','deleted_at'];

	protected $dates=['deleted_at'];

	//protected $softDelete = true;

	public function establecimiento_salud()
	{
		
		return $this->hasMany('awebss\Models\Establecimiento_salud', 'es_id');
	}

	public function funcionario()
	{
		
		return $this->hasMany('awebss\Models\Funcionario','fun_id');
	}


}
