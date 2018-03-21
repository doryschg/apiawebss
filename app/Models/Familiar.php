<?php

namespace awebss\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\SoftDeletes;

class Familiar extends Model
{
    //

     use SoftDeletes;
   protected $table="familiar";
   protected $primaryKey='fam_id';
  // protected $primaryKey = 'ci_familiar';

	// Atributos que se pueden asignar de manera masiva.
	protected $fillable = array('fam_parentesco');
	
	// Aquí ponemos los campos que no queremos que se devuelvan en las consultas.
	protected $hidden = ['created_at','updated_at','userid_at','deleted_at'];

	protected $dates = ['deleted_at'];

	
	public function personas()
	{
		return $this->belongsToMany('awebss\Persona','per_id');
	}
	
	
}
