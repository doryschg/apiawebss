<?php

namespace awebss\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\SoftDeletes;

class Institucion extends Model
{
    use SoftDeletes;

    protected $table='institucion';

	protected $primaryKey = 'ins_id';

	protected $fillable = array('ins_nombre');
	
	protected $hidden = ['created_at','updated_at','userid_at','deleted_at'];

	protected $dates = ['deleted_at'];

	protected $softDelete = true;


	public function establecimiento_salud()
	{
		
		return $this->HasMany('awebss\Models\Establecimiento_salud','es_id');
	}

	public function subsector()
	{
		
		return $this->belongsTo('awebss\Models\Subsector','ss_id');
	}

}
