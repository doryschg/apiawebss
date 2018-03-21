<?php

namespace awebss\Models;

use Illuminate\Database\Eloquent\Model;

//use Illuminate\Database\Eloquent\SoftDeletes;

class Enfermedad extends Model
{
   // use SoftDeletes;

    protected $table='_enfermedad';

	protected $primaryKey = 'enf_id';

	protected $fillable = array('enf_nombre','enf_causas','enf_consecuencia','enf_descripcion','enf_prevencion');
	
	protected $hidden = ['created_at','updated_at','userid_at','deleted_at'];

	protected $dates = ['deleted_at'];

	//protected $softDelete = true;

	

	/*public function vacuna()
	{
		
		return $this->hasMany('awebss\Models\Vacuna', 'vac_id');
	}

	public function suplemento()
	{
		
		return $this->hasMany('awebss\Models\Suplemento', 'sup_id');
	}  */


}
