<?php

namespace awebss\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\SoftDeletes;

class Usuario_servicio extends Model
{
    use SoftDeletes;

	protected $table='_usuario_servicio';
	
	protected $primaryKey = 'us_id';

	protected $hidden = ['created_at','updated_at','userid_at','deleted_at']; 

	protected $dates = ['deleted_at'];

	 protected $softDelete = true;


	public function usuario()
	{
		
return $this->hasMany('awebss\Modelos\Usuario','usu_id');
	}

	public function servicio_()
	{
		
return $this->hasMany('awebss\Modelos\Servicio_','ser_id');
	}
}
