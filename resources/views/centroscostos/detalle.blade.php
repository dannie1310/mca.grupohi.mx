@extends('layout')

@section('content')
    <h1>CENTRO DE COSTO

    </h1>
    {!! Breadcrumbs::render('centroscostos.show', $centro) !!}
    <hr>
    <div class="panel panel-default">
        <!-- Default panel contents -->
        <div class="panel-heading">INFORMACIÓN DEL CENTRO DE COSTO</div>

        <ul class="list-group">

            <li class="list-group-item"><strong>CENTRO DE COSTO:</strong> {{$centro->Descripcion}}</li>
            <li class="list-group-item"><strong>CUENTA:</strong> {{$centro->Cuenta}}</li>
            <li class="list-group-item"><strong>ESTATUS:</strong>{{$centro->estatus_string }}</li>
            <li class="list-group-item"><strong>FECHA Y HORA REGISTRO:</strong> {{$centro->created_at}}</li>
            <li class="list-group-item"><strong>PERSONA QUE REGITRÓ:</strong> {{$centro->user_registro}}</li>

        </ul>
    </div>
@endsection