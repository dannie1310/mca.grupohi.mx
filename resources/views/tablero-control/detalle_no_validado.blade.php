@extends('layout')

@section('content')
    @if($tipo == 1)
        <h1>VIAJES NO VALIDADOS Y NO CONCILIADOS</h1>
    @elseif($tipo == 2)
        <h1>VIAJES VALIDADOS Y NO CONCILIADOS</h1>
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
                <th>Folio Mina</th>
                <th>Folio Seguimiento</th>
                <th>Alerta</th>
            </tr>
            </thead>
            <tbody>

            @foreach($datos as $d)
               <tr>
                    <td>{{ $d->economico }}</td>
                    <td>{{ $d->origen }}</td>
                    <td>{{ $d->material }}</td>
                    <td><div align="right">{{ $d->cubicacion }}</div></td>
                    <td>{{ $d->fs }} {{$d->hs}}</td>
                    <td>{{ $d->tiro }}</td>
                    <td>{{ $d->fl }} {{$d->hl}}</td>
                    <td>{{ $d->code }}</td>
                    <td>{{ $d->foliomina }}</td>
                    <td>{{ $d->folioseg }}</td>
                    @if($d->alerta!="")
                        @if($d->alerta == 0)
                           <td><div align="center"><button type="button" class="btn btn-warning btn-circle"></button></div></td>
                        @else
                           <td><div align="center"><button type="button" class="btn btn-danger btn-circle"></button></div></td>
                        @endif
                    @endif
                </tr>
            @endforeach
            </tbody>
        </table>
        <div class="text-center">
            {!! $datos->appends(['buscar' => $busqueda])->render() !!}
        </div>
    </div>
@stop
