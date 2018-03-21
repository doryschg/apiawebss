<?php

Route::group(['middleware' => 'cors'], function () 
{  

Route::get('/', function () {
    
    return response()->json(['status'=>'ok','mensaje'=>'APLICACION WEB PARA SERVICIOS DE SALUD'],200);
});

    Route::resource('conexion','ConexionController',['only' => ['store', 'update','index','show']]);
    Route::resource('municipios','MunicipioController',['only' => ['index', 'store', 'update', 'destroy', 'show']]);
    Route::resource('departamentos','DepartamentoController',['only' => ['index', 'store', 'update', 'destroy', 'show']]);
    Route::resource('regiones','RegionController',['only' => ['index', 'store', 'update', 'destroy', 'show']]);
    Route::resource('provincias','ProvinciaController',['only' => ['index', 'store', 'update', 'destroy', 'show']]);
    Route::resource('tipos','TipoController',['only' => ['index', 'store', 'update', 'destroy', 'show']]);
    Route::resource('reds','RedController',['only' => ['index', 'store', 'update', 'destroy', 'show']]);
    Route::resource('institucion','InstitucionController',['only' => ['index', 'store', 'update', 'destroy', 'show']]);
    Route::resource('seguro','SeguroController',['only' => ['index', 'store', 'update', 'destroy', 'show']]);
    Route::get('cargo','CargoController@index');
    Route::get('paises','PaisController@index');
    Route::resource('subsector','SubsectorController',['only' => ['index', 'store']]);

    Route::post('auth_login','ApiAuthController@userAuth');

    Route::resource('roles','RolController',['only' => ['index', 'store', 'show']]);

   Route::put('roles_usuarios/{ru_id}','Rol_usuarioController@update');

    Route::get('permisos_roles/{rol_id}','RolController@roles_permisos');

    Route::resource('usuarios','UsuarioController',['only' => ['index', 'store', 'update', 'destroy', 'show']]);

    Route::get('usuarios_establecimiento/{es_id}','UsuarioController@usuarios_establecimiento'); 
    
    Route::get('usuarios_estados/{usu_id}/{es_id}','UsuarioController@usuarios_estados');

    Route::get('usuarios_cuentas/{per_id}','UsuarioController@usuarios_cuentas');

    Route::resource('personas','PersonaController',['only' => ['index', 'store', 'update', 'show']]); 

    Route::post('personas_temporales','Persona2Controller@store');

    Route::post('temporales','PersonaController@pasar_persona_temporal');

    Route::get('personas_temporales/{per_id}','Persona2Controller@show'); 
    
    Route::get('personasb/{per_ci}','PersonaController@buscar');

    Route::post('personas_familiar','FamiliarController@crear_persona_familiar');
    Route::get('personas_usuarios/{per_ci}','PersonaController@habilitar_cuentas');

    Route::resource('familiar','FamiliarController',['only' => ['store', 'update', 'show','destroy']]);

	Route::resource('establecimiento_salud','Establecimiento_saludController',['only' => ['index', 'store', 'update', 'destroy', 'show']]);
    
    Route::get('establecimiento_salud_red/{red_id}','Establecimiento_saludController@listar_establecimientos_red');
    Route::resource('pacientes','PacienteController',['only' => ['index', 'store', 'update', 'show']]);

    Route::get('pacientes_es/{es_id}','PacienteController@paciente_es');

    Route::get('paciente_ci/{per_ci}','PacienteController@paciente_ci');

     Route::get('pacientes_cedulas/{per_ci}','PacienteController@buscar_paciente');
    Route::post('pacientes_personas','PacienteController@crear_persona_paciente');

   Route::get('pacientes_edades/{pac_id}','PacienteController@calcular_edad');
  Route::get('pacientes_personas/{per_id}','PacienteController@ver_paciente_per_id');

    Route::resource('funcionarios','FuncionarioController',['only' => ['index', 'store', 'update', 'show']]);
    
    Route::get('funcionarioes/{es_id}','FuncionarioController@listar_funcionario');

    Route::get('funcionarios_per/{per_id}','FuncionarioController@ver_funcionario');

    Route::post('funcionarios_personas/{es_id}','FuncionarioController@crear_funcionario');

    Route::get('funcionarios_horarios/{fun_id}','FuncionarioController@listar_horario_funcionario');

    Route::get('funcionarios_establecimientos/{es_id}','FuncionarioController@listar_funcionario_sincuenta');

     Route::get('funcionarios_establecimientos/{fun_id}/{es_id}','FuncionarioController@buscar_funcionario');

    Route::resource('servicios','ServicioController',['only' => ['index','show']]);

    Route::resource('consultorios','ConsultorioController', ['only' => ['index','store', 'update', 'destroy', 'show']]);
    Route::resource('servicios_consultorios','Servicio_consultorioController', ['only' => [ 'store','show','destroy']]);
    Route::get('consultorios_establecimientos/{es_id}','ConsultorioController@listar_consultorio_es');
    
    Route::resource('servicios_establecimientos','Servicio_establecimientoController',['only' => ['index','store', 'update', 'destroy', 'show']]);

    Route::get('establecimientos_servicios/{ser_id}','Servicio_establecimientoController@establecimiento_salud');

    Route::get('establecimiento_presta/{es_id}','Servicio_establecimientoController@listar_servicios');

    Route::get('servicios_no_referencias/{es_id}','Servicio_establecimientoController@servicios_que_no_requieren_referenciacion');

    Route::get('servicios_no_establecimientos/{es_id}','Servicio_establecimientoController@agregar_servicios');

    Route::resource('referencia','Boleta_referenciaController',['only' => ['index', 'store', 'update', 'destroy', 'show']]);

    Route::get('pacientes_referencias/{pac_id}','Paciente_establecimientoController@listar_referencia_paciente');

    Route::get('referencias_establecimientos_origen/{es_id}','Boleta_referenciaController@lista_referencia');

    Route::get('referencias_establecimientos_destino/{es_id}','Boleta_referenciaController@lista_referencia_destino');

    Route::get('establecimientos_referencia/{es_id}','Boleta_referenciaController@listar_establecimientos_referencia');
    Route::get('red_referencias/{es_id}','Boleta_referenciaController@red_referencias');
   Route::put('referencias_estados/{br_id}','Boleta_referenciaController@editar');

    Route::get('estado_referencia/{es_id}','Boleta_referenciaController@estado_referencia');

    Route::get('estado_referencia_destino/{es_id_destino}','Boleta_referenciaController@estado_referencia_destino');

    Route::get('referencia_contra/{br_id}','Boleta_referenciaController@referencia_contra');

    Route::resource('contrareferencia','Boleta_contrareferenciaController',['only' => ['index', 'store', 'update', 'destroy', 'show']]);

    Route::get('contrareferencias_establecimientos_origen/{es_id}','Boleta_contrareferenciaController@lista_contrareferencia');
   Route::get('contrareferencias_establecimientos_destino/{es_id}','Boleta_contrareferenciaController@lista_contrareferencia_destino');
    Route::put('contrareferencias_estados/{bc_id}','Boleta_contrareferenciaController@editar');

      Route::resource('enfermedad','EnfermedadController',['only' => ['index', 'store', 'update', 'destroy', 'show']]);
     Route::resource('suplementos','SuplementoController',['only' => ['index', 'store', 'update', 'destroy', 'show']]);
     Route::resource('vacunas','VacunaController',['only' => ['index', 'store', 'update', 'destroy', 'show']]);
     Route::resource('dosis_vacuna','Dosis_vacunaController',['only' => ['index', 'store', 'update', 'destroy', 'show']]);
     Route::resource('dosis_suplemento','Dosis_suplementoController',['only' => ['index', 'store', 'update', 'destroy', 'show']]);
     Route::resource('configuracion_alerta','ConfiguracionAlertaController',['only' => ['index', 'store', 'update', 'destroy', 'show']]);

     Route::resource('vacuna_alerta','AlertaVacunaController',['only' => ['index', 'store', 'update', 'destroy', 'show']]);

     Route::get('suplementos_dosis/{sup_id}','SuplementoController@listar_suplemento_dosis');

     Route::get('vacunas_dosis/{vac_id}','VacunaController@listar_vacuna_dosis');

     Route::resource('paciente_establecimiento','Paciente_establecimientoController',['only' => ['store', 'show']]);
     Route::get('pacientes_establecimientos/{pac_id}/{es_id}','Paciente_establecimientoController@buscar_paciente_establecimiento');
     Route::resource('configuracion_horarios','Configuracion_horarioController',['only' => ['index','store', 'update', 'destroy', 'show']]);
    Route::get('horarios_establecimientos/{es_id}','Configuracion_horarioController@listar_configuracion_horarios');
     Route::resource('configuracion_turnos','Configuracion_turnoController',['only' => ['index','store', 'destroy']]);

     Route::get('horarios_consultorios/{con_id}','Configuracion_horarioController@listar_turnos_por_consultorio');

    Route::resource('atiende_diariamente','AtiendeDiariamenteController',['only' => ['index','store', 'update', 'show']]);

    Route::get('horarios_atiende_diariamente/{ch_id}','AtiendeDiariamenteController@generar_atiendes');

    Route::get('reservas_atiende/{pre_id}','AtiendeDiariamenteController@listar_horarios_diariamente');

    Route::resource('citas','CitaController',['only' => ['store', 'update', 'show','destroy']]);

     Route::put('citas_confirmacion/{cit_id}','CitaController@confirmar_cita');
    Route::get('citas_establecimientos/{es_id}','CitaController@listar_citas_fechas');

    Route::get('reservas_pacientes/{pac_id}','CitaController@listar_citas_paciente');

    Route::get('reservas_medicos/{fe_id}','CitaController@listar_citas_medico');

    Route::get('reservas_dias/{es_id}','CitaController@listar_citas_dia');

    Route::get('citas_medicos/{fe_id}','CitaController@listar_citas_medico_fecha');

    Route::resource('zonas','ZonaController',['only' => ['index','show']]);

    Route::get('establecimientos_por_zona/{zon_id}','ZonaController@establecimientos_por_zona');

    Route::get('establecimientos_reservas/{pac_id}','Paciente_establecimientoController@listar_establecimientos_habilitados');
    Route::resource('medicos','MedicoController',['only' => ['index','show']]);

    Route::resource('enfermeras','EnfermeraController',['only' => ['index','show']]);
    Route::resource('detalle_vacunas','Detalle_vacunaController',['only' => ['index','show','store','update','destroy']]);

    Route::resource('atendidos','ReporteController',['only' => ['show','index']]);

    Route::resource('crecimientos','Crecimiento_signos_vitalesController',['only' => ['show','store','update']]);

}
    );


