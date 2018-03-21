<?php

namespace awebss\Http\Controllers;
use Illuminate\Http\Request;
use awebss\Http\Requests;
use awebss\Models\Cita;
use awebss\Models\Paciente_establecimiento;
use awebss\Models\Servicio_establecimiento;
use awebss\Models\Servicio;
use Validator;
use Carbon;

class ReporteController extends Controller
{
    
    public function show($es_id, Request $request)
    {
         $validator = Validator::make($request->all(), [
            
            'se_id' => 'required',
        ]);

        if ($validator->fails()) 

        {
             return $validator->errors()->all();
        }
        $fecha1=$request->fecha;
        $fecha2=$request->fecha1;

        if($request->se_id==0)
       {
        $servicios=Servicio_establecimiento::where('es_id',$es_id)->join('servicio','servicio.ser_id','=','servicio_establecimiento.ser_id')->get(['servicio.ser_id','ser_nombre','servicio_establecimiento.se_id']);

       foreach ($servicios as $servicios) {

    $cita1=Cita::where('cit_es_id',$es_id)->where('cit_fecha_consulta','>=',$fecha1)->where('cit_fecha_consulta','<=',$fecha2)->join('paciente','paciente.pac_id','=','cita.pac_id')->join('persona','persona.per_id','=','paciente.per_id')->join('consultorio','consultorio.con_id','=','cita.cit_con_id')->join('servicio_consultorio','servicio_consultorio.sc_id','=','cita.cit_se_id')->join('servicio_establecimiento','servicio_establecimiento.se_id','=','servicio_consultorio.se_id')->where('servicio_establecimiento.se_id',$servicios->se_id)->join('servicio','servicio.ser_id','=','servicio_establecimiento.ser_id')->where('cita.cit_estado_pago',TRUE)->get(['servicio_establecimiento.se_id']);

     $cita2=Cita::where('cit_es_id',$es_id)->where('cit_fecha_consulta','>=',$fecha1)->where('cit_fecha_consulta','<=',$fecha2)->join('paciente','paciente.pac_id','=','cita.pac_id')->join('persona','persona.per_id','=','paciente.per_id')->join('consultorio','consultorio.con_id','=','cita.cit_con_id')->join('servicio_consultorio','servicio_consultorio.sc_id','=','cita.cit_se_id')->join('servicio_establecimiento','servicio_establecimiento.se_id','=','servicio_consultorio.se_id')->where('servicio_establecimiento.se_id',$servicios->se_id)->join('servicio','servicio.ser_id','=','servicio_establecimiento.ser_id')->where('cita.cit_estado_pago',TRUE)->where('persona.per_genero','M')->get(['servicio_establecimiento.se_id']);

     $cita3=Cita::where('cit_es_id',$es_id)->where('cit_fecha_consulta','>=',$fecha1)->where('cit_fecha_consulta','<=',$fecha2)->join('paciente','paciente.pac_id','=','cita.pac_id')->join('persona','persona.per_id','=','paciente.per_id')->join('consultorio','consultorio.con_id','=','cita.cit_con_id')->join('servicio_consultorio','servicio_consultorio.sc_id','=','cita.cit_se_id')->join('servicio_establecimiento','servicio_establecimiento.se_id','=','servicio_consultorio.se_id')->where('servicio_establecimiento.se_id',$servicios->se_id)->join('servicio','servicio.ser_id','=','servicio_establecimiento.ser_id')->where('cita.cit_estado_pago',TRUE)->where('persona.per_genero','F')->get(['servicio_establecimiento.se_id']);

    $array[]=['servicio'=>$servicios->ser_nombre,'cita1'=>count($cita1),'cita2'=>count($cita2),'cita3'=>count($cita3)]; 
}

return response()->json(['status'=>'ok','mensaje'=>'exito','cita'=>$array],200);  
}

$servicio_establecimiento=Servicio_establecimiento::find($request->se_id);
$servicio=Servicio::find($servicio_establecimiento->ser_id);

$cita1=Cita::where('cit_es_id',$es_id)->where('cit_fecha_consulta','>=',$fecha1)->where('cit_fecha_consulta','<=',$fecha2)->join('paciente','paciente.pac_id','=','cita.pac_id')->join('persona','persona.per_id','=','paciente.per_id')->join('consultorio','consultorio.con_id','=','cita.cit_con_id')->join('servicio_consultorio','servicio_consultorio.sc_id','=','cita.cit_se_id')->join('servicio_establecimiento','servicio_establecimiento.se_id','=','servicio_consultorio.se_id')->where('servicio_establecimiento.se_id',$request->se_id)->where('cita.cit_estado_pago',TRUE)->get(['servicio_establecimiento.se_id']);
     //atendidos todos los servicios
$cita2=Cita::where('cit_es_id',$es_id)->where('cit_fecha_consulta','>=',$fecha1)->where('cit_fecha_consulta','<=',$fecha2)->join('paciente','paciente.pac_id','=','cita.pac_id')->join('persona','persona.per_id','=','paciente.per_id')->join('consultorio','consultorio.con_id','=','cita.cit_con_id')->join('servicio_consultorio','servicio_consultorio.sc_id','=','cita.cit_se_id')->join('servicio_establecimiento','servicio_establecimiento.se_id','=','servicio_consultorio.se_id')->where('servicio_establecimiento.se_id',$request->se_id)->where('cita.cit_estado_pago',TRUE)->where('persona.per_genero','M')->get(['servicio_establecimiento.se_id']);
           //atendidos todos los servicios masculinos
$cita3=Cita::where('cit_es_id',$es_id)->where('cit_fecha_consulta','>=',$fecha1)->where('cit_fecha_consulta','<=',$fecha2)->join('paciente','paciente.pac_id','=','cita.pac_id')->join('persona','persona.per_id','=','paciente.per_id')->join('consultorio','consultorio.con_id','=','cita.cit_con_id')->join('servicio_consultorio','servicio_consultorio.sc_id','=','cita.cit_se_id')->join('servicio_establecimiento','servicio_establecimiento.se_id','=','servicio_consultorio.se_id')->where('servicio_establecimiento.se_id',$request->se_id)->where('cita.cit_estado_pago',TRUE)->where('persona.per_genero','F')->get(['servicio_establecimiento.se_id']);
         //atendidos todos los servicios femenino
    $array[]=['servicio'=>$servicio->ser_nombre,'cita1'=>count($cita1),'cita2'=>count($cita2),'cita3'=>count($cita3)];

return response()->json(['status'=>'ok','mensaje'=>'exito','cita'=>$array],200); 
    }

    public function index(Request $request)
    {

    $fecha_actual=Carbon::now();

$edades=[0,6,12,60,120,180,240,480,600,720,2000];

for($i=0;$i<sizeof($edades)-1;$i++)
{   
    $contador=0;

    $pacientes=Paciente_establecimiento::where('es_id',$request->es_id)->join('paciente','paciente.pac_id','=','paciente_establecimiento.pac_id')->join('persona','persona.per_id','=','paciente.per_id')->join('cita','cita.pac_id','=','paciente.pac_id')->where('cita.cit_fecha_consulta','>=',$request->fecha)->where('cita.cit_fecha_consulta','<=',$request->fecha1)->where('persona.per_genero','M')->where('cita.cit_estado_pago','TRUE')->get(['persona.per_fecha_nacimiento']);


    foreach ($pacientes as $pacientes) 
    {

    $fecha_nacimiento = new \Carbon\Carbon($pacientes->per_fecha_nacimiento);

    $edad=date_diff($fecha_actual,$fecha_nacimiento)->y*12 + date_diff($fecha_actual,$fecha_nacimiento)->m;

   if($edad>=$edades[$i] && $edad<$edades[$i+1])
    {

    $contador=$contador+1;
    } }



    $contador1=0;

   $pacientes1=Paciente_establecimiento::where('es_id',$request->es_id)->join('paciente','paciente.pac_id','=','paciente_establecimiento.pac_id')->join('persona','persona.per_id','=','paciente.per_id')->join('cita','cita.pac_id','=','paciente.pac_id')->where('cita.cit_fecha_consulta','>=',$request->fecha)->where('cita.cit_fecha_consulta','<=',$request->fecha1)->where('persona.per_genero','F')->where('cita.cit_estado_pago','TRUE')->get(['persona.per_fecha_nacimiento']);


    foreach ($pacientes1 as $pacientes1) 
    {

    $fecha_nacimiento = new \Carbon\Carbon($pacientes1->per_fecha_nacimiento);

    $edad=date_diff($fecha_actual,$fecha_nacimiento)->y*12 + date_diff($fecha_actual,$fecha_nacimiento)->m;

   if($edad>=$edades[$i] && $edad<$edades[$i+1])
    {

    $contador1=$contador1+1;
    } }


$array[]=['edad'=>$edades[$i].'-'.$edades[$i+1], 'pacientes'=>$contador,'pacientes1'=>$contador1];
}


return response()->json(['status'=>'ok','mensaje'=>'exito','atendidos'=>$array],200);

    }

}
