<?php

namespace awebss\Models;

use Illuminate\Database\Eloquent\Model;

//use Illuminate\Database\Eloquent\SoftDeletes;

class Imagen_enfermedad extends Model
{
    
   // use SoftDeletes;

    protected $table='imagen_enfermedad';

	protected $primaryKey = 'ie_id';

	protected $fillable = array('ie_nombre','ie_ruta');
	
	protected $hidden = ['created_at','updated_at','userid_at','deleted_at'];

	protected $dates = ['deleted_at'];

	//protected $softDelete = true;


public function enfermedad()
	{
		return $this->belongsTo('awebss\Models\Enfermedad','enf_id');
	}


}
