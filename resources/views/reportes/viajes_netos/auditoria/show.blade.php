@extends('layout')
@section('content')
    @include('reportes.viajes_netos.auditoria.table', ['data' => $data, 'request' => $request])
@stop