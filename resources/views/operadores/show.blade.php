@extends('layout')

@section('content')
<h1>{{ $operador->Nombre }}
    @permission('editar-operadores')
    <a style="margin-left: 5px" href="{{ route('operadores.edit', $operador) }}" class="btn btn-info pull-right"><i class="fa fa-edit"></i> EDITAR</a>
    @endpermission
    <button type="button" id="ver_historico" class="btn btn-primary pull-right"><i class="fa fa-calendar"></i>
        Historico
    </button>
</h1>
{!! Breadcrumbs::render('operadores.show', $operador) !!}
<hr>
{!! Form::model($operador) !!}
<div class="form-horizontal col-md-6 col-md-offset-3 rcorners">
    <div class="form-group">
        {!! Form::label('Nombre', 'Nombre', ['class' => 'control-label col-sm-2']) !!}
        <div class="col-sm-10">
            {!! Form::text('Nombre', null, ['class' => 'form-control', 'disabled' => 'disabled']) !!}
        </div>
    </div>
    <div class="form-group">
        {!! Form::label('Direccion', 'Dirección', ['class' => 'control-label col-sm-2']) !!}
        <div class="col-sm-10">
            {!! Form::text('Direccion', null, ['class' => 'form-control', 'disabled' => 'disabled']) !!}
        </div>
    </div>
    <div class="form-group">
        {!! Form::label('NoLicencia', 'Número de Licencia', ['class' => 'control-label col-sm-2']) !!}
        <div class="col-sm-4">
            {!! Form::text('NoLicencia', null, ['class' => 'form-control', 'disabled' => 'disabled']) !!}
        </div>
        {!! Form::label('VigenciaLicencia', 'Vigencia de Licencia', ['class' => 'control-label col-sm-2']) !!}
        <div class="col-sm-4">
            {!! Form::text('VigenciaLicencia', null, ['class' => 'form-control', 'disabled' => 'disabled']) !!}
        </div>
    </div>
</div>
<div class="form-group col-md-12" style="text-align: center; margin-top: 20px">
    {!! link_to_route('operadores.index', 'Regresar', [],  ['class' => 'btn btn-info'])!!}
</div>
{!! Form::close() !!}
<div id="modal_historico">
</div>
@stop
@section('scripts')
    <script>
        $('#ver_historico').off().on('click', function (e) {
            e.preventDefault();
            $.ajax({
                type: 'GET',
                url: App.host + '/historico/operadores/{{$operador->IdOperador}}',
                success: function (response) {
                    $('#modal_historico').html(response);
                    $('#historicoModal').modal('show');
                }
            })
        });
    </script>
@endsection