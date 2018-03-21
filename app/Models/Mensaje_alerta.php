<?php

namespace awebss\Models;

use Illuminate\Database\Eloquent\Model;

//use Illuminate\Database\Eloquent\SoftDeletes;

class Mensaje_alerta extends Model
{
    //use SoftDeletes;

    protected $table='_mensaje_alerta';

	protected $primaryKey = 'men_id';

	protected $fillable = array('men_encabezado','men_cuerpo','men_despedida','men_tipo');
	
	protected $hidden = ['created_at','updated_at','userid_at','deleted_at'];

	protected $dates = ['deleted_at'];

	//protected $softDelete = true;

	
	public function alerta_temprana_vacuna()
	{
		return $this->hasMany('awebss\Models\Alerta_temprana_vacuna', 'atv_id');
	}

	public function imagen_prevencion()
	{
		return $this->hasOne('awebss\Models\Imagen_prevencion', 'ip_id');
	}

		public function configuracion_alerta()
	{
		return $this->hasMany('awebss\Models\Configuracion_alerta', 'ca_id');
	}

	
}
