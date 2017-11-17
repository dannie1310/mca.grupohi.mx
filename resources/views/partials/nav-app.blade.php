@if($currentProyecto)
  <li class="dropdown">
    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
        {{ $currentProyecto->descripcion }} <span class="caret"></span>
    </a>
    <ul class="dropdown-menu" role="menu">
        <li>{!! link_to_route('proyectos', 'Cambiar De Proyecto') !!}</li>
    </ul>
  </li>
  @if ( Auth::user()->can(['consultar-historico'])
      || Auth::user()->can(['crear-centroscostos'])
      || Auth::user()->can(['crear-empresas'])
      || Auth::user()->can(['crear-etapas'])
      || Auth::user()->can(['crear-impresoras'])
      || Auth::user()->can(['crear-marcas'])
      || Auth::user()->can(['crear-materiales'])
      || Auth::user()->can(['crear-operadores'])
      || Auth::user()->can(['crear-origenes'])
      || Auth::user()->can(['crear-rutas'])
      || Auth::user()->can(['crear-sindicatos'])
      || Auth::user()->can(['crear-tarifas-material'])
      || Auth::user()->can(['crear-telefonos'])
      || Auth::user()->can(['crear-tiros'])
      || Auth::user()->can(['desactivar-camiones'])
      || Auth::user()->can(['desactivar-centroscostos'])
      || Auth::user()->can(['desactivar-empresas'])
      || Auth::user()->can(['desactivar-etapas'])
      || Auth::user()->can(['desactivar-impresoras'])
      || Auth::user()->can(['desactivar-marcas'])
      || Auth::user()->can(['desactivar-materiales'])
      || Auth::user()->can(['desactivar-operadores'])
      || Auth::user()->can(['desactivar-origenes'])
      || Auth::user()->can(['desactivar-rutas'])
      || Auth::user()->can(['desactivar-sindicatos'])
      || Auth::user()->can(['desactivar-tarifas-material'])
      || Auth::user()->can(['desactivar-telefonos'])
      || Auth::user()->can(['desactivar-tiros'])
      || Auth::user()->can(['editar-camiones'])
      || Auth::user()->can(['editar-centroscostos'])
      || Auth::user()->can(['editar-empresas'])
      || Auth::user()->can(['editar-etapas'])
      || Auth::user()->can(['editar-impresoras'])
      || Auth::user()->can(['editar-marcas'])
      || Auth::user()->can(['editar-materiales'])
      || Auth::user()->can(['editar-operadores'])
      || Auth::user()->can(['editar-origenes'])
      || Auth::user()->can(['editar-rutas'])
      || Auth::user()->can(['editar-sindicatos'])
      || Auth::user()->can(['editar-tarifas-material'])
      || Auth::user()->can(['editar-telefonos'])
      || Auth::user()->can(['editar-tiros'])
      || Auth::user()->can(['consultar-camiones'])
      || Auth::user()->can(['consultar-centroscostos'])
      || Auth::user()->can(['consultar-empresas'])
      || Auth::user()->can(['consultar-etapas'])
      || Auth::user()->can(['consultar-impresoras'])
      || Auth::user()->can(['consultar-marcas'])
      || Auth::user()->can(['consultar-materiales'])
      || Auth::user()->can(['consultar-operadores'])
      || Auth::user()->can(['consultar-origenes'])
      || Auth::user()->can(['consultar-rutas'])
      || Auth::user()->can(['consultar-sindicatos'])
      || Auth::user()->can(['consultar-tarifas-material'])
      || Auth::user()->can(['consultar-telefonos'])
      || Auth::user()->can(['consultar-tiros']))

  <li class="dropdown">
    <a tabindex="0" href="#" class="dropdown-toggle" data-toggle="dropdown" data-submenu>
        Catálogos <span class="caret"></span>
    </a>
    <ul class="dropdown-menu" role="menu">
        @if(Auth::user()->can(['consultar-historico']) ||  Auth::user()->can(['consultar-centroscostos']) || Auth::user()->can(['crear-centroscostos']) || Auth::user()->can(['desactivar-centroscostos']) || Auth::user()->can(['editar-centroscostos']))
            <li><a href="{{ route('centroscostos.index') }}">Centros De Costo</a></li>
        @endif
        @if(Auth::user()->can(['consultar-historico']) || Auth::user()->can(['consultar-camiones']) || Auth::user()->can(['desactivar-camiones']) || Auth::user()->can(['editar-camiones']))
            <li><a href="{{ route('camiones.index') }}">Camiones</a></li>
        @endif
        @if(Auth::user()->can(['consultar-historico']) ||  Auth::user()->can(['consultar-empresas'])|| Auth::user()->can(['crear-empresas']) || Auth::user()->can(['desactivar-empresas']) || Auth::user()->can(['editar-empresas']))
             <li><a href="{{ route('empresas.index') }}">Empresas</a></li>
        @endif
        @if(Auth::user()->can(['consultar-historico']) || Auth::user()->can(['consultar-etapas'])||  Auth::user()->can(['crear-etapas'])|| Auth::user()->can(['desactivar-etapas'])|| Auth::user()->can(['editar-etapas']))
            <li><a href="{{ route('etapas.index') }}">Etapas De Proyecto</a></li>
        @endif
        @if(Auth::user()->can(['consultar-historico']) || Auth::user()->can(['factores-abundamiento']))
            <li class="dropdown-submenu">
                <a tabindex="0" class="dropdown-toggle" data-toggle="dropdown">Factores De Abundamiento</a>
                <ul class="dropdown-menu">
                    <li><a tabindex="-1" href="{{ route('fda_material.index') }}">Por Material</a></li>
                    <li><a tabindex="-1" href="{{ route('fda_banco_material.index') }}">Por Banco Y Material</a></li>
                </ul>
            </li>
        @endif
        @if(Auth::user()->can(['consultar-historico']) || Auth::user()->can(['consultar-impresoras'])||  Auth::user()->can(['crear-impresoras']) || Auth::user()->can(['desactivar-impresoras']) || Auth::user()->can(['editar-impresoras']))
            <li><a href="{{ route('impresoras.index') }}">Impresoras</a></li>
        @endif
        @if(Auth::user()->can(['consultar-historico'])||  Auth::user()->can(['consultar-marcas'])||  Auth::user()->can(['crear-marcas'])|| Auth::user()->can(['desactivar-marcas'])|| Auth::user()->can(['editar-marcas']))
           <li><a href="{{ route('marcas.index') }}">Marcas</a></li>
        @endif
        @if(Auth::user()->can(['consultar-historico'])||  Auth::user()->can(['consultar-materiales'])||  Auth::user()->can(['crear-materiales'])|| Auth::user()->can(['desactivar-materiales'])|| Auth::user()->can(['editar-materiales']))
            <li><a href="{{ route('materiales.index') }}">Materiales</a></li>
        @endif
        @if(Auth::user()->can(['consultar-historico'])||  Auth::user()->can(['consultar-operadores'])||  Auth::user()->can(['crear-operadores'])|| Auth::user()->can(['desactivar-operadores'])|| Auth::user()->can(['editar-operadores']))
            <li><a href="{{ route('operadores.index') }}">Operadores</a></li>
        @endif
        @if(Auth::user()->can(['consultar-historico'])||  Auth::user()->can(['consultar-origenes'])||  Auth::user()->can(['crear-origenes'])|| Auth::user()->can(['desactivar-origenes'])|| Auth::user()->can(['editar-origenes']))
            <li><a href="{{ route('origenes.index') }}">Origenes</a></li>
        @endif
        @if(Auth::user()->can(['consultar-historico'])||  Auth::user()->can(['consultar-origenes_x_usuario'])||  Auth::user()->can(['crear-origenes_x_usuario'])|| Auth::user()->can(['desactivar-origenes_x_usuario'])|| Auth::user()->can(['editar-origenes_x_usuario']))
            <li><a href="{{ route('origenes_usuarios.index') }}">Origenes Por Usuario</a></li>
        @endif
        @if(Auth::user()->can(['consultar-historico'])||  Auth::user()->can(['consultar-rutas'])||  Auth::user()->can(['crear-rutas'])|| Auth::user()->can(['desactivar-rutas'])|| Auth::user()->can(['editar-rutas']))
            <li><a href="{{ route('rutas.index') }}">Rutas</a></li>
        @endif
        @if(Auth::user()->can(['consultar-historico'])||   Auth::user()->can(['consultar-sindicatos'])|| Auth::user()->can(['crear-sindicatos'])|| Auth::user()->can(['desactivar-sindicatos'])|| Auth::user()->can(['editar-sindicatos']))
            <li><a href="{{ route('sindicatos.index') }}">Sindicatos</a></li>
        @endif
        @if(Auth::user()->can(['consultar-historico'])||  Auth::user()->can(['consultar-telefonos'])||  Auth::user()->can(['crear-telefonos'])|| Auth::user()->can(['desactivar-telefonos'])|| Auth::user()->can(['editar-telefonos']))
            <li><a href="{{ route('telefonos.index') }}">Teléfonos</a></li>
        @endif
        @if(Auth::user()->can(['consultar-historico'])||   Auth::user()->can(['consultar-tiros'])|| Auth::user()->can(['crear-tiros'])|| Auth::user()->can(['desactivar-tiros'])|| Auth::user()->can(['editar-tiros']))
            <li><a href="{{ route('tiros.index') }}">Tiros</a></li>
        @endif
        @if(Auth::user()->can(['consultar-historico'])||  Auth::user()->can(['consultar-tarifas-materialgit '])||  Auth::user()->can(['crear-tarifas-material'])|| Auth::user()->can(['desactivar-tarifas-material'])|| Auth::user()->can(['editar-tarifas-material']))
            <li class="dropdown-submenu">
                <a tabindex="0" class="dropdown-toggle" data-toggle="dropdown">Tarifas</a>
                <ul class="dropdown-menu">
                    <li><a tabindex="-1" href="{{ route('tarifas_material.index') }}">Tarifas Por Material</a></li>
                    <li><a tabindex="-1" href="{{ route('tarifas_peso.index') }}">Tarifas Por Peso</a></li>
                    <li><a tabindex="-1" href="{{ route('tarifas_tiporuta.index') }}">Tarifas Por Tipo De Ruta</a></li>
                </ul>
            </li>
        @endif
    </ul>
  </li>
  @endif

  @if(Auth::user()->can(['consulta-solicitud-actualizar'])
  || Auth::user()->can(['consulta-solicitud-reactivar'])
  || Auth::user()->can(['ingresar-viajes-manuales'])
  || Auth::user()->can(['autorizar-viajes-manuales'])
  || Auth::user()->can(['ingresar-viajes-manuales-completos'])
  || Auth::user()->can(['consulta-viajes'])
  || Auth::user()->can(['configuracion-diaria'])
  || Auth::user()->can(['consultar-cortes-checador'])
  || Auth::user()->can(['consultar-viajes-conflicto'])
  || Auth::user()->can(['revertir-viajes'])
  || Auth::user()->can(['modificar-viajes'])
  || Auth::user()->can(['consultar-conciliacion'])
  || Auth::user()->can(['validar-viajes']))
  <li class="dropdown">
    <a tabindex="0" href="#" class="dropdown-toggle" data-toggle="dropdown" data-submenu>
        Operación<span class="caret"></span>
    </a>
    <ul class="dropdown-menu" role="menu">

        @if(Auth::user()->can(['consulta-viajes']))
        <li><a tabindex="-2" href="{{ route('viajes_netos.index') }}">Viajes</a></li>
        @endif
        @if(Auth::user()->can(['consulta-solicitud-actualizar']) || Auth::user()->can(['consulta-solicitud-reactivar']))
        <li class="dropdown-submenu">
            <a tabindex="0" class="dropdown-toggle" data-toggle="dropdown">Solicitudes para camiones</a>
            <ul class="dropdown-menu">
                @if(Auth::user()->can(['consulta-solicitud-actualizar']))
                    <li><a href="{{ route('solicitud-actualizacion.index') }}">Solicitud para actualizar</a> </li>
                @endif
                @if(Auth::user()->can(['consulta-solicitud-reactivar']))
                    <li><a href="{{ route('solicitud-reactivacion.index') }}">Solicitud para reactivar</a> </li>
                @endif
            </ul>
        </li>
      @endif
      @if(Auth::user()->can(['ingresar-viajes-manuales']) || Auth::user()->can(['autorizar-viajes-manuales']) || Auth::user()->can(['ingresar-viajes-manuales-completos']) )
        <li class="dropdown-submenu">
            <a tabindex="0" class="dropdown-toggle" data-toggle="dropdown">Registrar Viajes</a>
            <ul class="dropdown-menu">
                <li class="dropdown-submenu">
                    <a tabindex="-1" class="dropdown-toggle" data-toggle="dropdown">Carga Manual</a>
                    <ul class="dropdown-menu">
                        @if(Auth::user()->can(['ingresar-viajes-manuales']))
                        <li><a tabindex="-2" href="{{ route('viajes_netos.create', ['action' => 'manual']) }}">Ingresar Viajes</a></li>
                        @endif
                        @if(Auth::user()->can(['autorizar-viajes-manuales']))
                        <li><a tabindex="-2" href="{{ route('viajes_netos.edit', ['action' => 'autorizar']) }}">Autorizar Viajes</a></li>
                        @endif
                    </ul>
                </li>
                @if(Auth::user()->can(['ingresar-viajes-manuales-completos']))
                <li><a href="{{ route('viajes_netos.create', ['action' => 'completa']) }}">Carga Manual Completa</a></li>
                @endif
            </ul>
        </li>
        @endif
        @if(Auth::user()->can(['validar-viajes']))
        <li><a href="{{ route('viajes_netos.edit', ['action' => 'validar']) }}">Validar Viajes</a></li>
        @endif
        @if (Auth::user()->can(['consultar-conciliacion']))
            <li><a href="{{ route('conciliaciones.index') }}">Conciliaciones</a></li>
        @endif
        @if(Auth::user()->can(['modificar-viajes']))
        <li><a href="{{ route('viajes_netos.edit', ['action' => 'modificar']) }}">Modificar Viajes</a></li>
        @endif
        @if(Auth::user()->can(['revertir-viajes']))
        <li><a href="{{ route('viajes.edit', ['action' => 'revertir']) }}">Revertir Viajes</a> </li>
        @endif
        @if (Auth::user()->can(['consultar-viajes-conflicto']))
        <li><a href="{{ route('viajes_netos.index', ['action' => 'en_conflicto']) }}">Viajes en Conflicto</a> </li>
        @endif
        @if(Auth::user()->can(['consultar-cortes-checador']))
        <li><a href="{{ route('corte.index') }}">Corte de Checador</a> </li>
        @endif
        @if(Auth::user()->can(['configuracion-diaria']))
        <li class="dropdown-submenu">
            <a tabindex="0" class="dropdown-toggle" data-toggle="dropdown">Configuración Diaria</a>
            <ul class="dropdown-menu">
                @if(Auth::user()->can(['configuracion-diaria']))
                    <li><a href="{{ route('configuracion-diaria.index') }}">Checadores</a> </li>
                    <li><a href="{{ route('telefonos-impresoras.index') }}">Teléfonos-Impresoras</a> </li>
                @endif
            </ul>
        </li>
        @endif
        @if(Auth::user()->can(['validar-tickets']))
            <li><a href="{{ route('tickets.index') }}">Validar Tickets</a> </li>
        @endif
    </ul>
  </li>
@endif
  @if(Auth::user()->can(['visualizar-reporte-inicio-viajes']) || Auth::user()->can(['visualizar-reporte-viajes-diarios']) || Auth::user()->can(['visualizar-reporte-viajes-netos']) || Auth::user()->can(['visualizar-reporte-viajes-netos-auditoria']) || Auth::user()->can(['visualizar-reporte-conciliacion']))
      <li class="dropdown">
          <a tabindex="0" href="#" class="dropdown-toggle" data-toggle="dropdown" data-submenu>
              Reportes<span class="caret"></span>
          </a>
          <ul class="dropdown-menu" role="menu">
              @if(Auth::user()->can(['visualizar-reporte-inicio-viajes']))
                  <li><a href="{{ route('reportes.inicio_viajes.create') }}">Inicio Viajes</a></li>
              @endif
              @if(Auth::user()->can(['visualizar-reporte-viajes-diarios']))
                  <li><a href="{{ route('reportes.viajes_netos.diario.create') }}">Viajes Netos Diario</a></li>
              @endif
              @if(Auth::user()->can(['visualizar-reporte-viajes-netos']))
                  <li><a href="{{ route('reportes.viajes_netos.completo.create') }}">Viajes Netos Completos</a></li>
              @endif
              @if(Auth::user()->can(['visualizar-reporte-viajes-netos-auditoria']))
                  <li><a href="{{ route('reportes.viajes_netos.auditoria.create') }}">Viajes Netos Completos (Auditoría)</a></li>
              @endif
              @if(Auth::user()->can(['visualizar-reporte-conciliacion']))
                  <li><a href="{{ route('reportes.conciliacion_detalle.create') }}">Conciliaciones Detallado</a></li>
              @endif
          </ul>
      </li>
  @endif
  @if(Auth::user()->hasRole(['administrador-permisos','auditoria','administrador-sistema'])|| Auth::user()->can(['auditoria-resumen-configuracion','permisos_cierre_x_periodo']))

      <li class="dropdown">
          <a tabindex="0" href="#" class="dropdown-toggle" data-toggle="dropdown" data-submenu>
              Administración<span class="caret"></span>
          </a>
          <ul class="dropdown-menu" role="menu">
              @if(Auth::user()->hasRole(['administrador-permisos','administrador-sistema','auditoria'])|| Auth::user()->can('auditoria-resumen-configuracion'))
                  <li><a href="{{ route('administracion.roles_permisos') }}">Configuración general</a></li>
              @endif
              @if(Auth::user()->hasRole(['administrador-sistema'])||Auth::user()->can('permisos_cierre_x_periodo'))
                  <li><a href="{{ route('validar-cierre-periodo.configuracion') }}">Validación de Periodo Cerrado por Usuario</a></li>
              @endif
              <li><a href="{{ route('detalle.configuracion') }}">Detalle configuración</a></li>
          <!-- <li><a href="{{ route('usuarios_sistema.index') }}">Alta de usuarios</a></li> -->
              @if(Auth::user()->hasRole(['administrador-permisos','administrador-sistema'])||Auth::user()->can('consulta-asignacion-proyecto'))
                  <li><a href="{{ route('usuario_proyecto.index') }}">Asignación de Usuarios a Proyectos</a></li>
              @endif
          </ul>
      </li>
  @endif
  @if(Auth::user()->can(['control_suministro']))
      <li class="dropdown">
          <a tabindex="0" href="#" class="dropdown-toggle" data-toggle="dropdown" data-submenu>
              Operación Suministro<span class="caret"></span>
          </a>
          <ul class="dropdown-menu" role="menu">
              <li><a href="{{ route('suministro_netos.index') }}">Viajes Suministro</a></li>
              <li class="dropdown-submenu">
                  <a tabindex="0" class="dropdown-toggle" data-toggle="dropdown">Registrar Suministro</a>
                  <ul class="dropdown-menu">
                      <li class="dropdown-submenu">
                          <a tabindex="-1" class="dropdown-toggle" data-toggle="dropdown">Carga Manual</a>
                          <ul class="dropdown-menu">
                                  <li><a tabindex="-2" href="{{ route('suministro_netos.create', ['action' => 'manual']) }}">Ingresar Viajes</a></li>
                                  <li><a tabindex="-2" href="{{ route('suministro_netos.edit', ['action' => 'autorizar']) }}">Autorizar Viajes</a></li>
                          </ul>
                      </li>
                  </ul>
              </li>
              <li><a href="{{ route('suministro_netos.edit', ['action' => 'validar']) }}">Validar Viajes</a></li>
              <li><a href="{{ route('suministro_netos.edit', ['action' => 'modificar']) }}">Modificar Viajes</a></li>
              <li><a href="{{ route('suministro_netos.edit', ['action' => 'revertir']) }}">Revertir Viajes</a> </li>
              <li><a href="{{ route('suministro_netos.index', ['action' => 'en_conflicto']) }}">Viajes en Conflicto</a> </li>
              <li><a href="{{ route('conciliaciones.suministro.index') }}">Conciliaciones</a></li>
          </ul>
      </li>
  @endif
@else
  <li><a href="{{ route('proyectos') }}">Proyectos</a></li>
@endif
