<?php

namespace awebss\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\SoftDeletes;

class Imagen_prevencion extends Model
{
    use SoftDeletes;

    protected $table='imagen_prevencion';

	protected $primaryKey = 'ip_id';

	protected $fillable = array('imp_ruta');
	
	protected $hidden = ['created_at','updated_at','userid_at','deleted_at'];

	protected $dates = ['deleted_at'];

	 protected $softDelete = true;

	
	public function mensaje()
	{
		return $this->belongsTo('awebss\Models\Mensaje', 'men_id');
	}
}
