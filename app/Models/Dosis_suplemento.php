<?php

namespace awebss\Models;

use Illuminate\Database\Eloquent\Model;

//use Illuminate\Database\Eloquent\SoftDeletes;

class Dosis_suplemento extends Model
{
    //use SoftDeletes;

    protected $table='_dosis_suplemento';

	protected $primaryKey = 'dos_id';

	protected $fillable = array('dos_edad_inicio','dos_edad_fin','dos_unidad_dosis','dos_cantidad','dos_suministro','dos_numero_dosis');
	
	protected $hidden = ['created_at','updated_at','userid_at','deleted_at'];

	protected $dates=['deleted_at'];

	//protected $softDelete = true;

	
	public function suplemento()
	{
		
		return $this->belongsTo('awebss\Models\Suplemento', 'sup_id');
	}
}
