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
                <th>#</th>
                <th>Condición de análisis</th>
                <th><div align="center">Total</div></th>
                <th><div align="center">Señal</div></th>
                <th><div align="center">Acciones</div></th>
            </tr>
            </thead>
            <tbody>
                @foreach($datos as $c)
                    <tr>
                        <td>{{$contador++}}</td>
                        <td>{{$c[0]}}</td>
                        <td><div align="center">{{number_format($c[1],0,".",",")  }}</div></td>
                        @if($c[1] > 0)
                            <td><div align="center"> <button type="button" class="btn btn-danger btn-circle"></button></div></td>
                            <td width="20"><div align="center">
                                    <a href="{{ route('tablero-detalle.show',$c[2]) }}" title="Detalle" class="btn btn-xs btn-show"><i class="fa fa-eye"></i></a></div>
                            </td>
                        @else
                            <td><div align="center"><button type="button" class="btn btn-success btn-circle"></button></div></td>
                            <td width="20"><div align="center">
                                    <i class="fa fa-eye-slash"></i></div>
                            </td>
                        @endif
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@stop