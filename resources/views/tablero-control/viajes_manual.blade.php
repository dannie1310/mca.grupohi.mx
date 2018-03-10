@extends('layout')

@section('content')
    @if($tipo == 8)
        <h1>CAMIÓN CON MÁS VIAJES MANUALES</h1>
    @endif


    <h5>Fecha:{{$fecha_f}}</h5>
    <hr>

    <div class="table-responsive">
        <table class="table table-hover table-bordered small">
            <thead>
            <tr>
                <th>Económico</th>
                <th>Origen</th>
                <th>Material</th>
                <th>Cubicación</th>
                <th>Fecha Salida</th>
                <th>Destino</th>
                <th>Fecha Llegada</th>
                <th>Ticket</th>
                <th>Estado</th>
            </tr>
            </thead>
            <tbody>
            @foreach($datos  as  $d)
                <tr>
                    <td>{{ $d["economico"] }}</td>
                    <td>{{ $d["origen"] }}</td>
                    <td>{{ $d["material"] }}</td>
                    <td><div align="right">{{ $d["cubicacion"] }}</div></td>
                    <td>{{ $d["fs"] }} {{$d["hs"]}}</td>
                    <td>{{ $d["tiro"] }}</td>
                    <td>{{ $d["fl"] }} {{$d["hl"]}}</td>
                    <td>{{ $d["code"] }}</td>
                    <td>{{ $d["estatus"] }}</td>
                </tr>

            @endforeach
            </tbody>
        </table>

    </div>
@stop
