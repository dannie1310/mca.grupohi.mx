@extends('layout')

@section('content')
    <h1>CORTE {{ $corte->id }} <small>({{ $corte->estado }})</small>
        @if($corte->estatus == 2)
        <a href="{{ route('pdf.corte', $corte) }}" class="btn btn-success pull-right"><i class="fa fa-file-pdf-o"></i> VER PDF</a>
        @elseif($corte->estatus == 1)
        <a href="{{ route('corte.edit', $corte) }}" class="btn btn-primary pull-right"><i class="fa fa-pencil"></i> EDITAR CORTE</a>
        @endif
    </h1>
    {!! Breadcrumbs::render('corte.show', $corte) !!}
    <hr>
    <div class="row">
    <div class="col-md-6">
        <div class="panel panel-default">
            <div class="panel panel-heading">
                DETALLES DEL CORTE
            </div>
            <div class="panel-body">
                <strong>CHECADOR: </strong> {{ $corte->checador->present()->nombreCompleto }}<br>
                <strong>FECHA y HORA DEL CORTE: </strong> {{ $corte->timestamp->format('d-M-Y h:i:s a') }} <small>({{$corte->timestamp->diffForHumans()}})</small> <br>
                <strong>NÚMERO DE VIAJES: </strong> {{$corte->corte_detalles->count() }}
            </div>
        </div>
    </div>
    </div>
    <hr>
    <h3>VIAJES DEL CORTE</h3>
    <div class="table-responsive">
        <table class="table table-hover table-striped table-bordered small">
            <thead>
            <tr>
                <th style="text-align: center"> # </th>
                <th style="text-align: center"> Tipo </th>
                <th style="text-align: center"> Camión </th>
                <th style="text-align: center"> Ticket (Código) </th>
                <th style="text-align: center"> Fecha y Hora de Llegada </th>
                <th style="text-align: center"> Origen</th>
                <th style="text-align: center"> Tiro </th>
                <th style="text-align: center"> Material </th>
                <th style="text-align: center"> Cubicación	</th>
                <th style="text-align: center"> Importe </th>
                <th style="text-align: center"> Checador Primer Toque </th>
                <th style="text-align: center"> Checador Segundo Toque </th>
                <th style="text-align: center">Origen Nuevo</th>
                <th style="text-align: center">Material Nuevo</th>
                <th style="text-align: center">Cubic. Nueva</th>
                <th style="text-align: center">Observaciones</th>
            </tr>
            </thead>
            <tbody>
                @foreach($viajes_netos as $key => $viaje)
                <tr >
                    <td>{{ $key + 1 }}</td>
                    <td>{{ $viaje['tipo'] }}</td>
                    <td>{{ $viaje['camion'] }}</td>
                    <td>{{ $viaje['codigo'] }}</td>
                    <td>{{ $viaje['timestamp_llegada'] }}</td>
                    <td>{{ $viaje['origen'] }}</td>
                    <td>{{ $viaje['tiro'] }}</td>
                    <td>{{ $viaje['material'] }}</td>
                    <td style="text-align: right">{{ $viaje['cubicacion'] }} m<sup>3</sup></td>
                    <td style="text-align: right">${{ number_format($viaje['importe'], 2, ",", ".") }}</td>
                    <td>{{ $viaje['registro_primer_toque'] }}</td>
                    <td>{{ $viaje['registro'] }}</td>
                    <td>{{ $viaje['origen_nuevo'] }}</td>
                    <td>{{ $viaje['material_nuevo'] }}</td>
                    @if($viaje['cubicacion_nueva'])
                    <td style="text-align: right">{{ $viaje['cubicacion_nueva'] }} m<sup>3</sup></td>
                    @else
                        <td></td>
                    @endif
                    <td>{{ $viaje['observaciones'] }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection