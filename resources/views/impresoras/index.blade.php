@extends('layout')
@section('content')
    <h1>IMPRESORAS
        <a href="{{ route('impresoras.create') }}" class="btn btn-success pull-right" ><i class="fa fa-plus"></i> Nueva Impresora </a>
    </h1>
  {!! Breadcrumbs::render('impresoras.index')!!}
    <hr>
     <div class="table-responsive">
         <table class="table table-hover table-striped small">
             <thead>
             <tr>
                 <th>ID</th>
                 <th>MAC Address</th>
                 <th>Marca</th>
                 <th>Modelo</th>
                 <th>Fecha y hora registro</th>
                 <th>Registró</th>
                 <th>Acciones</th>
             </tr>
             </thead>
             <tbody>
            @foreach($impresoras as $impresora)
                 <tr>
                     <td>{{ $impresora->id}}</td>
                     <td>{{ $impresora->mac}}</td>
                     <td>{{ $impresora->marca}}</td>
                     <td>{{ $impresora->modelo}}</td>
                     <td>{{$impresora->created_at->format('d-M-Y h a') }}</td>
                     <td>{{ $impresora->user_registro->present()->nombreCompleto()}}</td>
                                        
                     
                     <td>
                          <a href="{{ route('impresoras.show', $impresora) }}" title="Ver" class="btn btn-xs btn-default"><i class="fa fa-eye"></i></a>
                          <a href="{{ route('impresoras.edit', $impresora) }}" title="Editar" class="btn btn-xs btn-info"><i class="fa fa-pencil"></i></a>
                          <button type="button" title="Eliminar" class="btn btn-xs btn-danger" onclick="eliminar_impresora({{ $impresora->id}});"><i class="fa fa-remove"></i></button>
                     <td>
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

@endsection
@section('scripts')
    <script>
            function eliminar_impresora(id) {  
                var form = $('#delete');
                var url=App.host +"/impresoras/"+id;

                swal({
                    title: "¡Eliminar Impresora!",
                    text: "¿Esta seguro de que deseas eliminar la imrpesora?",
                    type: "input",
                    showCancelButton: true,
                    closeOnConfirm: false,
                    inputPlaceholder: "Motivo de la eliminación.", 
                    confirmButtonText: "Si, Eliminar",
                    cancelButtonText: "No, Cancelar",
                    showLoaderOnConfirm: true                
                },function(inputValue){    
                    if (inputValue === false) return false;
                    if (inputValue === "") {
                        swal.showInputError("Escriba el motivo de la eliminación!"); 
                        return false 
                    }
                    form.attr("action",url);
                    $("input[name=motivo]").val(inputValue);
                    $('#delete').submit();
                });
            }
    </script>
@endsection