@extends('layout')
@section('content')
    @include('reportes.inicio_viajes.table', ['data' => $data, 'request' => $request])
@stop