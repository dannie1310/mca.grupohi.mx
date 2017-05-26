@extends('layout')

@section('content')
<h1>ETAPAS DE PROYECTO
  <a href="{{ route('etapas.create') }}" class="btn btn-success pull-right"><i class="fa fa-plus"></i> Nueva Etapa</a>
    <a href="{{ route('csv.etapas-proyecto') }}" style="margin-right: 5px" class="btn btn-info pull-right"><i class="fa fa-file-excel-o"></i> Descargar</a>
</h1>
{!! Breadcrumbs::render('etapas.index') !!}
<div class="table-responsive">
  <table class="table table-striped small">
    <thead>
      <tr>
        <th>ID Etapa</th>
        <th>Descripción</th>
        <th>Fecha Y Hora Registro</th>
        <th>Registró</th>
        <th>Estatus</th>
        <th>Acciones</th>
      </tr>
    </thead>
    <tbody>
      @foreach($etapas as $etapa)
        <tr>
          <td>
            <a href="{{ route('etapas.show', $etapa) }}">#{{ $etapa->IdEtapaProyecto }}</a>
          </td>
          <td>{{ $etapa->Descripcion }}</td>
          <td>{{ $etapa->created_at }}</td>
          <td>{{ $etapa->user_registro->present()->nombreCompleto() }}</td>
          <td>{{ $etapa->present()->estatus }}</td>
          <td>

            <a href="{{ route('etapas.show', $etapa) }}" title="Ver" class="btn btn-xs btn-default"><i class="fa fa-eye"></i></a>
            <a href="{{ route('etapas.edit', $etapa) }}" title="Editar" class="btn btn-xs btn-info"><i class="fa fa-pencil"></i></a>

            @if($etapa->Estatus == 1)
              <button type="submit" title="Desactivar" class="btn btn-xs btn-danger" onclick="desactivar_etapa({{$etapa->IdEtapaProyecto}})"><i class="fa fa-remove"></i></button>
            @else
              <button type="submit" title="Activar" class="btn btn-xs btn-success" onclick="activar_etapa({{$etapa->IdEtapaProyecto}})"><i class="fa fa-check"></i></button>
            @endif

          </td>
        </tr>
      @endforeach
    </tbody>
  </table>
  <form id="eliminar_etapa" method="POST">
    {{ csrf_field() }}
    <input type="hidden" name="_method" value="delete">
    <input type="hidden" name="motivo" value/>
  </form>

</div>
@stop



@section('scripts')
  <script>
      function desactivar_etapa(id) {
          var url = App.host + '/etapas/' + id;
          var form = $('#eliminar_etapa');

          swal({
                  title: "¡Desactivar etapa!",
                  text: "¿Esta seguro de que deseas desactivar la etapa?",
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

      function activar_etapa(id) {


          var url = App.host + '/etapas/' + id;
          var form = $('#eliminar_etapa');

          swal({
                  title: "¡Activar etapa!",
                  text: "¿Esta seguro de que deseas activar la etapa?",
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