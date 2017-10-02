@extends('layout')

@section('content')
<h1>CAMIONES
    @permission('crear-camiones')
  <a href="{{ route('camiones.create') }}" class="btn btn-success pull-right"><i class="fa fa-plus"></i> {{ trans('strings.new_camion') }}</a>
    @endpermission
    <a href="{{ route('csv.camiones') }}" style="margin-right: 5px" class="btn btn-info pull-right"><i class="fa fa-file-excel-o"></i> Excel</a>
</h1>
{!! Breadcrumbs::render('camiones.index') !!}
<hr>
@include('partials.search-form')
<div class="table-responsive">
  <table class="table table-hover table-bordered small">
      <thead>
      <tr>
        <th style="text-align: center" colspan="3"></th>
        <th style="text-align: center" colspan="2" >Cubicación</th>
        <th colspan="4"></th>
      </tr>
      <tr>
        <th>Económico</th>
        <th>Propietario</th>
        <th>Operador</th>
        <th>Real</th>
        <th>Para Pago</th>
          <th>Fecha Y Hora Registro</th>
          <th>Registró</th>
        <th>Estatus</th>
        <th width="100">Acciones</th>
      </tr>
    </thead>
    <tbody>

      @foreach($camiones as $camion)
        <tr>
          <td>
              <a href="{{ route('camiones.show', $camion) }}">{{ $camion->Economico }}</a>
          </td>
          <td>{{ $camion->Propietario }}</td>
          <td>{{ isset($camion->operador->Nombre) ? $camion->operador->Nombre : 'SIN OPERADOR' }}</td>
          <td>{{ $camion->CubicacionReal}} m<sup>3</sup></td>
          <td>{{ $camion->CubicacionParaPago}} m<sup>3</sup></td>
            <td>{{ $camion->FechaAlta }} {{ $camion->HoraAlta }}</td>
            <td>{{ $camion->user_registro }}</td>

            <td>{{ $camion->present()->estatus }}</td>
          <td>

              <a href="{{ route('camiones.show', $camion) }}" title="Ver" class="btn btn-xs btn-default"><i class="fa fa-eye"></i></a>
              @permission('editar-camiones')
              <a href="{{ route('camiones.edit', $camion) }}" title="Editar" class="btn btn-xs btn-info"><i class="fa fa-pencil"></i></a>
              @endpermission
              @permission('desactivar-camiones')
              @if($camion->Estatus == 1)
                  <button type="submit" title="Desactivar" class="btn btn-xs btn-danger" onclick="desactivar_camion({{$camion->IdCamion}})"><i class="fa fa-remove"></i></button>
              @else
                  <button type="submit" title="Activar" class="btn btn-xs btn-success" onclick="activar_camion({{$camion->IdCamion}})"><i class="fa fa-check"></i></button>
              @endif
              @endpermission
          </td>
        </tr>
      @endforeach
    </tbody>
  </table>
  <div class="text-center">
    {!! $camiones->appends(['buscar' => $busqueda])->render() !!}
  </div>

    <form id="eliminar_camion" method="POST">
        {{ csrf_field() }}
        <input type="hidden" name="_method" value="delete">
        <input type="hidden" name="motivo" value/>
    </form>
</div>
@stop

@section('scripts')
    <script>
        function desactivar_camion(id) {
            var url = App.host + '/camiones/' + id;
            var form = $('#eliminar_camion');

            swal({
                    title: "¡Desactivar camión!",
                    text: "¿Esta seguro de que deseas desactivar el camión?",
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
                        swal.showInputError("Escriba el motivo de la desactivación!");
                        return false
                    }
                    form.attr("action", url);
                    $("input[name=motivo]").val(inputValue);
                    form.submit();
                });
        }

        function activar_camion(id) {


            var url = App.host + '/camiones/' + id;
            var form = $('#eliminar_camion');

            swal({
                    title: "¡Activar camión!",
                    text: "¿Esta seguro de que deseas activar el camión?",
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
     @endsection