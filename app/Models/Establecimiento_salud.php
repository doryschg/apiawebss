<?php

namespace awebss\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\SoftDeletes;

class Establecimiento_salud extends Model
{
    use SoftDeletes;

    protected $table="establecimiento_salud";

    protected $primaryKey = 'es_id';

	// Atributos que se pueden asignar de manera masiva.
	protected $fillable = array('es_nombre','es_nivel','es_nit','es_fecha_inicio_actividad','es_zona_localidad_comuni','es_avenida_calle','es_numero','es_horas','es_inicio_atencion','es_final_atencion','es_latitud','es_longitud','es_altitud','es_codigo','es_fax','es_correo_electronico','es_direccion_web','es_fecha_creacion','es_numero_rm');

	protected $hidden = ['created_at','updated_at','userid_at','deleted_at'];

	protected $dates = ['deleted_at'];

	protected $softDelete = true;

		public function tipo()
	{	
		
		return $this->belongsto('awebss\Models\Tipo','tip_id');
	}

		public function red()
	{	
		return $this->belongsto('awebss\Models\Red','red_id');
	}

		public function municipio()
	{	
		return $this->belongsto('awebss\Models\Municipio','mun_id');
	}

	public function telefono()
	{	
		return $this->hasMany('awebss\Models\Telefono','te_id');
	}

	public function consultorio()
	{	
		return $this->hasMany('awebss\Models\Consultorio','con_id');
	}
	public function imagen_est()
	{	
		return $this->hasOne('awebss\Models\Imagen_est','ie_id');
	}

	public function boleta_referencia()
	{	
		return $this->hasMany('awebss\Models\boleta_referencia','br_id');
	}


}
