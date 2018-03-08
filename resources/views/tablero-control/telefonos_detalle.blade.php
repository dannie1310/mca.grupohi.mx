@extends('layout')

@section('content')
    @if($tipo == 3)
       <h1>USUARIOS CON DIFERENTES IMEI</h1>
    @elseif($tipo == 4 )
        <h1>IMEI CON DIFERENTES USUARIOS</h1>
    @elseif($tipo == 5)
        <h1>IMEI CON DIFERENTES IMPRESORA</h1>
    @elseif($tipo == 6)
        <h1>IMPRESORA CON DIFERENTES IMEI</h1>
    @endif


    <h5>Fecha:{{$fecha_f}}</h5>
    <hr>

    <div class="table-responsive">
        <table class="table table-hover table-bordered small">
            <thead>
            <tr>
                <th>#</th>
                <th>IMEI</th>
                <th>Modelo</th>
                <th>Marca</th>
                <th>Linea</th>
                <th>Checador</th>
                <th>Impresora</th>
            </tr>
            </thead>
            <tbody>
            @foreach($telefono  as  $tipo)
                <tr>
                    <td>{{$tipo["id"]}}</td>
                    <td>{{$tipo["imei"]}}</td>
                    <td>{{$tipo["modelo"]}}</td>
                    <td>{{$tipo["marca"]}}</td>
                    <td>{{$tipo["linea"]}}</td>
                    <td>{{$tipo["nombre"]}}</td>
                    @if($tipo["mac"]!="")
                        <td>{{$tipo["impresora"]}} - {{$tipo["mac"]}}</td>
                    @else
                        <td>{{$tipo["impresora"]}}</td>
                    @endif
                </tr>

            @endforeach
            </tbody>
        </table>

    </div>
@stop
