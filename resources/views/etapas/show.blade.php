@extends('layout')

@section('content')
<h1>{{ $etapa->Descripcion }}
    @permission('editar-etapas')
    <a style="margin-left: 5px" href="{{ route('etapas.edit', $etapa) }}" class="btn btn-info pull-right"><i class="fa fa-edit"></i> {{ trans('strings.edit') }}</a>
    @endpermission
    @permission('consultar-historico')
    <button type="button" id="ver_historico" class="btn btn-primary pull-right"><i class="fa fa-calendar"></i>
        Historico
    </button>
    @endpermission
</h1>
{!! Breadcrumbs::render('etapas.show', $etapa) !!}
<hr>
{!! Form::model($etapa) !!}
<div class="form-horizontal rcorners">
    <div class="form-group">
        {!! Form::label('Descripcion', 'Descripción', ['class' => 'control-label col-sm-3']) !!}
        <div class="col-sm-9">
            {!! Form::text('Descripcion', null, ['class' => 'form-control', 'disabled' => 'disabled']) !!}
        </div>
    </div>
</div>
<div class="form-group col-md-12" style="text-align: center; margin-top: 20px">
    {!! link_to_route('etapas.index', 'Regresar', [],  ['class' => 'btn btn-info'])!!}
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
                url: App.host + '/historico/etapas/{{$etapa->IdEtapaProyecto}}',
                success: function (response) {
                    $('#modal_historico').html(response);
                    $('#historicoModal').modal('show');
                }
            })
        });
    </script>
@endsection