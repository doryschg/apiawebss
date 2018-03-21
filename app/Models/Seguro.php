<?php

namespace awebss\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\SoftDeletes;

class Seguro extends Model
{
    use SoftDeletes;

    protected $table='seguro';

	protected $primaryKey = 'seg_id';

	protected $fillable = array('seg_nombre');
	
	protected $hidden = ['created_at','updated_at','userid_at','deleted_at'];

	protected $dates = ['deleted_at'];

	 protected $softDelete = true;

	public function paciente()
	{
		
		return $this->belongsTo('awebss\Models\Paciente','pac_id');
	}  //

	
}
