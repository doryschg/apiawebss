<?php

namespace awebss\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\SoftDeletes;

class Imagen_est extends Model
{
    use SoftDeletes;

    protected $table='imagen_est';

	protected $primaryKey = 'ie_id';

	protected $fillable = array('ie_nombre','ie_enlace','ie_tipo');
	
	protected $hidden = ['created_at','updated_at','userid_at','deleted_at']; 

	protected $dates = ['deleted_at'];

	 protected $softDelete = true;


	public function Establecimiento_salud()
	{
		
		return $this->belongsTo('awebss\Models\Establecimiento_salud','es_id');
	}
}
