<?php

namespace awebss\Models;


use Illuminate\Database\Eloquent\Model;

//use Illuminate\Database\Eloquent\SoftDeletes;

class Funcionario extends Model
{
    
   // use SoftDeletes;

     protected $table="funcionario";

    protected $primaryKey = 'fun_id';

	// Atributos que se pueden asignar de manera masiva.
	protected $fillable = array('fun_profesion');

	protected $hidden = ['created_at','updated_at','userid_at','deleted_at'];

	protected $dates=['deleted_at'];

	 //protected $softDelete = true;



	public function persona()
	{
		return $this->belongsTo('awebss\Models\Persona','per_id');
	}

	public function enfermera()
	{
		return $this->hasOne('awebss\Models\Enfermera','enf_id');
	}

	public function medico()
	{
		return $this->hasOne('awebss\Models\Medico','med_id');
	}



}

