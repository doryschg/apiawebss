<?php

namespace awebss;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Carbon;
use Validator;
use Hash;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;


class User extends Authenticatable
{  
    //use SoftDeletes;

    protected $table='_usuario';

	protected $fillable = array('usu_nick','password','usu_inicio_vigencia','usu_fin_vigencia');

	protected $hidden = ['created_at','updated_at','userid_at','deleted_at','password','remember_token',];

    //protected $dates = ['created_at','updated_at','usu_inicio_vigencia','usu_fin_vigencia'];

    protected $dates = ['deleted_at'];


public function rol()
	{
		
return $this->belongsTo('awebss\Modelos\Rol','rol_id');
	}

	public function persona()
	{
		
return $this->belongsTo('awebss\Modelos\Persona','per_id');
	}

public function usuario_servicio()
	{
		
return $this->hasMany('awebss\Modelos\Usuario_servicio','us_id');
	}

	
public function scopeGenerar_contraseña($query, $per_ci,$per_fecha_nacimiento)
 {

 $fecha_nacimiento = new \Carbon\Carbon($per_fecha_nacimiento);

$año = $fecha_nacimiento->format('Y');
$mes = $fecha_nacimiento->format('m');
$dia = $fecha_nacimiento->format('d');

$contrasena=$per_ci.$dia.$mes.$año; 

return $contrasena;
}

public function scopeCrear_cuenta($query, $contrasena,$per_ci,$per_id)
 {

        $usuario= new User();
        $usuario->per_id=$per_id;
        $usuario->usu_nick=$per_ci;
        $usuario->password=Hash::make($contrasena);
        $usuario->usu_clave_publica='NO DEFINIDO';
        $usuario->usu_inicio_vigencia=Carbon::now();
        $usuario->usu_fin_vigencia=Carbon::now()->addYears(2);
        $usuario->userid_at=JWTAuth::toUser()->id;
        $usuario->save();
		return $usuario;

}

}
