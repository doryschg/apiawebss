<?php

namespace awebss\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\SoftDeletes;

class Persona2 extends Model
{
    use SoftDeletes;
    
protected $table="nueva_persona";

protected $primaryKey='per_id';

	
protected $fillable = array('per_ci','per_ci_expedido','per_nombres','per_apellido_primero','per_apellido_segundo','per_fecha_nacimiento','per_genero','per_email','per_tipo_permanencia','per_numero_celular','per_tipo_documento','per_zona_comunidad','per_avenida_calle','per_numero','per_nacion','per_valida','per_origen');
	
protected $hidden = ['created_at','updated_at','userid_at','deleted_at'];

protected $dates=['deleted_at','per_fecha_nacimiento'];

	
}
