<?php

namespace awebss\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\SoftDeletes;

class Enfermera extends Model
{
     use SoftDeletes;

    protected $table="enfermera";

    protected $primaryKey = 'enf_id';

	protected $fillable = array();
	
	protected $hidden = ['created_at','updated_at','userid_at','deleted_at'];

	protected $dates = ['deleted_at'];

public function funcionario()
	{
		return $this->belongsTo('awebss\Models\Funcionario','fun_id');
	}
}
