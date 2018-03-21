<?php

namespace awebss\Models;

use Illuminate\Database\Eloquent\Model;

//use Illuminate\Database\Eloquent\SoftDeletes;

class Rol extends Model
{
	//use SoftDeletes;
    
	protected $table='_rol';
	
	protected $primaryKey = 'rol_id';

	protected $fillable = array('rol_nombre','rol_descripcion');

	protected $hidden = ['created_at','updated_at','userid_at','deleted_at'];

	protected $dates = ['deleted_at'];

	 //protected $softDelete = true;

public function rol()
	{
		
return $this->hasMany('awebss\Modelos\Usuario','usu_id');
	}
}
