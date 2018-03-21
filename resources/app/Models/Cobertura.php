<?php

namespace awebss\Models;

use Illuminate\Database\Eloquent\Model;

class Cobertura extends Model
{
     protected $table='cobertura';


	protected $primaryKey = 'cob_id';

	protected $fillable = array();
	
	protected $hidden = ['created_at','updated_at','userid_at','deleted_at'];

	protected $dates=['deleted_at'];

	public function establecimiento_salud()
	{
		
		return $this->belongsToMany('awebss\Models\Establecimiento_salud','es_id');
	}

		public function zona()
	{
		
		return $this->belongsToMany('awebss\Models\Zona','zon_id');
	}

}
