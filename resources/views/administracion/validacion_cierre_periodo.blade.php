@extends('layout')

@section('content')
    <h1>REGISTRO DE PERMISOS PARA CAMBIOS EN LOS PERIODOS CERRADOS</h1>
    <div id="app">
        <global-errors></global-errors>
        <periodocierre-administracion inline-template>
            <section>
                <app-errors v-bind:form="form"></app-errors>

                    <div class="panel panel-default">
                        <form id="cierre_form" class="form-horizontal" action="{{ route('cierre.save') }}" method="POST">
                        <div class="panel-body">

                            <div class="col-sm-12">
                                <div class="panel panel-default">

                                    <div class="row">
                                        <div class="panel-body">
                                            <form class="form-horizontal">
                                                <div class="form-group">
                                                    <label class="control-label col-sm-5" >Seleccione Usuario:</label>
                                                    <div class="col-sm-12">
                                                        <select class="input form-control" id="selUser"  v-on:change="select_usuario" v-model="selected_usuario_id" id="usuario">
                                                            <option value="">[--Seleccione--]</option>
                                                            <option v-for="usuario in usuarios" v-bind:value="usuario.id">@{{ usuario.nombre }}</option>
                                                        </select>

                                                    </div>
                                                </div>
                                            </form>

                                        </div>

                                    </div>
                                </div>
                                <div class="panel-body">
                                    <div class="col-sm-6">
                                        <div class="row">
                                            <div  class="col-sm-10">
                                                <center><b>Habilitar Periodos Cerrados</b></center>
                                                <select id="seleccionValues" size="20" class="form-control" v-model="select" multiple>
                                                    <option v-for="cierre in cierres_periodo"  v-bind:value="cierre.idcierre">@{{ cierre.mesNombre }} @{{ cierre.anio }}</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="row">
                                            <h4><label style="cursor: pointer">Selecciona la Fecha Inicial</label></h4>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label>FECHA</label>
                                                    <input type="text" id="FechaInicial"  name="FechaInicial" class="form-control" v-datepicker>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label>HORA </label>
                                                    <input type="text" id="HoraInicial" name="HoraInicial" format="HH:mm a" class="form-control"  v-timepicker>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="row">
                                            <h4><label style="cursor: pointer">Selecciona la Fecha Final</label></h4>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label>FECHA FINAL</label>
                                                    <input type="text" id="FechaFinal"  name="FechaFinal" class="form-control" v-datepicker>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label>HORA FINAL </label>
                                                    <input type="text" id="HoraFinal" name="HoraFinal" class="form-control" v-timepicker>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-success"  id="btnCierre" :disabled="guardando"   v-on:click="add_permiso_click">Agregar </button>
                                        </div>
                                    </div>
                                </div>
                                </div>
                            </div>
                            <div  class="col-md-12">
                                <center><h4><b>Historico</b></h4></center>

                                <table class="table table-bordered">
                                   <thead>
                                        <tr>
                                            <th>Nombre del Usuario</th>
                                            <th>Fecha Inicio</th>
                                            <th>Fecha Final</th>
                                            <th>Mes y año permitido</th>
                                        </tr>
                                   </thead>
                                    <tbody>
                                    <tr v-for="historico in historico">
                                        <th>@{{ historico.nombre }}</th>
                                        <th>@{{ historico.fecha_inicio }}</th>
                                        <th>@{{ historico.fecha_fin }}</th>
                                        <th>@{{ historico.mes }} @{{ historico.anio }}</th>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                        </form>

                    </div>

            </section>
        </periodocierre-administracion>
    </div>

@stop
