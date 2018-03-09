@extends('layout')

@section('content')
    @if($tipo == 7)
        <h1>Cancelación de conciliaciones sin perfil de Gerente</h1>
    @endif


    <h5>Fecha:{{$fecha_f}}</h5>
    <hr>

    <div class="table-responsive">
        <table class="table table-hover table-bordered small">
            <thead>
            <tr>
                <th>Conciliación</th>
                <th>Motivo</th>
                <th>Fecha</th>
                <th>Usuario Canceló</th>
            </tr>
            </thead>
            <tbody>
            @foreach($conciliacion  as  $tipo)
                <tr>
                    <td>{{$tipo["conciliacion"]}}</td>
                    <td>{{$tipo["motivo"]}}</td>
                    <td>{{$tipo["fecha"]}}</td>
                    <td>{{$tipo["nombre"]}}</td>
                </tr>

            @endforeach
            </tbody>
        </table>

    </div>
@stop
