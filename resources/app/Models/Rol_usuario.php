<?php

namespace awebss\Models;
use Illuminate\Database\Eloquent\Model;

class Rol_usuario extends Model
{
    protected $table='_rol_usuario';

	protected $primaryKey = 'ru_id';

	protected $fillable = array('ru_estado');
	
	protected $hidden = ['created_at','updated_at','userid_at','deleted_at'];

	protected $dates = ['deleted_at'];

	 protected $softDelete = true;

	public function usuario()
	{
		
		return $this->belongsTo('awebss\User','id');
	} 

	public function scopeCrear_rol($query, $usu_id,$rol_id)
 {
       
        $roles=new \awebss\Models\Rol_usuario();
        $roles->rol_id=$rol_id;
        $roles->usu_id=$usu_id;
        $roles->ru_estado='ACTIVO';
        $roles->userid_at='2';
        $roles->save();
        
		return $roles;

} //

}
