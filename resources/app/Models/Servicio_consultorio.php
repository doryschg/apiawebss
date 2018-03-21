<?php

namespace awebss\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\SoftDeletes;

class Servicio_consultorio extends Model
{
    use SoftDeletes;
	
    protected $table='servicio_consultorio';

	protected $primaryKey = 'sc_id';


	protected $fillable = array();
	
	protected $hidden = ['created_at','updated_at','userid_at','deleted_at'];

	protected $dates=['deleted_at'];

	protected $softDelete = true;

	public function consultorio()
	
	{
		return $this->hasMany('awebss\Models\Consultorio','con_id');
	}

	public function servicio_establecimiento()
	{
		return $this->belongsTo('awebss\Models\Servicio_establecimiento','se_id');
	}
}
