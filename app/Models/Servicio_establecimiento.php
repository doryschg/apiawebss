<?php


namespace awebss\Models;

use Illuminate\Database\Eloquent\Model;

//use Illuminate\Database\Eloquent\SoftDeletes;

class Servicio_establecimiento extends Model
{
    // use SoftDeletes;
	
    protected $table='servicio_establecimiento';

	protected $primaryKey = 'se_id';


	protected $fillable = array('se_necesita_ref','se_costo');
	
	protected $hidden = ['created_at','updated_at','userid_at','deleted_at'];

	protected $dates=['deleted_at'];

	//protected $softDelete = true;

	public function servicio_consultorio()
	
	{
		return $this->hasMany('awebss\Models\servicio_consultorio','sc_id');
	}

	public function servicio()
	{
		return $this->belongsTo('awebss\Models\Servicio','ser_id');
	}

	public function establecimiento_salud()
	
	{
		return $this->hasMany('awebss\Models\establecimiento_salud','es_id');
	}
}
