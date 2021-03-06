@extends('layout')
@section('content')
    <h1>DETALLE DE USUARIO A PROYECTO</h1>
    @if(Auth::user()->hasRole(['administrador-permisos','administrador-sistema']))
    <a href="{{ route('usuario_proyecto.edit', $usuario[0]->id_usuario) }}" class="btn btn-info pull-right"><i class="fa fa-edit"></i> Editar</a>
    @endif
    </h1>
    {!! Breadcrumbs::render('usuario_proyecto.show', $usuario[0]->id_usuario) !!}
    <hr>
    <div class="panel panel-default">
        <!-- Default panel contents -->
        <div class="panel-heading">INFORMACIÓN DEL USUARIO</div>

        <!-- List group -->
        <ul class="list-group">
            <li class="list-group-item"><strong>ID:</strong> {{$usuario[0]->id_usuario}}</li>
            <li class="list-group-item"><strong>USUARIO:</strong> {{$usuario[0]->nombre}}</li>
            <li class="list-group-item"><strong>PROYECTO:</strong> {{$usuario[0]->proyecto}}</li>
            <li class="list-group-item"><strong>FECHA Y HORA REGISTRO:</strong> {{$usuario[0]->created_at}}</li>
            <li class="list-group-item"><strong>PERSONA QUE REGITRÓ:</strong> {{$usuario[0]->registro}}</li>
            <li class="list-group-item"><strong>ESTATUS:</strong> @if($usuario[0]->estatus==1)<span>Activado</span>@else <span>Desactivado</span> @endif</li>
        </ul>
    </div>
@endsection