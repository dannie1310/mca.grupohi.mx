@extends('layout')

@section('content')
    <h1>CONFIGURACIÓN DIARIA
        <a href="{{ route('csv.configuracion-checadores') }}" class="btn btn-info pull-right"><i class="fa fa-file-excel-o"></i> Descargar </a>
    </h1>
    {!! Breadcrumbs::render('configuracion-diaria.index') !!}
    <hr>

    <div id="app">
        <global-errors></global-errors>
        <configuracion-diaria inline-template rol_checador="{{ $rol->id }}">
            <section>
                <div v-if="cargando" class="row">
                    <h4 style="text-align: center"><i class="fa fa-spinner fa-spin fa-lg"></i> CARGANDO </h4>
                </div>
                <div v-else class="row">
                    <!-- Configuración de Checadores-->
                    <div v-if="checadores.length" class="col-md-12" >
                        <h3>CONFIGURACIÓN DE CHECADORES</h3>
                        <div class="table-responsive">
                            <table class="table small">
                                <thead>
                                <tr>
                                    <th>#</th>
                                    <th style="text-align: center">Nombre</th>
                                    <th style="text-align: center">Usuario Intranet</th>
                                    <th style="text-align: center">Origen / Tiro</th>
                                    <th style="text-align: center">Ubicación </th>
                                    <th style="text-align: center">Perfil</th>
                                    <th style="text-align: center">Turno</th>
                                    <th style="text-align: center">Guardar</th>
                                    <th style="text-align: center">Limpiar Configuración</th>
                                    <th style="text-align: center">Eliminar Checador</th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr v-for="(user, index) in checadores">
                                    <td>@{{ index + 1 }}</td>
                                    <td>@{{ user.nombre  }}</td>
                                    <td>@{{ user.usuario }}</td>
                                    <td>
                                        <select v-on:change="clear_ubicacion(user)" name="tipo" class="form-control input-sm" v-model="user.configuracion.tipo">
                                            <option value>-- SELECCIONE --</option>
                                            <option value="0">Origen</option>
                                            <option value="1">Tiro</option>
                                        </select>
                                    </td>
                                    <td>
                                        <select v-if="user.configuracion.tipo == 0" v-on:change="set_ubicacion(user, $event)" name="ubicacion" class="form-control input-sm" v-bind:disabled="user.configuracion.tipo == ''">
                                            <option v-bind:selected="user.configuracion.ubicacion.id == ''" value>-- SELECCIONE --</option>
                                            <option v-bind:selected="user.configuracion.ubicacion.id == origen.id"  v-for="origen in origenes" v-bind:value="origen.id">@{{ origen.descripcion }}</option>
                                        </select>
                                        <select v-else-if="user.configuracion.tipo == 1" v-on:change="set_ubicacion(user, $event)" name="ubicacion" class="form-control input-sm" v-bind:disabled="user.configuracion.tipo == ''">
                                            <option v-bind:selected="user.configuracion.ubicacion.id == ''" value>-- SELECCIONE --</option>
                                            <option v-bind:selected="user.configuracion.ubicacion.id == tiro.id"  v-for="tiro in tiros" v-bind:value="tiro.id">@{{ tiro.descripcion }}</option>
                                        </select>
                                    </td>
                                    <td v-if="user.configuracion.ubicacion.id != ''">
                                         <select v-if="user.configuracion.tipo == 0" name="perfil" class="form-control input-sm" v-model="user.configuracion.id_perfil" >
                                             <option value>-- SELECCIONE --</option>
                                            <option  v-for="perfil in para_origen" v-bind:value="perfil.id">@{{ perfil.name }}</option>
                                        </select> 
                                        <select v-if="user.configuracion.tipo == 1" name="perfil" class="form-control input-sm" v-model="user.configuracion.id_perfil" >
                                             <option value>-- SELECCIONE --</option>
                                            <option  v-for="perfil in para_tiro" v-bind:value="perfil.id">@{{ perfil.name }}</option>
                                        </select>
                                    </td>
                                    <td v-else>
                                       <select class="form-control" disabled="disabled">
                                           <option value>-- SELECCIONE --</option>
                                       </select>
                                    </td>
                                    <td>
                                        <select name="turno" class="form-control input-sm" v-model="user.configuracion.turno" :disabled="!user.configuracion.id_perfil">
                                            <option value>-- SELECCIONE --</option>
                                            <option value="M">Matutino</option>
                                            <option value="V">Vespertino</option>
                                        </select>
                                    </td>
                                    <td style="text-align: center">
                                        <button @click="guardar_configuracion(user)" type="submit" class="btn btn-xs btn-success" :disabled="user.guardando">
                                            <i v-if="user.guardando" class="fa fa-spinner fa-spin fa-lg"></i>
                                            <i v-else class="fa fa-save fa-lg"></i>
                                        </button>
                                    </td>
                                    <td style="text-align: center">
                                        <button @click="quitar_configuracion(user)" type="button" class="btn btn-xs btn-warning" :disabled=" ! user.configuracion.id || user.guardando">
                                            <i v-if="user.guardando" class="fa fa-spinner fa-spin fa-lg"></i>
                                            <i v-else class="fa fa-undo fa-lg"></i>
                                        </button>
                                    </td>
                                    <td style="text-align: center">
                                        <button @click="confirmar_quitar_checador(user)" type="button" class="btn btn-xs btn-danger" :disabled="user.guardando">
                                            <i v-if="user.guardando" class="fa fa-spinner fa-spin fa-lg"></i>
                                            <i v-else class="fa fa-remove fa-lg"></i>
                                        </button>
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </section>
        </configuracion-diaria>
    </div>
@endsection