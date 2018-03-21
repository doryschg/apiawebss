<?php

namespace awebss\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon;
use Validator;
use Hash;
use Str;


class Persona extends Model
{
    
protected $table="persona";
protected $primaryKey='per_id';

protected $fillable = array('per_ci','per_ci_expedido','per_nombres','per_apellido_primero','per_apellido_segundo','per_fecha_nacimiento','per_genero','per_email','per_tipo_permanencia','per_numero_celular','per_clave_publica','per_tipo_documento','per_pais');
	
protected $hidden = ['created_at','updated_at','userid_at','deleted_at'];

//protected $encrypts=['per_nombres','per_apellido_primero','per_apellido_segundo'];

protected $dates=['deleted_at','per_fecha_nacimiento'];


public function direccion()
	{	
		
		return $this->hasOne('awebss\Models\Direccion','dir_id');
	}
	public function paciente()
	{	
		
		return $this->hasOne('awebss\Models\Paciente','pac_id');
	}
	
	public function familiar()
	{	
		
		return $this->hasMany('awebss\Models\Familiar','fam_id');
	}
		public function funcionario()
	{	
		
		return $this->hasOne('awebss\Models\Funcionario','fun_id');
	}

		public function imagen()
	{	
		
		return $this->hasOne('awebss\Models\Imagen','ima_id');
	}

 public function scopeCalcular_edad($query, $per_fecha_nacimiento)
 {
$fecha_actual=carbon::now();
$edades= date_diff($fecha_actual,$per_fecha_nacimiento);

$edad=$edades->y;
$mes_n=$edades->m;
$dia_a=$edades->d;

$resultado=compact('edad','mes_n','dia_a');

return $resultado;

}

public function scopeEdad($query, $per_fecha_nacimiento)
 {

$fecha_nacimiento = new \Carbon\Carbon($per_fecha_nacimiento);

$año_n = $fecha_nacimiento->format('Y');
$mes_n = $fecha_nacimiento->format('m');
$dia_n = $fecha_nacimiento->format('d');  
$edad = Carbon::createFromDate($año_n,$mes_n,$dia_n)->age;

if($edad=="")    
{
	return -1;
} 
return $edad;
}


    }
