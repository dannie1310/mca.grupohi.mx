@extends('layout')

@section('content')
<h1>{{ strtoupper(trans('strings.edit_material')) }}</h1>
{!! Breadcrumbs::render('materiales.edit', $material) !!}
<hr>
@include('partials.errors')

{!! Form::model($material, ['method' => 'PATCH', 'route' => ['materiales.update', $material]]) !!}

<div class="form-horizontal col-md-6 col-md-offset-3 rcorners">
    <div class="form-group">
        {!! Form::label('Descripcion', 'Descripción', ['class' => 'control-label col-sm-3']) !!}
        <div class="col-sm-9">
            {!! Form::text('Descripcion', $material->Descripcion, ['class' => 'form-control', 'placeholder' => 'Descripción...']) !!}
        </div>
    </div>
    <div class="form-group">
        {!! Form::label('Estatus', 'Estatus', ['class' => 'control-label col-sm-3']) !!}
        <div class="col-sm-9">
            {!! Form::select('Estatus', ['1' => 'ACTIVO', '0' => 'INACTIVO'], null, ['placeholder' => 'Seleccione un Estatus...', 'class' => 'form-control']) !!}
        </div>
    </div>
    <div class="form-group " style="text-align: center">
        <a class="btn btn-info" href="{{ URL::previous() }}">Regresar</a>        
        {!! Form::submit('Actualizar', ['class' => 'btn btn-primary']) !!}
    </div>
</div>

{!! Form::close() !!}
@stop