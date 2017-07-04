@extends('layout')

@section('content')
    @include('partials.errors')
    <div id="app">
        <global-errors></global-errors>
        <tickets-validar
                inline-template>
            <section>
                <div id="app" class="container">
                        <div class="row col-sm-12">
                            <div class="form-group text-center">
                                <h4> VALIDACIÓN DE TICKETS DE ACARREOS</h4>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <label>C&oacute;digo de Barras: </label>
                                    <input class="form-control" type="password"  style="font-size: 20px" v-model="code"  v-on:keyup="escanear"/>

                                    <h2></h2>
                                </div>
                                <div class="form-group text-center text-uppercase text-info">
                                    <input type="button"  value="Limpiar" @click="limpiar">
                                </div>
                            </div>
                        </div>
                    <div class="row" v-if="items.barras!=''">
                        <div class="col-sm-12 ">
                            <div v-if="error !=''" style="color: #FF0000" class="form-group text-center text-uppercase text-info" >
                                <h1 style=""><strong >@{{ error }}</strong></h1>
                            </div>
                            <div v-else class="form-group text-center text-uppercase text-info" >
                                <h1><strong >@{{ items.barras }}</strong></h1>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-12 ">
                            <div class="form-group text-center text-uppercase text-info">
                                <h1><strong></strong></h1>

                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="form-group">
                                <label>Proyecto: </label>
                                <input class="form-control" readonly="true" :value="items.proyecto" />
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="form-group">
                                <label>Cami&oacute;n: </label>
                                <input class="form-control" v-if="items.camion=='No se encontro en la base de datos'" style="color: #FF0000" readonly="true" :value="items.camion" />
                                <input class="form-control" v-else readonly="true" :value="items.camion" />
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="form-group">
                                <label>Cubicaci&oacute;n: </label>
                                <input class="form-control" readonly="true" :value="items.cubicacion" />
                            </div>
                        </div>
                    </div>
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <label>Material: </label>
                                    <input class="form-control" v-if="items.material=='No se encontro en la base de datos'" style="color: #FF0000" readonly="true" :value="items.material" />
                                    <input class="form-control" v-else readonly="true" :value="items.material" />
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <label>Origen: </label>
                                    <input class="form-control" v-if="items.origen=='Sin Origen.' || items.origen=='nada' " style="color: #FF0000" readonly="true" :value="items.origen" />
                                    <input class="form-control" v-else readonly="true" :value="items.origen" />
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <label>Fecha de Salida: </label>
                                    <input class="form-control" readonly="true" :value="items.fechaSalida" />
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <label>Destino: </label>
                                    <input v-if="items.destino =='No se encontro en la base de datos'" style="color: #FF0000"  class="form-control" readonly="true" :value="items.destino" />
                                    <input v-else class="form-control" readonly="true" :value="items.destino" />
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <label>Fecha de Llegada: </label>
                                    <input class="form-control" readonly="true" :value="items.fechaLlegada" />
                                </div>
                            </div>
                        </div>
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="form-group">
                                <label>Checador Inicio: </label>
                                <input class="form-control" readonly="true" :value="items.ChInicio" />
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="form-group">
                                <label>Checador Cierre: </label>
                                <input class="form-control" readonly="true" :value="items.ChCierre" />
                            </div>
                        </div>
                    </div>

                </div>
            </section>
        </tickets-validar>

    </div>

@stop