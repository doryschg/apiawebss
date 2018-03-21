<?php

namespace awebss\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\SoftDeletes;

class Servicio extends Model
{
    use SoftDeletes;

    protected $table="servicio";

    protected $primaryKey = 'ser_id';

	// Atributos que se pueden asignar de manera masiva.
	protected $fillable = array('ser_nombre','ser_tipo');

	protected $hidden = ['created_at','updated_at','userid_at','deleted_at'];

	protected $dates = ['deleted_at'];

	 protected $softDelete = true;
	
	

}
