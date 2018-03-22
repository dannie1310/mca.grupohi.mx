@extends('layout')

@section('content')
    @if($tipo == 10)
        <h1>CAMBIO DE CUBICACIÓN EN LOS CAMIONES</h1>
        <h5>Fecha:{{$fecha_f}}</h5>
    <hr>

    <div class="table-responsive">
        <table class="table table-hover table-bordered small">
            <thead>
            <tr>
                <th>Económico</th>
                <th>Placas</th>
                <th>Cubicación para Pago Actual</th>
                <th>Cubicación Real Actual</th>
                <th>Cubicación para Pago Anterior</th>
                <th>Cubicación Real Anterior</th>
                <th>Fecha</th>
            </tr>
            </thead>
            <tbody>

            @foreach($datos as $d)
                <tr>
                    <td>{{ $d["economico"] }}</td>
                    <td>{{ $d["placas"] }}</td>
                    <td><div align="right">{{ $d["cubicacionPagoActual"] }}</div></td>
                    <td><div align="right">{{ $d["cubicacionRealActual"] }}</div></td>
                    <td><div align="right">{{ $d["cubicacionPago"]}}</div></td>
                    <td><div align="right">{{ $d["cubicacionReal"] }}</div></td>
                    <td>{{ $d["fecha"]}}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
    @elseif($tipo == 12)
        <h1>CAMIONES: MÁS DE 3 VIAJES EN UN TURNO.</h1>
        <h5>Fecha:{{$fecha_f}}</h5>
        <hr>

        <div class="table-responsive">
            <table class="table table-hover table-bordered small">
                <thead>
                <tr>
                    <th>Económico</th>
                    <th>Cubicación</th>
                    <th>Ticket</th>
                    <th>Origen</th>
                    <th>Fecha Salida</th>
                    <th>Destino</th>
                    <th>Fecha Llegada</th>
                    <th>Material</th>
                    <th>Folio Mina</th>
                    <th>Folio Seguimiento</th>
                    <th>Estado</th>
                    <th>Turno</th>
                </tr>
                </thead>
                <tbody>

                @foreach($primer_turno as $d)
                    <tr>
                        <td>{{ $d->economico }}</td>
                        <td><div align="right">{{ $d->cubicacion}}</div></td>
                        <td>{{ $d->Code }}</td>
                        <td>{{ $d->origen }}</td>
                        <td>{{ $d->fs }} {{ $d->hs }}</td>
                        <td>{{ $d->tiro }}</td>
                        <td>{{ $d->fl}} {{ $d->hl }}</td>
                        <td>{{ $d->material}}</td>
                        <td>{{$d->mina}}</td>
                        <td>{{$d->seguimiento}}</td>
                        <td>{{$d->estatus}}</td>
                        <td>PRIMER TURNO</td>
                    </tr>
                @endforeach

                @foreach($segundo_turno as $d)
                    <tr>
                        <td>{{ $d->economico }}</td>
                        <td>{{ $d->Placas }}</td>
                        <td><div align="right">{{ $d->cubicacion}}</div></td>
                        <td>{{ $d->Code }}</td>
                        <td>{{ $d->origen }}</td>
                        <td>{{ $d->fs }} {{ $d->hs }}</td>
                        <td>{{ $d->tiro }}</td>
                        <td>{{ $d->fl}} {{ $d->hl }}</td>
                        <td>{{ $d->material}}</td>
                        <td>{{$d->mina}}</td>
                        <td>{{$d->seguimiento}}</td>
                        <td>{{$d->estatus}}</td>
                        <td>SEGUNDO TURNO</td>
                    </tr>
                @endforeach

                </tbody>
            </table>
        </div>
    @endif
@stop
