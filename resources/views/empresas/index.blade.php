@extends('layout')

@section('content')
<h1>{{ strtoupper(trans('strings.empresas')) }}
  @permission('crear-empresas')
  <a href="{{ route('empresas.create') }}" class="btn btn-success pull-right"><i class="fa fa-plus"></i> {{ trans('strings.new_empresa') }}</a>
  @endpermission
    <a href="{{ route('csv.empresas') }}" style="margin-right: 5px" class="btn btn-info pull-right"><i class="fa fa-file-excel-o"></i> Descargar</a>
</h1>
{!! Breadcrumbs::render('empresas.index') !!}
<hr>
<div class="table-responsive">
  <table class="table table-striped small">
    <thead>
      <tr>
        <th>ID Empresa</th>
        <th>Razón Social</th>
        <th>RFC</th>
        <th>Fecha Y Hora Registro</th>
        <th>Registró</th>
        <th>Estatus</th>
        <th width="160px">Acciones</th>
      </tr>
    </thead>
    <tbody>
      @foreach($empresas as $empresa)
        <tr>
          <td>
            <a href="{{ route('empresas.show', $empresa) }}">#{{ $empresa->IdEmpresa }}</a>
          </td>
          <td>{{ $empresa->razonSocial }}</td>
          <td>{{ $empresa->RFC }}</td>
          <td>{{ $empresa->created_at }}</td>
          <td>{{ $empresa->user_registro }}</td>
          <td>{{ $empresa->present()->estatus }}</td>
          <td>
            <a href="{{ route('empresas.show', $empresa) }}" title="Ver" class="btn btn-xs btn-default"><i class="fa fa-eye"></i></a>
            @permission('editar-empresas')
            <a href="{{ route('empresas.edit', $empresa) }}" title="Editar" class="btn btn-xs btn-info"><i class="fa fa-pencil"></i></a>
            @endpermission
            @permission('desactivar-empresas')
            @if($empresa->Estatus == 1)
              <button type="submit" title="Desactivar" class="btn btn-xs btn-danger" onclick="desactivar_empresa({{$empresa->IdEmpresa}})"><i class="fa fa-remove"></i></button>
            @else
              <button type="submit" title="Activar" class="btn btn-xs btn-success" onclick="activar_empresa({{$empresa->IdEmpresa}})"><i class="fa fa-check"></i></button>
            @endif
            @endpermission
          </td>
          </td>
        </tr>
      @endforeach
    </tbody>
  </table>
  <form id="eliminar_empresa" method="POST">
    {{ csrf_field() }}
    <input type="hidden" name="_method" value="delete">
    <input type="hidden" name="motivo" value/>
  </form>


</div>
@stop



@section('scripts')
  <script>
      function desactivar_empresa(id) {
          var url = App.host + '/empresas/' + id;
          var form = $('#eliminar_empresa');

          swal({
                  title: "¡Desactivar empresa!",
                  text: "¿Esta seguro de que deseas desactivar la empresa?",
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

      function activar_empresa(id) {


          var url = App.host + '/empresas/' + id;
          var form = $('#eliminar_empresa');

          swal({
                  title: "¡Activar empresa!",
                  text: "¿Esta seguro de que deseas activar la empresa?",
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