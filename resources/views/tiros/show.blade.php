@extends('layout')

@section('content')
<h1>{{ $tiro->Descripcion }}
 </h1>
{!! Breadcrumbs::render('tiros.show', $tiro) !!}
@permission('consultar-historico')
<button type="button" id="ver_historico" class="btn btn-primary pull-right"><i class="fa fa-calendar"></i>
    Historico
</button>
@endpermission
<hr>
{!! Form::model($tiro) !!}
<div class="form-horizontal col-md-6 col-md-offset-3 rcorners">
    <div class="form-group">
        {!! Form::label('Clave', 'Clave', ['class' => 'control-label col-sm-3']) !!}
        <div class="col-sm-9">
            {!! Form::text('Clave', $tiro->present()->claveTiro, ['class' => 'form-control', 'disabled' => 'disabled']) !!}
        </div>
    </div>
    <div class="form-group">
        {!! Form::label('Descripcion', 'Descripción', ['class' => 'control-label col-sm-3']) !!}
        <div class="col-sm-9">
            {!! Form::text('Descripcion', null, ['class' => 'form-control', 'disabled' => 'disabled']) !!}
        </div>
    </div>
</div>
{!! Form::close() !!}
<div class="form-group col-md-12" style="text-align: center; margin-top: 20px">
    {!! link_to_route('tiros.index', 'Regresar', [],  ['class' => 'btn btn-info'])!!}
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
                url: App.host + '/historico/tiros/{{$tiro->IdTiro}}',
                success: function (response) {
                    $('#modal_historico').html(response);
                    $('#historicoModal').modal('show');
                }
            })
        });
    </script>
@endsection