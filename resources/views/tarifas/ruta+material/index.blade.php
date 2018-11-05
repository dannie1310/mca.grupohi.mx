@extends('layout')

@section('content')
    <div class='success'></div>
    <h1>{{ strtoupper(trans('strings.tarifas_ruta_material')) }}
        @permission('crear-tarifa-ruta-material')
            <a href="{{ route('tarifas_ruta_material.create') }}" class="btn btn-success pull-right"><i class="fa fa-plus"></i> Nueva Tarifa</a>
        @endpermission
        @permission('descargar-excel-tarifa-ruta-material')
            <a href="{{ route('csv.tarifas-ruta-material') }}" style="margin-right: 5px" class="btn btn-info pull-right"><i class="fa fa-file-excel-o"></i> Descargar</a>
        @endpermission
    </h1>
    {!! Breadcrumbs::render('tarifas_ruta_material.index') !!}
    <hr>
    <div class="table-responsive">
        <table class="table table-striped small"  id="index_tarifas_ruta_material" >
            <thead>
            <tr>
                <th width="80px">Tarifa</th>
                <th>Ruta</th>
                <th>Origen</th>
                <th>Destino</th>
                <th>Material</th>
                <th>Tarifa 1er. KM</th>
                <th>Tarifa KM Subsecuentes</th>
                <th>Tarifa KM Adicionales</th>
                <th>Inicio de Vigencia</th>
                <th>Fin de Vigencia</th>
                <th>Tipo Tarifa</th>
                <th>Registro</th>
                <th>Fecha Hora Registro</th>
                <th>Estado</th>
                <th>Desactivó</th>
                <th>Canceló</th>
                <th width="70px">Acciones</th>
            </tr>
            </thead>
            <tbody>
            @foreach($tarifas as $tarifa)
                @if($tarifa->FinVigenciaTarifa == 'VIGENTE')
                    <tr style="background-color: azure">
                @else
                    <tr>
                        @endif
                        <td>Tarifa {{ $tarifa->id }}</td>
                        <td>{{ $tarifa->ruta->Clave }}{{$tarifa->ruta->IdRuta}}{!! Form::hidden('IdRuta', $tarifa->ruta->IdRuta) !!}</td>
                        <td>{{ $tarifa->ruta->origen->Descripcion }}{!! Form::hidden('IdOrigen', $tarifa->ruta->origen->IdOrigen) !!}</td>
                        <td>{{ $tarifa->ruta->tiro->Descripcion }}{!! Form::hidden('IdTiro', $tarifa->ruta->tiro->IdTiro) !!}</td>
                        <td>{{ $tarifa->material->Descripcion }}{!! Form::hidden('IdMaterial', $tarifa->material->IdMaterial) !!}</td>
                        <td>{{ $tarifa->primer_km }}</td>
                        <td>{{ $tarifa->km_subsecuentes }}</td>
                        <td>{{ $tarifa->km_adicionales}}</td>
                        <td>{{ $tarifa->inicio_vigencia->format("Y/m/d h:i:s") }}</td>
                        <td>{{ $tarifa->fin_vigencia }}</td>
                        @if($tarifa->idtipo_tarifa != null)
                            @foreach($tipos as $tipo)
                                @if($tipo->id == $tarifa->idtipo_tarifa)
                                    <td>{{ $tipo->descripcion }}</td>
                                @endif
                            @endforeach
                        @else
                            <td>NO ASIGNADO</td>
                        @endif
                        <td>{{ $tarifa->registro->present()->NombreCompleto }}</td>
                        <td>{{ $tarifa->fecha_hora_registro->format("Y/m/d h:i:s") }}</td>
                        <td>{{ $tarifa->estatus_string }}</td>
                        <td>{{ $tarifa->user_desactivo }}</td>
                        <td>{{ $tarifa->user_cancelo }}</td>
                        <td>
                            @permission('desactivar-tarifa-ruta-material')
                                @if($tarifa->estatus == 1)
                                    <button title="Desactivar" class="btn btn-xs btn-danger" onclick="desactivar_tarifa({{$tarifa->id}})"><i class="fa fa-remove"></i></button>
                                @endif
                            @endpermission
                            @permission('cancelar-tarifa-ruta-material')
                            @if($tarifa->estatus == 1 || $tarifa->estatus == 0)
                                <button title="Cancelar" class="btn btn-xs btn-black" onclick="cancelar_tarifa({{$tarifa->id}})"><i class="glyphicon glyphicon-trash"></i></button>
                            @endif
                            @endpermission
                        </td>
                    </tr>
                    @endforeach
            </tbody>
        </table>
    </div>
    <form id='delete' method="post">
        <input type='hidden' name='motivo' value/>
        {{csrf_field()}}
        <input type="hidden" name="_method" value="delete"/>
    </form>
    <form id='update' method="post">
        <input type='hidden' name='motivo' value/>
        {{csrf_field()}}
        <input type="hidden" name="_method" value="update"/>
    </form>
@stop
@section('scripts')
    <script>
        var auth_config = {
            auto_filter: true,
            col_0: 'input',
            col_1: 'select',
            col_2: 'select',
            col_3: 'select',
            col_4: 'select',
            col_5: 'none',
            col_6: 'none',
            col_7: 'none',
            col_8: 'input',
            col_9: 'input',
            col_10: 'select',
            col_11: 'input',
            col_12: 'input',
            col_13: 'select',
            col_14: 'input',
            col_15: 'input',
            col_16: 'none',
            base_path: App.tablefilterBasePath,
            auto_filter: true,
            paging: false,
            rows_counter: true,
            rows_counter_text: 'Tarifas Ruta y Material: ',
            btn_reset: true,
            btn_reset_text: 'Limpiar',
            clear_filter_text: 'Limpiar',
            loader: true,
            page_text: 'Pagina',
            of_text: 'de',
            help_instructions: false,
            extensions: [{ name: 'sort' }]
        };
        var tf = new TableFilter('index_tarifas_ruta_material', auth_config);
        tf.init();
        function desactivar_tarifa(id) {
            var form = $('#delete');
            var url=App.host +"/tarifas_ruta_material/"+id;

            swal({
                    title: "¡Desactivar tarifa!",
                    text: "¿Esta seguro de que deseas desactivar la tarifa ruta por material?",
                    type: "input",
                    showCancelButton: true,
                    closeOnConfirm: false,
                    inputPlaceholder: "Motivo de la desactivación.",
                    confirmButtonText: "Si Desactivar",
                    cancelButtonText: "No Desactivar",
                    showLoaderOnConfirm: true

                },
                function(inputValue){
                    if (inputValue === false) return false;
                    if (inputValue === "") {
                        swal.showInputError("Escriba el motivo de la desactivación!");
                        return false
                    }
                    form.attr("action", url);
                    $("input[name=motivo]").val(inputValue);
                    form.submit();
                });
        };
        function cancelar_tarifa(id) {
            var form = $('#update');
            var url=App.host +"/tarifas_ruta_material/"+id;

            swal({
                    title: "¡Cancelar la tarifa!",
                    text: "¿Esta seguro de que deseas cancelar la tarifa ruta por material?",
                    type: "input",
                    showCancelButton: true,
                    closeOnConfirm: false,
                    inputPlaceholder: "Motivo de la cancelación.",
                    confirmButtonText: "Si Cancelar",
                    cancelButtonText: "No Cancelar",
                    showLoaderOnConfirm: true

                },
                function(inputValue){
                    if (inputValue === false) return false;
                    if (inputValue === "") {
                        swal.showInputError("Escriba el motivo de la cancelación!");
                        return false
                    }
                    form.attr("action", url);
                    $("input[name=motivo]").val(inputValue);
                    form.submit();
                });
        }
    </script>
@endsection
