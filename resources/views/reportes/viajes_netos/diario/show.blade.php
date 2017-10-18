@extends('layout')
@section('content')
@include('reportes.viajes_netos.diario.table', ['data' => $data, 'request' => $request])
@stop