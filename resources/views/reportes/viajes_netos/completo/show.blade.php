@extends('layout')
@section('content')
    @include('reportes.viajes_netos.completo.table', ['data' => $data, 'request' => $request])
@stop