@include('partials.errors')
@extends('layout')

@section('content')
    <div class='success'></div>
    <h1>TABLERO DE CONTROL</h1>
    <hr>

    <div class="errores"></div>
    <div class="table-responsive">
        <table class="table table-hover table-bordered small">
            <thead>
            <tr>
                <th>Condición de análisis</th>
                <th>Núm Total</th>
                <th>Señal</th>
                <th>Acciones</th>
            </tr>
            </thead>
            <tbody>
           {{-- @foreach($tarifas as $tarifa)
                @if($tarifa->FinVigenciaTarifa == 'VIGENTE')
                    <tr style="background-color: azure">
                @else
                    <tr>
                        @endif
                        <td>{{ $tarifa->material->Descripcion }}{!! Form::hidden('IdMaterial', $tarifa->material->IdMaterial) !!}</td>
                        <td>{{ $tarifa->PrimerKM }}</td>
                        <td>{{ $tarifa->KMSubsecuente }}</td>
                        <td>{{ $tarifa->KMAdicional }}</td>
                        <td>{{ $tarifa->InicioVigencia->format("d-m-Y h:i:s") }}</td>
                        <td>{{ $tarifa->FinVigenciaTarifa }}</td>
                        @if($tarifa->idtarifas_tipo != null)
                            @foreach($tipos as $tipo)
                                @if($tipo->idtarifas_tipo == $tarifa->idtarifas_tipo)
                                    <td>{{ $tipo->nombre }}</td>
                                @endif
                            @endforeach
                        @else
                            <td>NO ASIGNADO</td>
                        @endif
                        <td>{{ $tarifa->registro->present()->NombreCompleto }}</td>
                        <td>{{ $tarifa->Fecha_Hora_Registra->format("d-m-Y h:i:s") }}</td>
                        <td>{{ $tarifa->estatus_string }}</td>
                        <td>{{ $tarifa->user_desactivo }}</td>
                        <td>{{ $tarifa->motivo }}</td>
                        <td>
                            @permission('editar-tarifas-material')
                            <a href="{{ route('tarifas_material.edit', $tarifa) }}" title="Editar" class="btn btn-xs btn-info"><i class="fa fa-pencil"></i></a>
                            @endpermission
                            @permission('desactivar-tarifas-material')
                            @if($tarifa->Estatus == 1)
                                <button title="Desactivar" class="btn btn-xs btn-danger" onclick="desactivar_tarifa({{$tarifa->IdTarifa}})"><i class="fa fa-remove"></i></button>
                            @endif
                            @endpermission
                        </td>
                    </tr>
                    @endforeach--}}
            </tbody>
        </table>
    </div>
    <form id='delete' method="post">
        <input type='hidden' name='motivo' value/>
        {{csrf_field()}}
        <input type="hidden" name="_method" value="delete"/>
    </form>
@stop{{--
@section('scripts')
    <script>
        function desactivar_tarifa(id) {
            var form = $('#delete');
            var url=App.host +"/tarifas_material/"+id;

            swal({
                    title: "¡Desactivar tarifa!",
                    text: "¿Esta seguro de que deseas desactivar la tarifa?",
                    type: "input",
                    showCancelButton: true,
                    closeOnConfirm: false,
                    inputPlaceholder: "Motivo de la desactivación.",
                    confirmButtonText: "Si, Desactivar",
                    cancelButtonText: "No, Cancelar",
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
        }
    </script>
@endsection
@stop--}}