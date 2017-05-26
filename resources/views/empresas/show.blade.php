@extends('layout')

@section('content')
<h1>{{ $empresa->razonSocial }}
    @permission('editar-empresas')
    <a href="{{ route('empresas.edit', $empresa) }}" class="btn btn-info pull-right"><i class="fa fa-edit"></i> {{ trans('strings.edit') }}</a>
    @endpermission
    <button type="button" id="ver_historico" class="btn btn-primary pull-right"><i class="fa fa-calendar"></i>
        Historico
    </button>
</h1>
{!! Breadcrumbs::render('empresas.show', $empresa) !!}
<hr>
{!! Form::model($empresa) !!}
<div class="form-horizontal col-md-6 col-md-offset-3 rcorners">
    <div class="form-group">
        {!! Form::label('razonSocial', 'Razón Social', ['class' => 'control-label col-sm-3']) !!}
        <div class="col-sm-9">
            {!! Form::text('razonSocial', null, ['class' => 'form-control', 'disabled' => 'disabled']) !!}
        </div>
    </div>
    <div class="form-group">
        {!! Form::label('RFC', 'RFC', ['class' => 'control-label col-sm-3']) !!}
        <div class="col-sm-9">
            {!! Form::text('RFC', null, ['class' => 'form-control', 'disabled' => 'disabled']) !!}
        </div>
    </div>
    <div class="form-group">
        {!! Form::label('Estatus', 'Estatus', ['class' => 'control-label col-sm-3']) !!}
        <div class="col-sm-9">
            {!! Form::text('Estatus', $empresa->present()->estatus, ['class' => 'form-control', 'disabled' => 'disabled']) !!}
        </div>
    </div>

</div>
{!! Form::close() !!}
<div class="form-group col-md-12" style="text-align: center; margin-top: 20px">
    {!! link_to_route('empresas.index', 'Regresar', [],  ['class' => 'btn btn-info'])!!}
</div>
<div id="modal_historico">
</div>
@stop
@section('scripts')
    <script>
        $('#ver_historico').off().on('click', function (e) {
            e.preventDefault();
            $.ajax({
                type: 'GET',
                url: App.host + '/historico/camiones/{{$empresa->IdEmpresa}}',
                success: function (response) {
                    $('#modal_historico').html(response);
                    $('#historicoModal').modal('show');
                }
            })
        });
    </script>
@endsection