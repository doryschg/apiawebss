<?php

namespace awebss\Models;

use Illuminate\Database\Eloquent\Model;

//use Illuminate\Database\Eloquent\SoftDeletes;

class Suplemento extends Model
{
    //use SoftDeletes;

    protected $table='_suplemento';

	protected $primaryKey = 'sup_id';

	protected $fillable = array('sup_nombre','sup_tipo_suplemento','sub_descripcion','sup_cant_dosis');
	
	protected $hidden = ['created_at','updated_at','userid_at','deleted_at'];

	protected $dates = ['deleted_at'];

	//protected $softDelete = true;

	public function enfermedad()
	{
			return $this->hasMany('awebss\Models\Enfermedad', 'enf_id');
	}
	public function dosis_suplemento()
	{
		
		return $this->hasMany('awebss\Models\dosis_suplemento', 'dos_id');
	}
}
