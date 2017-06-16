@extends('layout')
@section('content')
    @include('reportes.conciliacion_detalle.table', ['data' => $data, 'request' => $request])
@stop