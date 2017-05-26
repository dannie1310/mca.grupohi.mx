@extends('layout')

@section('content')
<h1>{{ strtoupper(trans('strings.sindicatos')) }}
  <a href="{{ route('sindicatos.create') }}" class="btn btn-success pull-right"><i class="fa fa-plus"></i> {{ trans('strings.new_sindicato') }}</a>
  <a href="{{ route('csv.sindicatos') }}" style="margin-right: 5px" class="btn btn-info pull-right"><i class="fa fa-file-excel-o"></i> Descargar</a>
</h1>
{!! Breadcrumbs::render('sindicatos.index') !!}
<hr>
<div class="table-responsive">
  <table class="table table-striped small">
    <thead>
      <tr>
        <th>ID Sindicato</th>
        <th>Descripción</th>
        <th>Nombre Corto</th>
        <th>Fecha Y Hora Registro</th>
        <th>Registró</th>
        <th>Estatus</th>
        <th width="160px">Acciones</th>
      </tr>
    </thead>
    <tbody>
      @foreach($sindicatos as $sindicato)
        <tr>
          <td>
            <a href="{{ route('sindicatos.show', $sindicato) }}">#{{ $sindicato->IdSindicato }}</a>
          </td>
          <td>{{ $sindicato->Descripcion }}</td>
          <td>{{ $sindicato->NombreCorto }}</td>
          <td>{{ $sindicato->created_at }}</td>
          <td>{{ $sindicato->user_registro->present()->nombreCompleto() }}</td>
          <td>{{ $sindicato->present()->estatus }}</td>
          <td>

            <a href="{{ route('sindicatos.show', $sindicato) }}" title="Ver" class="btn btn-xs btn-default"><i class="fa fa-eye"></i></a>
            <a href="{{ route('sindicatos.edit', $sindicato) }}" title="Editar" class="btn btn-xs btn-info"><i class="fa fa-pencil"></i></a>

            @if($sindicato->Estatus == 1)
              <button type="submit" title="Desactivar" class="btn btn-xs btn-danger" onclick="desactivar_sindicato({{$sindicato->IdSindicato}})"><i class="fa fa-remove"></i></button>
            @else
              <button type="submit" title="Activar" class="btn btn-xs btn-success" onclick="activar_sindicato({{$sindicato->IdSindicato}})"><i class="fa fa-check"></i></button>
            @endif

          </td>
        </tr>
      @endforeach
    </tbody>
  </table>
  <form id="eliminar_sindicato" method="POST">
    {{ csrf_field() }}
    <input type="hidden" name="_method" value="delete">
    <input type="hidden" name="motivo" value/>
  </form>
</div>
@stop


@section('scripts')
  <script>
      function desactivar_sindicato(id) {
          var url = App.host + '/sindicatos/' + id;
          var form = $('#eliminar_sindicato');

          swal({
                  title: "¡Desactivar sindicato!",
                  text: "¿Esta seguro de que deseas desactivar el sindicato?",
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

      function activar_sindicato(id) {


          var url = App.host + '/sindicatos/' + id;
          var form = $('#eliminar_sindicato');

          swal({
                  title: "¡Activar sindicato!",
                  text: "¿Esta seguro de que deseas activar el sindicato?",
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