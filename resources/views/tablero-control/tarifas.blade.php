@extends('layout')

@section('content')
    @if($tipo == 11)
        <h1>TARIFAS DISTINTAS PARA UN MISMO MATERIAL</h1>
    @endif

    <h5>Fecha:{{$fecha_f}}</h5>
    <hr>

    <div class="table-responsive">
        <table class="table table-hover table-bordered small">
            <thead>
            <tr>
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
            </tr>
            </thead>
            <tbody>

            @foreach($tarifas as $d)
                <tr>

                    <td>{{ $d->descripcion }}</td>
                    <td>{{ $d->PrimerKM }}</td>
                    <td>{{ $d->KMSubsecuente }}</td>
                    <td>{{ $d->KMAdicional }}</td>
                    <td>{{ $d->InicioVigencia }}</td>
                    <td>{{ $d->FinVigencia }}</td>
                    @if($d->idtarifas_tipo==1)
                        <td>TARIFA FLETE</td>
                    @elseif($d->idtarifas_tipo==2)
                        <td>TARIFA SUMINISTRO + FLETE</td>
                    @else
                        <td>NO ASIGNADO</td>
                    @endif
                    <td>{{ $d->nombre }} {{$d->apaterno}} {{$d->amaterno}}</td>
                    <td>{{ $d->Fecha_Hora_Registra }}</td>
                    @if($d->Estatus == 1)
                        <td>ACTIVA</td>
                    @else
                        <td>DESACTIVADO</td>
                    @endif
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
@stop
