@extends('layout')

@section('content')
    <h1>VIAJES NO VALIDADOS Y NO CONCILIADOS</h1>
    <hr>
    @include('partials.search-form')
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
            </tr>
            </thead>
            <tbody>

            @foreach($datos as $d)
                <tr>
                    <td>{{ $d->economico }}</td>
                    <td>{{ $d->origen }}</td>
                    <td>{{ $d->material }}</td>
                    <td>{{ $d->cubicacion }}</td>
                    <td>{{ $d->fs }} {{$d->hs}}</td>
                    <td>{{ $d->tiro }}</td>
                    <td>{{ $d->fl }} {{$d->hl}}</td>
                    <td>{{ $d->code }}</td>
                    <td>{{ $d->foliomina }}</td>
                    <td>{{ $d->folioseg }}</td>

                </tr>
            @endforeach
            </tbody>
        </table>
        <div class="text-center">
            {!! $camiones->appends(['buscar' => $busqueda])->render() !!}
        </div>

        <form id="eliminar_camion" method="POST">
            {{ csrf_field() }}
            <input type="hidden" name="_method" value="delete">
            <input type="hidden" name="motivo" value/>
        </form>
    </div>
@stop
