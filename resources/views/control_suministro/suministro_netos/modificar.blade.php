<h1>VIAJES</h1>
{!! Breadcrumbs::render('suministro_netos.modificar') !!}
<hr>
<div id="app">
    <global-errors></global-errors>
    <suministro-modificar inline-template v-cloak>
        <section>
            <app-errors v-bind:form="form"></app-errors>
            <h3>BUSCAR VIAJES</h3>
            {!! Form::open(['class' => 'form_buscar']) !!}
            <h4><label style="cursor: pointer"><input type="radio" name="tipo_busqueda" value="fecha" checked="checked">BUSCAR POR FECHA</label></h4>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label>FECHA INICIAL (*)</label>
                        <input type="text" name="FechaInicial" v-datepicker class="fecha form-control">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label>FECHA FINAL (*)</label>
                        <input type="text" name="FechaFinal" v-datepicker class="fecha form-control">
                    </div>
                </div>
            </div>
            <h4><label style="cursor: pointer"><input type="radio" name="tipo_busqueda" value="codigo" > BUSCAR POR CÓDIGO</label></h4>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Ticket (Código) (*)</label>
                        <input type="text" name="Codigo" class="form-control">
                    </div>
                </div>
            </div>
            <div class="form-group">
                <button class="btn btn-primary" type="submit" @click="buscar">
                <span v-if="cargando"><i class="fa fa-spinner fa-spin"></i></span>
                <span v-else>Buscar</span>
                </button>
            </div>
            <p class="small">Los campos <strong>(*)</strong> son obligatorios.</p>
            {!! Form::close() !!}

            <hr>
            <div class="table-responsive">
                <span v-if="cargando">
                    <div class="text-center">
                        <i class="fa fa-2x fa-spinner fa-spin"></i> Cargando Viajes...
                    </div>
                </span>
                <span v-if="viajes_netos.length">
                    <h3>RESULTADOS DE LA BÚSQUEDA</h3>
                    <table id="viajes_netos_modificar" v-tablefilter class="table table-condensed table-bordered table-hover small">
                        <thead>
                            <tr>
                                <th rowspan="2">#</th>
                                <th rowspan="2">Fecha</th>
                                <th rowspan="2">Origen</th>
                                <th rowspan="2">Camión</th>
                                <th rowspan="2">Cubic.</th>
                                <th rowspan="2">Material</th>
                                <th rowspan="2">Código</th>
                                <th rowspan="2">Folio de Mina</th>
                                <th rowspan="2">Folio de Seguimiento</th>
                                <th rowspan="2">Volumen</th>
                                <th rowspan="2">Modificar</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="(viaje, index) in viajes_netos">
                                <td>@{{ index + 1 }}</td>
                                <td>@{{ viaje.FechaLlegada }}</td>
                                <td>@{{ viaje.Origen }}</td>
                                <td>@{{ viaje.Camion }}</td>
                                <td>@{{ viaje.Cubicacion }}</td>
                                <td>@{{ viaje.Material }}</td>
                                <td>@{{ viaje.Code }}</td>
                                 <td>@{{ viaje.FolioMina }}</td>
                                 <td>@{{ viaje.FolioSeguimiento }}</td>
                                 <td>@{{ viaje.Volumen }}</td>

                                <td  v-if="viaje.cierre == 0">
                                    <a id="show-modal" @click="showModal(viaje)">
                                        Modificar
                                    </a>
                                    <modal-modificar v-if="viaje.ShowModal" @close="viaje.ShowModal = false">
                                        <h3 slot="header">Modificar Viaje Neto</h3>
                                        <div slot="body">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label>ORIGEN</label>
                                                        <select v-model="form.data.IdOrigen" class="form-control input-sm">
                                                            <option value>--SELECCIONE--</option>
                                                            @foreach($origenes as $key => $origen)
                                                                <option value="{{ $key }}">{{ $origen }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label>CUBICACIÓN</label>
                                                        <input type="text" v-model="form.data.Cubicacion"  class="form-control input-sm"  >
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-md-6">
                                                    <label>MATERIAL</label>
                                                    <select v-model="form.data.IdMaterial" class="form-control input-sm">
                                                        <option value>--SELECCIONE--</option>
                                                        @foreach($materiales as $key => $material)
                                                            <option value="{{ $key }}">{{ $material }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label>Folio de Mina</label>
                                                        <input type="text" v-model="form.data.FolioMina"  class="form-control input-sm"  >
                                                    </div>
                                                </div>
                                            </div>
                                             <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label>Folio de Seguimiento</label>
                                                        <input type="text" v-model="form.data.FolioSeguimiento"  class="form-control input-sm"  >
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label>Volumen</label>
                                                        <input type="text" v-model="form.data.Volumen"  class="form-control input-sm"  >
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group" slot="footer">
                                            <button class="btn btn-info btn-sm" @click="viaje.ShowModal = false">Cerrar</button>
                                            <button class="btn btn-success btn-sm" @click="modificar(viaje)">
                                                <span v-if="guardando"><i class="fa fa-spinner fa-spin"></i></span>
                                                <span v-else>Modificar</span>
                                            </button>
                                        </div>
                                    </modal-modificar>
                                </td>
                                <td v-else>Periodo Cerrado</td>
                            </tr>
                        </tbody>
                    </table>
                </span>
            </div>
        </section>
    </suministro-modificar>
</div>