<?php

namespace awebss\Models;

use Illuminate\Database\Eloquent\Model;

//use Illuminate\Database\Eloquent\SoftDeletes;

class Vacuna extends Model
{
 
	//use SoftDeletes;

	protected $table='_vacuna';

	protected $primaryKey = 'vac_id';

	protected $fillable = array('vac_nombre','vac_cant_dosis','vac_descripcion');
	
	protected $hidden = ['created_at','updated_at','userid_at','deleted_at'];

	protected $dates = ['deleted_at'];

	//protected $softDelete = true;

	public function dosis_vacuna()
	{
		
		return $this->hasMany('awebss\Models\Dosis_vacuna', 'dov_id');
	}


}
