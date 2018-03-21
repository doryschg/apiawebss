<?php


namespace awebss\Models;


use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\SoftDeletes;

class Cargo extends Model
{

	use SoftDeletes;
    protected $table='_cargo';

	protected $primaryKey = 'car_id';

	
	protected $fillable = array('car_tipo');
	
	
	protected $hidden = ['created_at','updated_at','userid_at','deleted_at'];

	protected $dates=['deleted_at'];

	 protected $softDelete = true;

}
