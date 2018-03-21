<?php

namespace awebss\Models;

use Illuminate\Database\Eloquent\Model;

class Detalle_vacuna extends Model
{

 //use SoftDeletes;
   protected $table="detalle_vacuna";
   protected $primaryKey='dev_id';
	// Atributos que se pueden asignar de manera masiva.
	protected $fillable = array('dev_fecha_aplicada','dev_fecha_estimada','dev_observacion','dev_estado_aplicado','dev_tipo_servicio','dev_edad_aplicacion');
	
	// Aquí ponemos los campos que no queremos que se devuelvan en las consultas.
	protected $hidden = ['created_at','updated_at','userid_at','deleted_at'];

	protected $dates = ['deleted_at'];

    

}
