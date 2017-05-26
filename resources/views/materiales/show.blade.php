@extends('layout')

@section('content')
    <h1>MATERIALES
        @permission('consultar-historico')
        <button type="button" id="ver_historico" class="btn btn-primary pull-right"><i class="fa fa-calendar"></i>
            Historico
        </button>
        @endpermission
    </h1>
    {!! Breadcrumbs::render('materiales.show', $material) !!}
    <hr>
    <div class="row"></div>
    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label>Descripción</label>
                <input type="text" class="form-control" value="{{$material->Descripcion}}" disabled>
            </div>
        </div>

        <div class="col-md-6">
            <div class="form-group">
                <label>Registró</label>
                <input type="text" class="form-control" value="{{$material->user_registro}}" disabled>
            </div>
        </div>

        <div class="col-md-6">
            <div class="form-group">
                <label>Fecha y Hora de Registro</label>
                <input type="text" class="form-control" value="{{$material->created_at->format('Y-M-d H:i:s a')}}" disabled>
            </div>
        </div>
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
                url: App.host + '/historico/materiales/{{$material->IdMaterial}}',
                success: function (response) {
                    $('#modal_historico').html(response);
                    $('#historicoModal').modal('show');
                }
            })
        });
    </script>
@endsection