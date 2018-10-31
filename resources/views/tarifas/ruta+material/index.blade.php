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
    <div class="errores"></div>
    <div class="table-responsive">
        <table class="table table-hover table-bordered small">
            <thead>
            <tr>
                <th>Tarifa</th>
                <th>Ruta</th>
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
                <th>Motivo de Desactivación</th>
                <th>Canceló</th>
                <th>Motivo de Cancelación</th>
                <th>Acciones</th>
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
                        <td>{{ $tarifa->ruta->descripcion }}{!! Form::hidden('IdRuta', $tarifa->ruta->IdRuta) !!}</td>
                        <td>{{ $tarifa->material->Descripcion }}{!! Form::hidden('IdMaterial', $tarifa->material->IdMaterial) !!}</td>
                        <td>{{ $tarifa->primer_km }}</td>
                        <td>{{ $tarifa->km_subsecuentes }}</td>
                        <td>{{ $tarifa->km_adicionales}}</td>
                        <td>{{ $tarifa->inicio_vigencia->format("d-m-Y h:i:s") }}</td>
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
                        <td>{{ $tarifa->fecha_hora_registro->format("d-m-Y h:i:s") }}</td>
                        <td>{{ $tarifa->estatus_string }}</td>
                        <td>{{ $tarifa->user_desactivo }}</td>
                        <td>{{ $tarifa->motivo_desactivar }}</td>
                        <td>{{ $tarifa->user_cancelo }}</td>
                        <td>{{ $tarifa->motivo_cancelar }}</td>
                        <td>
                            @permission('desactivar-tarifa-ruta-material')
                                @if($tarifa->estatus == 1)
                                    <button title="Desactivar" class="btn btn-xs btn-danger" onclick="desactivar_tarifa({{$tarifa->id}})"><i class="fa fa-remove"></i></button>
                                @endif
                            @endpermission
                            @permission('cancelar-tarifa-ruta-material')
                            @if($tarifa->estatus == 1 || $tarifa->estatus == 0)
                                <button title="Cancelar" class="btn btn-xs btn-danger" onclick="cancelar_tarifa({{$tarifa->id}})"><i class="fa fa-remove"></i></button>
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
@stop
@section('scripts')
    <script>
        function desactivar_tarifa(id) {
            var form = $('#delete');
            var url=App.host +"/tarifas_ruta_material/"+id;

            swal({
                    title: "¡Desactivar tarifa!",
                    text: "¿Esta seguro de que deseas desactivar la tarifa?",
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
        },
        function cancelar_tarifa(id) {
            var form = $('#delete');
            var url=App.host +"/tarifas_ruta_material/"+id;

            swal({
                    title: "¡Cancelar la tarifa!",
                    text: "¿Esta seguro de que deseas cancelar la tarifa?",
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
