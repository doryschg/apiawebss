<?php

namespace awebss\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\SoftDeletes;

class Paciente extends Model
{
    
	//use SoftDeletes;

	protected $table="paciente";

    protected $primaryKey = 'pac_id';

	protected $fillable = array('pac_grupo_sanguineo','pac_alergia','pac_hist_clinico');
	
	protected $hidden = ['created_at','updated_at','userid_at','deleted_at'];

	protected $dates = ['deleted_at'];

	protected $softDelete = true;
	
	public function persona()
	{
		return $this->belongsTo('awebss\Models\Persona','per_id');
	}

	public function seguro()
	{	
		
		return $this->belongsTo('awebss\Modles\Seguro','seg_id');
	}
		public function establecimiento_salud()
	{	
		
		return $this->belongsTo('awebss\Modles\Establecimiento_salud','es_id');
	}
}
