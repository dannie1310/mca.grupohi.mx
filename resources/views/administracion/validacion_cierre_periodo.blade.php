@extends('layout')

@section('content')
    <h1>ADMINISTRACIÓN DE PERMISOS PARA CAMBIOS EN LOS PERIODOS CERRADOS</h1>
    <div id="app">
        <global-errors></global-errors>
        <periodocierre-administracion inline-template>
            <section>
                <app-errors v-bind:form="form"></app-errors>

                    <div class="panel panel-default">

                        <div class="panel-body">

                            <div class="col-sm-12">
                                <div class="panel panel-default">

                                    <div class="row">
                                        <div class="panel-body">
                                            <form class="form-horizontal">
                                                <div class="form-group">
                                                    <label class="control-label col-sm-5" >Seleccione Usuario:</label>
                                                    <div class="col-sm-12">
                                                        <select class="input form-control" v-on:change="select_usuario" v-model="selected_usuario_id" id="usuario">
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
                                    <div class="row">
                                        <div  class="col-sm-5">
                                            <center><b>Habilitar Periodos Cerrados</b></center>
                                            <select id="leftValues" size="20" class="form-control"   multiple>
                                                <option v-for="cierre in cierres_periodo" id="cierre.idcierre" value="cierre.idcierre">@{{ cierre.mesNombre }} @{{ cierre.anio }}</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <h4><label style="cursor: pointer">Selecciona el periodo</label></h4>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>FECHA INICIAL</label>
                                            <input type="text" name="FechaInicial" class="form-control" v-datepicker>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>FECHA FINAL </label>
                                            <input type="text" name="FechaFinal" class="form-control" v-datepicker>
                                        </div>
                                    </div>
                                </div>

                        </div>
                    </div>
                </div>



            </section>
        </periodocierre-administracion>
    </div>

@stop
