'use strict';
angular.module("adminApp")

//Lista todos los servicios de salud
.controller('ListaServiciosEsCtrl', ['$scope', 'Servicios', 'toastr', function ($scope, Servicios, toastr)
{
  $scope.ajustes = {
    menu:{
      titulo: 'Gestión de Servicios',
      items:[
        {nombre:'Servicios de Salud', enlace:'#/servicios', estilo:'active'}]
    },
    pagina:{
      titulo:'Servicios de Salud'
    }
  }
  
  $scope.sortType = 'ser_id'; // set the default sort type
  $scope.sortReverse  = false;  // set the default sort order
  $scope.loading=true;//PARA HACER UN LOADING EN EL TEMPLATE

  Servicios.get(function(data)
  {
    $scope.servicios = data.servicio;
    if(data.msg){
      $scope.loading = false;
      $scope.msg = data.msg;
    }
  },function () {
      toastr.error("ERROR INESPERADO, POR FAVOR ACTUALICE LA PÁGINA");
      $scope.loading = false;
      $scope.msg = false;
    });
}])

//Para LISTAR y AGREGAR los servicios de salud con los que no cuenta un establecimiento de salud
.controller('AgregaServiciosCtrl', ['$auth', '$scope', 'ServiciosNoEstablecimiento', '$route', 'toastr', 'ServiciosEstablecimiento', '$timeout',
 function ($auth,$scope,ServiciosNoEstablecimiento,$route,toastr,ServiciosEstablecimiento,$timeout) {
  $scope.ajustes = {
    menu:{
      titulo: 'Gestión de Servicios',
      items:[
        {nombre:'Servicios de salud', enlace:'#/presta', estilo:''},
        {nombre:'Agregar servicios', enlace:'#/servicios/agregar', estilo:'active'}]
    },
    pagina:{
      titulo:'Agregar servicios'
    }
  }
  
  $scope.loading=true;
  
  var FunG = localStorage.getItem("Funcionario");
  var FunG = JSON.parse(FunG);
  var es_id = FunG.es_id;
  
  ServiciosNoEstablecimiento.get({es_id:es_id}, function(data)
  {
    $scope.noprestas = data.nopresta;
    if(data.status && $scope.noprestas.length>0){
      $scope.loading = false;
      $scope.msg = data.status;
    }
    $scope.loading = false;
  });

  var id = 0;
  $scope.nombre_servicio = "";
  $scope.ger_ser_id = function(ser_id, ser_nombre){
    id = ser_id;
    $scope.nombre_servicio = ser_nombre;
  }

  $scope.agregar=function(ser_id)
  {
    $scope.presta={
      se_necesita_ref:"true",
      es_id:es_id,
      ser_id:id,
      se_costo:null
    };

    ServiciosEstablecimiento.save({token:$auth.getToken()},$scope.presta).$promise.then(function(data)
    {   
      if(data.mensaje)
      {
        $timeout(function() {
          toastr.success('Servicio añadido correctamente');
          $route.reload();
        },1000);
      }
    })
  } 
}])

//Muestra los servicios que presta un establecimiento de salud, además permite editarlos y eliminarlos
.controller('EstablecimientoPrestaCtrl', ['$auth', 'CONFIG', 'authUser', '$scope','EstablecimientoPresta','ServiciosEstablecimiento','$routeParams','$route', 'toastr', '$timeout',
  function ($auth,CONFIG,authUser,$scope,EstablecimientoPresta,ServiciosEstablecimiento,$routeParams,$route,toastr,$timeout){
  $scope.ajustes = {
    menu:{
      titulo: 'Gestión de Servicios',
      items:[
        {nombre:'Servicios de Salud', enlace:'#/presta', estilo:'active'},
        {nombre:'Agregar servicios', enlace:'#/servicios/agregar', estilo:''}]
    },
    pagina:{
      titulo:'Servicios de Salud del Establecimiento'
    }
  }

  $scope.loading=true;
  $scope.sortType = 'se_id'; // set the default sort type
  $scope.sortReverse  = true;  // set the default sort order
  var es_id=0;
  if (authUser.isLoggedIn()) {
    if(CONFIG.ROL_CURRENT_USER == 1){
      var es_id = $routeParams.es_id;
    }
    else{
      var FunG = localStorage.getItem("Funcionario");
      var FunG = JSON.parse(FunG);
      var es_id = FunG.es_id;
    }
  }
  else {
    var es_id = $routeParams.es_id;
  }
    
  EstablecimientoPresta.get({es_id:es_id}, function(data)
  {
    $scope.servicio_especialidad = data.servicio_especialidad;
    if(data.status && $scope.servicio_especialidad.length>0){
      $scope.loading = false;
      $scope.msg = data.status;
      for(var i = 0;i<$scope.servicio_especialidad.length; i++) {
        if ($scope.servicio_especialidad[i].se_necesita_ref)
        {
          $scope.servicio_especialidad[i].se_necesita_ref = false;
        } else {
          $scope.servicio_especialidad[i].se_necesita_ref = true;
        }

      }
    }

    $scope.loading = false;
  },function () {
      toastr.error("ERROR INESPERADO, POR FAVOR ACTUALICE LA PÁGINA");
      $scope.loading = false;
      $scope.msg = false;
  });

  var id = 0;
  var necesita_ref = false;
  $scope.nombre_servicio = "";

  $scope.get_se_id = function(se_id,pre_necesita_ref,pre_ser_nombre){
      id=se_id;
      necesita_ref=pre_necesita_ref;
      $scope.nombre_servicio = pre_ser_nombre;
  }

  $scope.guardarpre=function(se_id,pre_necesita_refy)
  {
    ServiciosEstablecimiento.get({se_id:id}, function(data)
    {
      $scope.pre = data.servicio_establecimiento;
      $scope.prest={
        se_costo:$scope.pre[0].se_costo,
        ser_id:$scope.pre[0].ser_id,
        se_necesita_ref:pre_necesita_refy
      }
      
      ServiciosEstablecimiento.update({token:$auth.getToken(),se_id:id},$scope.prest).$promise.then(function(data)
      {
        if(data.mensaje)
        {
          $timeout(function() {
            toastr.success('SERVICIO ACTUALIZADO CORRECTAMENTE');
            $route.reload();
          },1000);
        }
      });
    });  
  }

  $scope.remove=function(se_id)
  {
    ServiciosEstablecimiento.delete({token:$auth.getToken(),se_id:id}).$promise.then(function(data)
    {
      if(data.mensaje)
      {
        $timeout(function() {
          toastr.success('Eliminado correctamente');
          $route.reload();
        },1000);
      }
    })
  }
}])

.controller('CheckController', ['$scope',function($scope){
  $scope.checkbox= {
    value : true
  };
}])