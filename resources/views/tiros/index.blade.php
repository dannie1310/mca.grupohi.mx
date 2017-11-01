@extends('layout')

@section('content')
<h1>{{ strtoupper(trans('strings.tiros')) }}
    @permission('crear-tiros')
    <a href="{{ route('tiros.create') }}" class="btn btn-success pull-right"><i class="fa fa-plus"></i> NUEVO TIRO</a>
    @endpermission
    <a href="{{ route('csv.tiros') }}" style="margin-right: 5px" class="btn btn-default pull-right"><i class="fa fa-file-excel-o"></i> EXCEL</a>
</h1>
{!! Breadcrumbs::render('tiros.index') !!}
<hr>
<div class="table-responsive">
  <table class="table table-striped small" id="index_tiros">
    <thead>
      <tr>
        <th>Clave</th>
        <th>Descripción</th>
        <th>Fecha y hora registro</th>
        <th>Registró</th>
        <th>Estatus</th>
        <th>Acciones</th>
        <th>Concepto</th>
      </tr>
    </thead>
    <tbody>
      @foreach($tiros as $tiro)
        <tr>
          <td>
            {{ $tiro->present()->claveTiro }}
          </td>
          <td>{{ $tiro->Descripcion }}</td>
          <td>{{$tiro->created_at->format('d-M-Y h:i:s a')}}</td>
          <td>{{$tiro->user_registro}}</td>
          <td>{{ $tiro->present()->estatus }}</td>
          <td>
            <a href="{{ route('tiros.show', $tiro) }}" title="Ver" class="btn btn-xs btn-default"><i class="fa fa-eye"></i></a>
          @permission('desactivar-tiros')
              @if($tiro->Estatus == 1)
              <button type="submit" title="Desactivar" class="btn btn-xs btn-danger" onclick="desactivar_tiro({{$tiro->IdTiro}})"><i class="fa fa-remove"></i></button>
            @else
              <button type="submit" title="Activar" class="btn btn-xs btn-success" onclick="activar_tiro({{$tiro->IdTiro}})"><i class="fa fa-check"></i></button>
            @endif
              @endpermission
          </td>
          <td id="{{$tiro->IdTiro}}" title="{{ $tiro->concepto() ? $tiro->concepto()->path : '' }}">
                @if($tiro->concepto())
                  <a onclick="aviso('{{$tiro->concepto()}}', '{{ $tiro->IdTiro }}', '{{ $tiro->Descripcion}}')">{{$tiro->concepto()}}</a>
                @else
                  <a href="" data-toggle="modal" data-target="#myModal" onclick="datosTiro('{{$tiro->IdTiro}}' , '{{ $tiro->Descripcion}}')">--ASIGNAR--</a>
                @endif
          </td>
        </tr>
      @endforeach
    </tbody>
  </table>
  <form id='delete' method="post">
    <input type='hidden' name='motivo' value/>
    {{csrf_field()}}
    <input type="hidden" name="_method" value="delete"/>
  </form>
</div>
@stop

<!-- Modal -->
<div class="modal fade" id="myModal"  role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h3 class="modal-title" id="myModalLabel">Asignación de Concepto</h3>
                <p class="alert alert-warning text-center" id="titulo"></p>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    {!! Form::label('concepto', 'Concepto:') !!}
                    <div class="input-group">
                        <select class="form-control" id="concepto-select" ></select>
                        <div type="button" class="input-group-addon btn" onclick="showTree()">
                            <i class="fa fa-fw fa-sitemap"></i>
                        </div>
                        {!! Form::hidden('id_concepto', null, ['class' => 'form-control', 'id' => 'id_concepto']) !!}
                    </div>
                </div>
                <div id="jstree"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-success" onclick="validar_tiro()">Asignar</button>
                <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>


@section('scripts')
  <script>
      var idTiro;
      function datosTiro(id, desc) {
          idTiro = id;
          $('#titulo').text(('Seleccione el Concepto que desea asignar al Tiro ' + desc).toUpperCase());
      }

      function aviso(concepto, id, desc) {
          swal({
              title: "¡Asignar Concepto!",
              text: "El tiro <strong>" + desc + "</strong> esta asignado al Concepto <strong>" + concepto + "</strong>\n¿Desea actualizar el Concepto del tiro mencionado?",
              type: "info",
              showCancelButton: true,
              closeOnConfirm: true,
              confirmButtonText: "Si, Actualizar",
              cancelButtonText: "No, Cancelar",
              showLoaderOnConfirm: true,
              html: true

              },
              function(){
                  datosTiro(id, desc);
                  $('#myModal').modal('show');
              });


          /*confirmar=confirm('El tiro ' + desc + ' esta asignado al Concepto ' + concepto + '\n¿Desea actualizar el Concepto del tiro mencionado?' );
          if (confirmar) {
              datosTiro(id, desc);
              $('#myModal').modal('show');
          }*/
      }

      var auth_config = {
          auto_filter: true,
          col_0: 'input',
          col_1: 'input',
          col_2: 'input',
          col_3: 'select',
          col_4: 'select',
          col_5: 'none',
          base_path: App.tablefilterBasePath,
          auto_filter: true,
          paging: false,
          rows_counter: true,
          rows_counter_text: 'Tiros: ',
          btn_reset: true,
          btn_reset_text: 'Limpiar',
          clear_filter_text: 'Limpiar',
          loader: true,
          page_text: 'Pagina',
          of_text: 'de',
          help_instructions: false,
          extensions: [{ name: 'sort' }]
      };
      var tf = new TableFilter('index_tiros', auth_config);
      tf.init();

      function desactivar_tiro(id) {
          var form = $('#delete');
          var url=App.host +"/tiros/"+id;

          swal({
                  title: "¡Desactivar tiro!",
                  text: "¿Esta seguro de que deseas desactivar el tiro?",
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

      function activar_tiro(id) {

          var form = $('#delete');
          var url=App.host +"/tiros/"+id;

          swal({
                  title: "¡Activar Tiro!",
                  text: "¿Esta seguro de que deseas activar el tiro?",
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

      function validar_tiro() {

          swal({
              title: "¡Asignar Concepto!",
              text: "¿Esta seguro de que deseas asignar el concepto?",
              type: "warning",
              showCancelButton: true,
              closeOnConfirm: true,
              confirmButtonText: "Si, Asignar",
              cancelButtonText: "No, Cancelar",
              showLoaderOnConfirm: true

          },
          function(){
              asignar();
          });
      }

      function asignar() {
          var url=App.host +"/tiros/asignar_concepto";

          $.ajax({
              type: 'POST',
              url: url,
              data: {
                  id_tiro: idTiro,
                  id_concepto: $('#id_concepto').val()
              },
              beforeSend: function beforeSend() {
                  self.guardando = true;
              },
              success: function (data, textStatus, xhr) {
                  swal({
                      type: "success",
                      title: '¡Correcto!',
                      text: data.mensaje
                  });

                    var concept = data.concepto.descripcion.replace(/"/g, '&quot;');
                  $('#' + idTiro)
                      .attr('title', "'" + data.concepto.path +"'")
                      .html('<a onclick="aviso('+ "'" + concept + "'" +','+ "'" + data.tiro.IdTiro + "'" +','+ "'" + data.tiro.Descripcion + "'" +')">'+ data.concepto.descripcion +'</a>');
              },
              error: function(error) {
                  var salida = '';
                  $.each($.parseJSON(error.responseText), function (ind, elem) {
                      salida += elem + '\n';
                  });
                  swal({
                      type: 'error',
                      title: '¡Error!',
                      text: salida
                  });
              }
          });
          $('#myModal').modal('hide');

      }

      var select_settings = {
          dropdownAutoWidth : true,
          language: "es",
          ajax: {
              url: '{{route('conceptos.lists')}}',
              dataType: 'json',
              data: function (params) {
                  var query = {
                      q: params.term,
                  }
                  return query;
              },
              processResults: function (data) {
                  var results = [];
                  $.each(data.conceptos, function (id, concepto) {
                      results.push({id: concepto.id_concepto, text: concepto.descripcion})
                  })
                  return { results : results};
              }
          }
      }


      // JsTree Configuration
      var jstreeConf = {
          'core' : {
              'multiple': false,
              'data': {
                  "url": function(node) {
                      if (node.id === "#") {
                          var id_concepto = $('#id_concepto').val();
                          if (! id_concepto) {
                              return App.host + '/conceptos/jstree';
                          } else {
                              return App.host + '/conceptos/' + id_concepto + '/jstree';
                          }
                      }
                      return App.host + '/conceptos/' + node.id + '/jstree';
                  },
                  "data": function (node) {
                      return { "id" : node.id };
                  }
              }
          },
          'types': {
              'default': {
                  'icon': 'fa fa-folder-o text-success'
              },
              'medible': {
                  'icon': 'fa fa-file-text'
              },
              'material' : {
                  'icon': 'fa fa-briefcase'
              },
              'opened' : {
                  'icon': 'fa fa-folder-open-o text-success'
              }
          },
          'plugins': ['types']
      };

      function conf_tree() {
          $('#jstree').on("after_open.jstree", function (e, data) {
              if (data.instance.get_type(data.node) == 'default') {
                  data.instance.set_type(data.node, 'opened');
              }
          }).on("after_close.jstree", function (e, data) {
              if (data.instance.get_type(data.node) == 'opened') {
                  data.instance.set_type(data.node, 'default');
              }
          }).on("select_node.jstree", function(event, node) {
              $('#id_concepto').val(node.node.id);
              var new_settings = $.extend({}, select_settings);
              new_settings.data = [{id: node.node.id, text: node.node.text}];
              $('#concepto-select').select2('destroy').empty().select2(new_settings);

          });
      }

      // On hide the BS modal, get the selected node and destroy the jstree
      $('#myModal').on('shown.bs.modal', function (e) {
          $('#concepto-select').select2(select_settings).on('select2:select', function (e) {
              $('#id_concepto').val(e.params.data.id);
          });
      }).on('hidden.bs.modal', function (e) {
          var jstree = $('#jstree').jstree(true);
          if(jstree)
              jstree.destroy();
          $("#concepto-select option:selected").each(function () {
              $(this).remove();
          });
          $('#id_concepto').val('');
      });

      function showTree() {
          var jstree = $('#jstree').jstree(true);
          if(jstree)
              jstree.destroy();
          $('#jstree').jstree(jstreeConf);
          conf_tree();
      }

  </script>
@endsection