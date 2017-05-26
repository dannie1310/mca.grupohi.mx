@extends('layout')

@section('content')
<h1>{{ strtoupper(trans('strings.operadores')) }}
  <a href="{{ route('operadores.create') }}" class="btn btn-success pull-right"><i class="fa fa-plus"></i> {{ trans('strings.new_operador') }}</a>
  <a href="{{ route('csv.operadores') }}" style="margin-right: 5px" class="btn btn-info pull-right"><i class="fa fa-file-excel-o"></i> Descargar</a>
</h1>
{!! Breadcrumbs::render('operadores.index') !!}
<hr>
@include('partials.search-form')
<div class="table-responsive">
  <table class="table table-striped small">
    <thead>
      <tr>
        <th>ID</th>
        <th>Nombre</th>
        <th>Dirección</th>
        <th>No. Licencia</th>
        <th>Vigencia Licencia</th>
        <th>Registró</th>
        <th>Fecha y Hora de Registro</th>
        <th>Estatus</th>
        <th>Acciones</th>
      </tr>
    </thead>
    <tbody>
      @foreach($operadores as $operador)
        <tr>
          <td>
            <a href="{{ route('operadores.show', $operador) }}">#{{ $operador->IdOperador }}</a>
          </td>
          <td>{{ $operador->Nombre }}</td>
          <td>{{ $operador->Direccion }}</td>
          <td>{{ $operador->NoLicencia }}</td>
          <td>{{ $operador->VigenciaLicencia }}</td>
          <td>{{ $operador->user_registro }}</td>
          <td>{{ $operador->created_at}}</td>
          <td>{{ $operador->present()->estatus }}</td>
          <td>

            <a href="{{ route('operadores.show', $operador) }}" title="Ver" class="btn btn-xs btn-default"><i class="fa fa-eye"></i></a>
            <a href="{{ route('operadores.edit', $operador) }}" title="Editar" class="btn btn-xs btn-info"><i class="fa fa-pencil"></i></a>
            @if($operador->Estatus == 1)
              <button type="submit" title="Desactivar" class="btn btn-xs btn-danger" onclick="desactivar_operador({{$operador->IdOperador}})"><i class="fa fa-remove"></i></button>
            @else
              <button type="submit" title="Activar" class="btn btn-xs btn-success" onclick="activar_operador({{$operador->IdOperador}})"><i class="fa fa-check"></i></button>
            @endif

          </td>
        </tr>
      @endforeach
    </tbody>
  </table>

  <form id="eliminar_operador" method="POST">
    {{ csrf_field() }}
    <input type="hidden" name="_method" value="delete">
    <input type="hidden" name="motivo" value/>
  </form>
  <div class="text-center">
    {!! $operadores->appends(['buscar' => $busqueda])->render() !!}
  </div>
</div>
@stop


@section('scripts')
  <script>
      function desactivar_operador(id) {
          var url = App.host + '/operadores/' + id;
          var form = $('#eliminar_operador');

          swal({
                  title: "¡Desactivar operador!",
                  text: "¿Esta seguro de que deseas desactivar el operador?",
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

      function activar_operador(id) {


          var url = App.host + '/operadores/' + id;
          var form = $('#eliminar_operador');

          swal({
                  title: "¡Activar operador!",
                  text: "¿Esta seguro de que deseas activar el operador?",
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