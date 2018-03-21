<?php

Route::group(['middleware' => 'cors'], function () 
{  

Route::get('/', function () {
    
    return response()->json(['status'=>'ok','mensaje'=>'APLICACION WEB PARA SERVICIOS DE SALUD'],200);
});

// sirve para listar los establecimientos de salud y el tipo de conexion que tiene esto sirve para realizar la conexion con el sise

    Route::resource('conexion','ConexionController',['only' => ['store', 'update','index','show']]);
    //tablas administrativas listar, ver, crear, eliminar de tablas administrativas
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

    //autentification sirve para la autentificacion de personas a la aplicacion

    Route::post('auth_login','ApiAuthController@userAuth');

    // permite operaciones con la tabla rol

    Route::resource('roles','RolController',['only' => ['index', 'store', 'show']]);

    // roles usuarios

    Route::put('roles_usuarios','Rol_usuarioController@update');

    //lista los permisos de un rol determinado

    Route::get('permisos_roles/{rol_id}','RolController@roles_permisos');

    // permite operaciones con los usuarios del sistema

    Route::resource('usuarios','UsuarioController',['only' => ['index', 'store', 'update', 'destroy', 'show']]);

    // listado de los usuarios de un determinado establecimeinto de salud

    Route::get('usuarios_establecimiento/{es_id}','UsuarioController@usuarios_establecimiento'); 

    // verifica si un usuario pertenece a un establecimiento de salud
    
    Route::get('usuarios_estados/{usu_id}/{es_id}','UsuarioController@usuarios_estados');

    // habilita cuenta para un paciente 
    //actualizar

    Route::get('usuarios_cuentas/{per_id}','UsuarioController@usuarios_cuentas');

    // personas
    // operaciones de rest para personas

    Route::resource('personas','PersonaController',['only' => ['index', 'store', 'update', 'show']]); 

    // permite la creacion de un registro en la tabla persona temporal

    Route::post('personas_temporales','Persona2Controller@store');

    // permite pasar registros de la tabla nueva persona a la tabla persona

    Route::post('temporales','PersonaController@pasar_persona_temporal');

    // permite ver un registro de la tabla temporal dado el per_id
    Route::get('personas_temporales/{per_id}','Persona2Controller@show'); 

    // permite la busqueda de personas en las tablas nueva_persona y persona
    
    Route::get('personasb/{per_ci}','PersonaController@buscar');

    // permite la creacion de  familiares de una persona
    Route::post('personas_familiar','FamiliarController@crear_persona_familiar');

    // verifica si un ci es paciente en la bd para crear cuenta de usuario paciente
    // ACTUALIZAR

    Route::get('personas_usuarios/{per_ci}','PersonaController@habilitar_cuentas');

    // operaciones con la tabla familiar

    Route::resource('familiar','FamiliarController',['only' => ['store', 'update', 'show','destroy']]);

    //establecimiento de salud
    //operaciones de establecimiento de salud

	Route::resource('establecimiento_salud','Establecimiento_saludController',['only' => ['index', 'store', 'update', 'destroy', 'show']]);
    
//lista establecimientos por redes
    
    Route::get('establecimiento_salud_red/{red_id}','Establecimiento_saludController@listar_establecimientos_red');

    //pacientes
    //operaciones de pacientes

    Route::resource('pacientes','PacienteController',['only' => ['index', 'store', 'update', 'show']]);

    //lista a los paciendes de un determinado establecimiento de salud
    
    Route::get('pacientes_es/{es_id}','PacienteController@paciente_es');

    // permite buscar paciente por el ci

    Route::get('paciente_ci/{per_ci}','PacienteController@paciente_ci');

    // permite buscar paciente por el ci

     Route::get('pacientes_cedulas/{per_ci}','PacienteController@buscar_paciente');

    // permite la creacion de un paciente con persona

    Route::post('pacientes_personas','PacienteController@crear_persona_paciente');

    // permite calular la edad de un paciente dado el pac_id

   Route::get('pacientes_edades/{pac_id}','PacienteController@calcular_edad');

    // ver paciente por per_id

  Route::get('pacientes_personas/{per_id}','PacienteController@ver_paciente_per_id');

    //operaciones de funcionarios

    Route::resource('funcionarios','FuncionarioController',['only' => ['index', 'store', 'update', 'show']]);

   // lista funcionarios de un determinado establecimiento de salud
    
    Route::get('funcionarioes/{es_id}','FuncionarioController@listar_funcionario');
    
    // ver datos del funcionario por el per_id

    Route::get('funcionarios_per/{per_id}','FuncionarioController@ver_funcionario');

    // crear funcionarios de un determinado establecimiento de salud

    Route::post('funcionarios_personas/{es_id}','FuncionarioController@crear_funcionario');

    // lista los horarios de un funcionario

    Route::get('funcionarios_horarios/{fun_id}','FuncionarioController@listar_horario_funcionario');

    // lista los funcionarios de un establecimeinto de salud que no tengan cuenta

    Route::get('funcionarios_establecimientos/{es_id}','FuncionarioController@listar_funcionario_sincuenta');

    // verifica que un medico pertenesca a un establecimiento de salud

     Route::get('funcionarios_establecimientos/{fun_id}/{es_id}','FuncionarioController@buscar_funcionario');

    // operaciones con servicio
    Route::resource('servicios','ServicioController',['only' => ['index','show']]);

    Route::resource('consultorios','ConsultorioController', ['only' => ['index','store', 'update', 'destroy', 'show']]);

    // permite realizar operaciones con servicios de un consultorio
    Route::resource('servicios_consultorios','Servicio_consultorioController', ['only' => [ 'store','show','destroy']]);
    // permite listar los consultorios de un establecimiento

    Route::get('consultorios_establecimientos/{es_id}','ConsultorioController@listar_consultorio_es');
    
    // permite realizar operaciones con los servicios de salud que presta un establecimiento
    Route::resource('servicios_establecimientos','Servicio_establecimientoController',['only' => ['index','store', 'update', 'destroy', 'show']]);

    // permitle el listado de los establecimientos de salud que brindan un servicio

    Route::get('establecimientos_servicios/{ser_id}','Servicio_establecimientoController@establecimiento_salud');

    // lista los servicios un establecimiento de salud
    Route::get('establecimiento_presta/{es_id}','Servicio_establecimientoController@listar_servicios');

    // lista de servicios que no necesitan referenciacion por es_id

    Route::get('servicios_no_referencias/{es_id}','Servicio_establecimientoController@servicios_que_no_requieren_referenciacion');

    /// lista los servicios que no tiene un establecimiento de salud

    Route::get('servicios_no_establecimientos/{es_id}','Servicio_establecimientoController@agregar_servicios');

     // permite realizar operaciones con referencia

    Route::resource('referencia','Boleta_referenciaController',['only' => ['index', 'store', 'update', 'destroy', 'show']]);

    // listas las referencias de un paciente

    Route::get('pacientes_referencias/{pac_id}','Paciente_establecimientoController@listar_referencia_paciente');

    // listar  boletas de referencia de un establecimiento de salud origen

    Route::get('referencias_establecimientos_origen/{es_id}','Boleta_referenciaController@lista_referencia');

    // lista referencias del establecimiento destino

    Route::get('referencias_establecimientos_destino/{es_id}','Boleta_referenciaController@lista_referencia_destino');

    // lista establecimientos de para referenciar

    Route::get('establecimientos_referencia/{es_id}','Boleta_referenciaController@listar_establecimientos_referencia');
    
// lista los establecimientos de salud de una red para las referencias

    Route::get('red_referencias/{es_id}','Boleta_referenciaController@red_referencias');

// edita el campo estado de referencia de una boleta de referencia
 Route::put('referencias_estados/{br_id}','Boleta_referenciaController@editar');

// litado de las referencias con el estrao true del establecimieto origen

    Route::get('estado_referencia/{es_id}','Boleta_referenciaController@estado_referencia');

// listado de las referencias con el estado true del establecimiento destino

    Route::get('estado_referencia_destino/{es_id_destino}','Boleta_referenciaController@estado_referencia_destino');

// permite verificar si una referencia tiene contrareferencia

    Route::get('referencia_contra/{br_id}','Boleta_referenciaController@referencia_contra');

//operaciones con la tabla contrareferencia

    Route::resource('contrareferencia','Boleta_contrareferenciaController',['only' => ['index', 'store', 'update', 'destroy', 'show']]);

    //listar boletas de contrareferencia de un E.S. origen

    Route::get('contrareferencias_establecimientos_origen/{es_id}','Boleta_contrareferenciaController@lista_contrareferencia');

    // lista contrareferencias del establecimiento destino
Route::get('contrareferencias_establecimientos_destino/{es_id}','Boleta_contrareferenciaController@lista_contrareferencia_destino');

    // edita algunos campos de contrareferencia

    Route::put('contrareferencias_estados/{bc_id}','Boleta_contrareferenciaController@editar');

      // operaciones con enfermedad

      Route::resource('enfermedad','EnfermedadController',['only' => ['index', 'store', 'update', 'destroy', 'show']]);

    // operaciones con suplemento

     Route::resource('suplementos','SuplementoController',['only' => ['index', 'store', 'update', 'destroy', 'show']]);

     // operaciones con vacunas

     Route::resource('vacunas','VacunaController',['only' => ['index', 'store', 'update', 'destroy', 'show']]);

     // operaciones con dosis de vacuna

     Route::resource('dosis_vacuna','Dosis_vacunaController',['only' => ['index', 'store', 'update', 'destroy', 'show']]);

     // operaciones con dosis de suplemento

     Route::resource('dosis_suplemento','Dosis_suplementoController',['only' => ['index', 'store', 'update', 'destroy', 'show']]);

     // operaciones con configuracion alerta

     Route::resource('configuracion_alerta','ConfiguracionAlertaController',['only' => ['index', 'store', 'update', 'destroy', 'show']]);

     // operaciones con vacuna alerta

     Route::resource('vacuna_alerta','AlertaVacunaController',['only' => ['index', 'store', 'update', 'destroy', 'show']]);

     // lista los suplementos con sus dosis

     Route::get('suplementos_dosis/{sup_id}','SuplementoController@listar_suplemento_dosis');

     // lista las vacunas con sus dosis

     Route::get('vacunas_dosis/{vac_id}','VacunaController@listar_vacuna_dosis');

     // operaciones con paciente establecimiento

     Route::resource('paciente_establecimiento','Paciente_establecimientoController',['only' => ['store', 'show']]);

     // busca al pacientes de un establecimiento dado el pac_id y es_id

     Route::get('pacientes_establecimientos/{pac_id}/{es_id}','Paciente_establecimientoController@buscar_paciente_establecimiento');

     // permite realizar operaciones con configuracion horario

     Route::resource('configuracion_horarios','Configuracion_horarioController',['only' => ['index','store', 'update', 'destroy', 'show']]);

     // listar configuracion horarios dada un es_id

    Route::get('horarios_establecimientos/{es_id}','Configuracion_horarioController@listar_configuracion_horarios');

     //permite realizar operaciones con configuracion horario

     Route::resource('configuracion_turnos','Configuracion_turnoController',['only' => ['index','store', 'destroy']]);

     // listar turnos dado un consultorio

     Route::get('horarios_consultorios/{con_id}','Configuracion_horarioController@listar_turnos_por_consultorio');

    // operaciones con la tabla atiende diariamente

    Route::resource('atiende_diariamente','AtiendeDiariamenteController',['only' => ['index','store', 'update', 'show']]);

    // genera los atiendes dado una configuracion horario y los respectivos turnos

    Route::get('horarios_atiende_diariamente/{ch_id}','AtiendeDiariamenteController@generar_atiendes');

    //ver horarios diariamente para realizar reserva

    Route::get('reservas_atiende/{pre_id}','AtiendeDiariamenteController@listar_horarios_diariamente');

    // operaciones con citas

    Route::resource('citas','CitaController',['only' => ['store', 'update', 'show','destroy']]);

    // listar reservas de un establecimiento de salud
     Route::put('citas_confirmacion/{cit_id}','CitaController@confirmar_cita');

    // listar las citas de un establecimiento de salud dado uno fecha
    Route::get('citas_establecimientos/{es_id}','CitaController@listar_citas_fechas');

    // listar reservas de un paciente

    Route::get('reservas_pacientes/{pac_id}','CitaController@listar_citas_paciente');

    // listar reservas de un medico

    Route::get('reservas_medicos/{fe_id}','CitaController@listar_citas_medico');

    // listar reservas de un dia determinado

    Route::get('reservas_dias/{es_id}','CitaController@listar_citas_dia');

    // lista las citas programadas de un establecimeinto de salud

    Route::get('citas_medicos/{fe_id}','CitaController@listar_citas_medico_fecha');

    // permite realizar operacion con zonas listar zonas listar zonas por municipio

    Route::resource('zonas','ZonaController',['only' => ['index','show']]);

    // permite listar los establecimeintos de salud que pertenecen a un zona

    Route::get('establecimientos_por_zona/{zon_id}','ZonaController@establecimientos_por_zona');

    // permite el listado de los establecimientos habilitados para realizar reserva
    Route::get('establecimientos_reservas/{pac_id}','Paciente_establecimientoController@listar_establecimientos_habilitados');
    // permite realizar operaciones con medicos 

    Route::resource('medicos','MedicoController',['only' => ['index','show']]);

    // permite realizar operaciones con enfermedad

    Route::resource('enfermeras','EnfermeraController',['only' => ['index','show']]);
// permite realizar operaciones con detalle_vacuna
    Route::resource('detalle_vacunas','Detalle_vacunaController',['only' => ['index','show','store','update','destroy']]);

    Route::resource('atendidos','ReporteController',['only' => ['index','show','store','update']]);

    Route::resource('crecimientos','Crecimiento_signos_vitalesController',['only' => ['show','store','update']]);

}
    );


