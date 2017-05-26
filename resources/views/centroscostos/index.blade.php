@extends('layout')

@section('content')
<h1>{{ strtoupper(trans('strings.centroscostos')) }}
  <a href="{{ route('centroscostos.create', 0) }}" class="btn btn-success pull-right centrocosto_create"><i class="fa fa-plus"></i> NUEVO CENTRO DE COSTO</a>
    <a href="{{ route('csv.centros-costos') }}" style="margin-right: 5px" class="btn btn-info pull-right"><i class="fa fa-file-excel-o"></i> Descargar</a>

</h1>
{!! Breadcrumbs::render('centroscostos.index') !!}
<hr>
<div class="table-responsive">
    <table id='centros_costos_table' class="table table-hover small">
        <thead>
            <tr>
                <th>Centro de Costo</th>
                <th>Cuenta</th>
                <th>Fecha y hora registro</th>
                <th>Registró</th>
                <th>Estatus</th>
                <th>Acciones</th>

            </tr>
        </thead>
        <tbody>
            @foreach($centros as $centro)
            @if($centro->IdPadre == 0)
            <tr id="{{$centro->IdCentroCosto}}" class="treegrid-{{$centro->IdCentroCosto}}">
            @else
            <tr id="{{$centro->IdCentroCosto}}" class="treegrid-{{$centro->IdCentroCosto}} treegrid-parent-{{$centro->IdPadre}}">
            @endif
                <td>{{$centro->Descripcion}}</td>
                <td>{{$centro->Cuenta}}</td>
                <td>{{$centro->created_at}}</td>
                <td>{{$centro->user_registro->present()->nombreCompleto()}}</td>
                <td>{{$centro->estatus_string }}</td>
                <td>

                    <a href="{{ route('centroscostos.show', $centro) }}" title="Ver" class="btn btn-xs btn-default"><i class="fa fa-eye"></i></a>

                    <a href="{{ route('centroscostos.edit', $centro) }}" title="Editar" class="btn btn-xs btn-info centrocosto_edit"><i class="fa fa-pencil"></i></a>
                    @if($centro->Estatus == 1)
                        <button type="submit" title="Desactivar" class="btn btn-xs btn-danger" onclick="desactivar_centro({{$centro->IdCentroCosto}})"><i class="fa fa-remove"></i></button>
                    @else
                        <button type="submit" title="Activar" class="btn btn-xs btn-success" onclick="activar_centro({{$centro->IdCentroCosto}})"><i class="fa fa-check"></i></button>
                    @endif
                    <a href="{{ route('centroscostos.create', $centro) }}" class="btn btn-success btn-xs centrocosto_create" type="button">
                        <i class="fa fa-plus-circle"></i>
                    </a>

                </td>

            </tr>
            @endforeach
        </tbody>             
    </table>


    <form id='delete' method="post">
        <input type='hidden' name='motivo' value/>
        {{csrf_field()}}
        <input type="hidden" name="_method" value="delete"/>
        <input type="hidden" name="idCentro"/>

    </form>
</div>

@endsection
@section('scripts')
    <script>
        function desactivar_centro(id) {
            var form = $('#delete');
            $('#idCentro').val(id);
            var url=App.host +"/centroscostos/"+id;

            swal({
                    title: "¡Desactivar centro de costo!",
                    text: "¿Esta seguro de que deseas desactivar el centro de costo?",
                    type: "input",
                    showCancelButton: true,
                    closeOnConfirm: false,
                    inputPlaceholder: "Motivo de la desactivación.",
                    confirmButtonText: "Si, Desactivar",
                    cancelButtonText: "No, Cancelar",
                    showLoaderOnConfirm: true

                },
                function(inputValue){
                    if (inputValue === false) return false;
                    if (inputValue === "") {
                        swal.showInputError("Escriba el motivo de la eliminación!");
                        return false
                    }
                    form.attr("action", url);
                    $("input[name=motivo]").val(inputValue);
                    form.submit();
                });
        }

        function activar_centro(id) {
            var form = $('#delete');
            $('#idCentro').val(id);
            var url=App.host +"/centroscostos/"+id;

            swal({
                    title: "¡Activar centro de costo!",
                    text: "¿Esta seguro de que deseas activar el centro de costo?",
                    type: "warning",
                    showCancelButton: true,
                    closeOnConfirm: false,
                    inputPlaceholder: "Motivo de la activación.",
                    confirmButtonText: "Si, Activar",
                    cancelButtonText: "No, Cancelar",
                    showLoaderOnConfirm: true

                },
                function(){
                    form.attr("action", url);
                    $("input[name=motivo]").val("");
                    form.submit();
                });
        }


    </script>
</div>
<div id="div_modal"></div>
@stop

