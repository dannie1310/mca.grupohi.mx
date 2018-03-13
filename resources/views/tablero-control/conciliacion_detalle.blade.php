@extends('layout')

@section('content')
    @if($tipo == 7)
        <h1>Cancelación de conciliaciones sin perfil de Gerente</h1>
    @elseif($tipo == 9)
        <h1>Conciliaciones: Creación, revisión y autorización con el mismo usuario</h1>
    @endif


    <h5>Fecha:{{$fecha_f}}</h5>
    <hr>

    <div class="table-responsive">
        <table class="table table-hover table-bordered small">
            <thead>
            <tr>
                <th>Conciliación</th>
                @if($tipo != 9)
                    <th>Motivo</th>
                    <th>Fecha</th>
                    <th>Usuario Canceló</th>
                @else
                    <th>Estado</th>
                    <th>Fecha</th>
                    <th>Usuario (Creó, Revisó y Autorizó)</th>
                @endif

            </tr>
            </thead>
            <tbody>
            @foreach($conciliacion  as  $tipo)
                <tr>
                    <td>{{$tipo["conciliacion"]}}</td>
                    @if($tipo["motivo"]!="")
                        <td>{{$tipo["motivo"]}}</td>
                    @else
                        @if($tipo["estado"] == 0)
                            <td>Generada</td>
                        @elseif($tipo["estado"] == 1)
                            <td>Cerrada</td>
                        @elseif($tipo["estado"] == 2)
                            <td>Aprobada</td>
                        @elseif($tipo["estado"] == -1)
                            <td>Cancelada</td>
                        @elseif($tipo["estado"] == -2)
                            <td>Cancelada</td>
                        @endif
                    @endif
                    <td>{{$tipo["fecha"]}}</td>
                    <td>{{$tipo["nombre"]}}</td>
                </tr>

            @endforeach
            </tbody>
        </table>

    </div>
@stop
