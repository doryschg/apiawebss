<?php

namespace awebss\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\SoftDeletes;

class Conexion extends Model
{
    use SoftDeletes;
    protected $table='conexion';


	protected $primaryKey = 'con_id';

	protected $fillable = array('con_ip','con_puerto','con_instancia','con_bd','con_usuario','con_password','con_inicio_vigencia','con_final_vigencia');
	
	protected $hidden = ['created_at','updated_at','userid_at','deleted_at'];

	protected $dates=['deleted_at'];

	 protected $softDelete = true;


	public function mensaje()
	{
		
		return $this->belongsTo('awebss\Models\Mensaje','men_id');
	}
}
