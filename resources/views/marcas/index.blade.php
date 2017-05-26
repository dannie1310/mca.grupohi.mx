@extends('layout')

@section('content')
<h1>{{ strtoupper(trans('strings.brands')) }}
    @permission('crear-marcas')
  <a href="{{ route('marcas.create') }}" class="btn btn-success pull-right"><i class="fa fa-plus"></i> {{ trans('strings.new_brand') }}</a>
    @endpermission
    <a href="{{ route('csv.marcas') }}" style="margin-right: 5px" class="btn btn-info pull-right"><i class="fa fa-file-excel-o"></i> Descargar</a>

</h1>
{!! Breadcrumbs::render('marcas.index') !!}
<hr>
<div class="table-responsive">
  <table class="table table-striped small">
    <thead>
      <tr>
        <th>ID Marca</th>
        <th>Descripción</th>
        <th>Fecha Y Hora Registro</th>
        <th>Registró</th>
        <th>Estatus</th>
        <th>Acciones</th>
      </tr>
    </thead>
    <tbody>
      @foreach($marcas as $marca)
        <tr>
          <td>
            <a href="{{ route('marcas.show', $marca) }}">#{{ $marca->IdMarca }}</a>
          </td>
          <td>{{ $marca->Descripcion }}</td>
          <td>{{ $marca->created_at }}</td>
          <td>{{ $marca->user_registro }}</td>
          <td>{{ $marca->present()->estatus }}</td>
          <td>

            <a href="{{ route('marcas.show', $marca) }}" title="Ver" class="btn btn-xs btn-default"><i class="fa fa-eye"></i></a>
            @permission('editar-marcas')
              <a href="{{ route('marcas.edit', $marca) }}" title="Editar" class="btn btn-xs btn-info"><i class="fa fa-pencil"></i></a>
            @endpermission
            @permission('desactivar-marcas')
            @if($marca->Estatus == 1)
              <button type="submit" title="Desactivar" class="btn btn-xs btn-danger" onclick="desactivar_marcas({{$marca->IdMarca}})"><i class="fa fa-remove"></i></button>
            @else
              <button type="submit" title="Activar" class="btn btn-xs btn-success" onclick="activar_marcas({{$marca->IdMarca}})"><i class="fa fa-check"></i></button>
            @endif
            @endpermission
          </td>
        </tr>
      @endforeach
    </tbody>
  </table>
  <form id="eliminar_marca" method="POST">
    {{ csrf_field() }}
    <input type="hidden" name="_method" value="delete">
    <input type="hidden" name="motivo" value/>
  </form>
</div>
@stop


@section('scripts')
  <script>
      function desactivar_marcas(id) {
          var url = App.host + '/marcas/' + id;
          var form = $('#eliminar_marca');

          swal({
                  title: "¡Desactivar marca!",
                  text: "¿Esta seguro de que deseas desactivar la marca?",
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

      function activar_marcas(id) {


          var url = App.host + '/marcas/' + id;
          var form = $('#eliminar_marca');

          swal({
                  title: "¡Activar marca!",
                  text: "¿Esta seguro de que deseas activar la marca?",
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