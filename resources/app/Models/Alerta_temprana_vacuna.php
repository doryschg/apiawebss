<?php

namespace awebss\Models;

use Illuminate\Database\Eloquent\Model;

//use Illuminate\Database\Eloquent\SoftDeletes;

class Alerta_temprana_vacuna extends Model
{
    //use SoftDeletes;


    protected $table='alertas_temprana_vacuna';


	protected $primaryKey = 'atv_id';

	protected $fillable = array();
	
	protected $hidden = ['created_at','updated_at','userid_at','deleted_at'];

	protected $dates = ['deleted_at'];

	//protected $softDelete = true;


	public function vacuna()
	{
		
		return $this->belongsTo('awebss\Models\vacuna','vac_id');
	}

}
