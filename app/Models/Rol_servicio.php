<?php

namespace awebss\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\SoftDeletes;

class Rol_servicio extends Model
{
    use SoftDeletes;

    protected $table='_rol_servicio';
	
	protected $primaryKey = 'rs_id';

	protected $hidden = ['created_at','updated_at','userid_at','deleted_at'];

	protected $dates = ['deleted_at'];

	 protected $softDelete = true;

	public function rol()
	{
		
return $this->hasMany('awebss\Modelos\Rol','rol_id');
	}

	public function servicio_()
	{
		
return $this->hasMany('awebss\Modelos\Servicio_','ser_id');
	}
}
