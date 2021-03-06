@extends('layout')

@section('content')
<h1>{{ strtoupper(trans('strings.edit')) }}</h1>
{!! Breadcrumbs::render('origenes.edit', $origen) !!}
<hr>
@include('partials.errors')

{!! Form::model($origen, ['method' => 'PATCH', 'route' => ['origenes.update', $origen]]) !!}

<div class="form-horizontal col-md-6 col-md-offset-3 rcorners">
    <div class="form-group">
        {!! Form::label('Clave', 'Clave', ['class' => 'control-label col-sm-3']) !!}
        <div class="col-sm-9">
            {!! Form::text('Clave', $origen->present()->claveOrigen, ['class' => 'form-control', 'disabled' => 'disabled']) !!}
        </div>
    </div>
    <div class="form-group">
        {!! Form::label('IdTipoOrigen', 'Tipo de Origen', ['class' => 'control-label col-sm-3']) !!}
        <div class="col-sm-9">
            {!! Form::text('IdTipoOrigen', $tipos->present()->Descripcion,['class' => 'form-control', 'disabled' => 'disabled']) !!}
        </div>
    </div>
     <div class="form-group">
        {!! Form::label('Descripcion', 'Descripción', ['class' => 'control-label col-sm-3']) !!}
        <div class="col-sm-9">
            {!! Form::text('Descripcion', $origen->present()->Descripcion, ['class' => 'form-control', 'placeholder' => 'Descripción...', 'disabled' => 'disabled']) !!}
        </div>
    </div>
    @if(Auth::user()->can(['modificar_tipo_origen']))
    <div class="form-group">
        {!! Form::label('Tipo', 'Tipo', ['class' => 'control-label col-sm-3']) !!}
        <div class="col-sm-9">
            {!! Form::select('interno', ['1' => 'INTERNO','0' => 'EXTERNO'], null, ['placeholder' => '--SELECCIONE--', 'class' => 'form-control']) !!}
        </div>

    </div>
    @else
        <div class="form-group">
            {!! Form::label('Tipo', 'Tipo', ['class' => 'control-label col-sm-3']) !!}
            <div class="col-sm-9">
                @if($origen->interno == 0)
                    {!! Form::text('interno', 'EXTERNO', [ 'class' => 'form-control', 'disabled' => 'disabled']) !!}
                @else
                    {!! Form::text('interno', 'INTERNO', [ 'class' => 'form-control', 'disabled' => 'disabled']) !!}
                @endif
            </div>

        </div>
    @endif
</div>
<div class="form-group col-md-12" style="text-align: center; margin-top: 20px">
    <a class="btn btn-info" href="{{ URL::previous() }}">Regresar</a>
    @if(Auth::user()->can(['modificar_tipo_origen']))
        {!! Form::submit('Actualizar', ['class' => 'btn btn-primary']) !!}
    @endif
</div>
{!! Form::close() !!}
@stop