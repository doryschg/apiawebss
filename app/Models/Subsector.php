<?php

namespace awebss\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\SoftDeletes;

class Subsector extends Model
{
    use SoftDeletes;

    protected $table='subsector';

	protected $primaryKey = 'ss_id';

	protected $fillable = array('ss_nombre');
	
	protected $hidden = ['created_at','updated_at','userid_at','deleted_at'];

	protected $dates = ['deleted_at'];

	 protected $softDelete = true;

	public function institucion()
	{
		
		return $this->belongsTo('awebss\Models\Institucion','ins_id');
	}
}
