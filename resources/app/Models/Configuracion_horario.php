<?php

namespace awebss\Models;

use Illuminate\Database\Eloquent\Model;

//use Illuminate\Database\Eloquent\SoftDeletes;

class Configuracion_horario extends Model
{
    
    //use SoftDeletes;
    protected $table='configuracion_horario';

	protected $primaryKey = 'ch_id';

	protected $fillable = array('ch_fecha_inicio','ch_fecha_final');
	
	protected $hidden = ['created_at','updated_at','userid_at','deleted_at'];

	//protected $dates=['deleted_at'];

	//protected $softDelete = true;


	public function consultorio()
	{
		return $this->hasMany('awebss\Models\Consultorio','con_id');
	}

	public function funcionario()
	{
		
		return $this->hasMany('awebss\Models\Funcionario','fun_id');
	}

	public function funcionario_horario($query, $fun_id)
	{
		
		$configuracion_horario=Configuracion_horario::where('fun_id',$fun_id);
		return $configuracion_horario;

	}

}
