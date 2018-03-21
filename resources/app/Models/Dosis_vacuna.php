<?php

namespace awebss\Models;

use Illuminate\Database\Eloquent\Model;

//use Illuminate\Database\Eloquent\SoftDeletes;

class Dosis_vacuna extends Model
{
    //use SoftDeletes;

    protected $table='_dosis_vacuna';

	protected $primaryKey = 'dov_id';

	protected $fillable = array('dov_tipo','dov_suministro','dov_edad_inicio','dov_edad_fin','dov_numero_dosis');
	
	protected $hidden = ['created_at','updated_at','userid_at','deleted_at'];

	protected $dates = ['deleted_at'];

	//protected $softDelete = true;

	
	public function vacuna()
	{
		
		return $this->belongsTo('awebss\Models\Vacuna', 'vac_id');
	}

}
