<?php

namespace awebss\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\SoftDeletes;

class Medico_especialidad extends Model
{
    use SoftDeletes;

    protected $table="medico_especialidad";

    protected $primaryKey = 'me_id';

	
	protected $fillable = array('me_descripcion','me_casa_superior','me_titulacion');

	protected $hidden = ['created_at','updated_at','userid_at','deleted_at'];

	protected $hidden = ['updated_at','userid_at','deleted_at'];

	protected $dates = ['deleted_at'];
	

public function medico()
	{
		return $this->belongsTo('awebss\Models\Medico','med_id');
	}
public function especialidad()
	{
		return $this->belongsTo('awebss\Models\Especialidad','esp_id');
	}
	
}
