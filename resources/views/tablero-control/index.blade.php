@include('partials.errors')
@extends('layout')

@section('content')
    <div class='success'></div>
    <h1>TABLERO DE CONTROL</h1>
    <hr>

    <div class="table-responsive">
        <table class="table table-hover table-bordered">
            <thead>
            <tr>
                <th>Condición de análisis</th>
                <th>Núm Total</th>
                <th>Señal</th>
                <th>Acciones</th>
            </tr>
            </thead>
            <tbody>
                 <tr>
                     <td>Viajes: No validados y No conciliados</td>
                     <td>{{ $no_validados }}</td>
                     @if($no_validados > 0)
                         <td><button type="button" class="btn btn-danger btn-circle"></button></td>
                     @else
                         <td> <button type="button" class="btn btn-success btn-circle"></button></td>
                     @endif
                     <td>
                     <a href="{{ route('tablero-detalle.show',1) }}" title="Detalle" class="btn btn-xs btn-show"><i class="fa fa-eye"></i></a>
                     </td>
                 </tr>
                 <tr>
                     <td>Viajes: Validados y No conciliados</td>
                     <td>{{ $validados }}</td>
                     @if($validados > 0)
                         <td> <button type="button" class="btn btn-danger btn-circle"></button></td>
                     @else
                         <td> <button type="button" class="btn btn-success btn-circle"> </button></td>
                     @endif
                     <td>
                         <a href="{{ route('tablero-detalle.show',2) }}" title="Detalle" class="btn btn-xs btn-show"><i class="fa fa-eye"></i></a>
                     </td>
                 </tr>
            </tbody>
        </table>
    </div>
@stop