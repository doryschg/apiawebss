<?php

namespace awebss\Models;

use Illuminate\Database\Eloquent\Model;

class Paciente_establecimiento extends Model
{
    
    protected $table='paciente_establecimiento';

	protected $primaryKey = 'pe_id';

	protected $fillable = array('pe_hist_clinico','pe_estado');
	
	protected $hidden = ['created_at','updated_at','userid_at','deleted_at'];

	protected $dates=['deleted_at'];

	//protected $softDelete = true;

	public function establecimiento_salud()
	{
		
		return $this->hasMany('awebss\Models\Establecimiento_salud', 'es_id');
	}

	public function paciente()
	{
		
		return $this->hasMany('awebss\Models\Paciente', 'pac_id');
	}


public function scopePaciente_establecimientos($query, $pac_id)
 {

return $query->where('pac_id',$pac_id)->get();

}


	public function scopeInactivo_paciente($query, $pac_id, $es_id_origen)
 {

        $query=$query->where('pac_id',$pac_id)->get();

        foreach($query as $paciente)
        {  
        $es_id=$paciente->es_id;

        if  ($es_id==$es_id_origen)
         
        $pe_id=$paciente->pe_id; 
        
        }
        $paciente_es=Paciente_establecimiento::find($pe_id);
        $paciente_es->pe_estado='INACTIVO';
        $paciente_es->save();
        return $paciente_es;

}

	public function scopeActivar_paciente($query, $pac_id, $es_id)
 {

        $query=$query->where('pac_id',$pac_id)->get();

        foreach($query as $paciente) 

        {  

        $es_id_p=$paciente->es_id;

        if($es_id_p==$es_id)

         
        $pe_id=$paciente->pe_id;
        
        }

        $paciente_es =Paciente_establecimiento::find($pe_id);
        $paciente_es->pe_estado='ACTIVO';
        $paciente_es->save();

        return $paciente_es;
        

}

	public function scopeDesactivar_paciente($query, $pac_id)
 {

        $query=$query->where('pac_id',$pac_id)->get();

        foreach($query as $paciente)
        {  
        $pe_id=$paciente->pe_id; 
        $paciente_est=Paciente_establecimiento::find($pe_id);
        $paciente_est->pe_estado='INACTIVO';
        $paciente_est->save();
        }

}

	public function scopeCrear_paciente($query, $es_id,$pac_id,$pe_hist_clinico,$userid_at)
 {

        $paciente_establecimiento= new Paciente_establecimiento();
        $paciente_establecimiento->es_id=$es_id;
        $paciente_establecimiento->pac_id=$pac_id;
        $paciente_establecimiento->pe_hist_clinico=$pe_hist_clinico;
        $paciente_establecimiento->pe_estado='ACTIVO';
        $paciente_establecimiento->userid_at=$userid_at;
        $paciente_establecimiento->save();

        return $paciente_establecimiento;

}


}
