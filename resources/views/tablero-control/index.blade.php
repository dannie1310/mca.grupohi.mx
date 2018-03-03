@include('partials.errors')
@extends('layout')

@section('content')
    <div class='success'></div>
    <h1>TABLERO DE CONTROL</h1>
    <hr>

    <div class="errores"></div>
    <div class="table-responsive">
        <table class="table table-hover table-bordered small">
            <thead>
            <tr>
                <th>Condición de análisis</th>
                <th>Núm Total</th>
                <th>Señal</th>
                <th>Acciones</th>
            </tr>
            </thead>
            <tbody>
                 <tr>
                     <td>No validados y No conciliados</td>
                     <td>{{ $no_validados }}</td>
                     @if($no_validados > 0)
                         <td> <button type="button" class="btn btn-danger">Warning</button></td>
                     @else
                         <td> <button type="button" class="btn btn-success">Warning</button></td>
                     @endif
                     <td>
                     <a href="{{ route('tablero-detalle.show',1) }}" title="Detalle" class="btn btn-xs btn-show"><i class="fa fa-eye"></i></a>
                     </td>
                 </tr>
                 <tr>
                     <td>Validados y No conciliados</td>
                     <td>{{ $validados }}</td>
                     @if($validados > 0)
                         <td> <button type="button" class="btn btn-danger">Warning</button></td>
                     @else
                         <td> <button type="button" class="btn btn-success"> </button></td>
                     @endif
                     <td>
                         <a href="{{ route('tablero-detalle.show',2) }}" title="Detalle" class="btn btn-xs btn-show"><i class="fa fa-eye"></i></a>
                     </td>
                 </tr>
            </tbody>
        </table>
    </div>
    <form id='delete' method="post">
        <input type='hidden' name='motivo' value/>
        {{csrf_field()}}
        <input type="hidden" name="_method" value="delete"/>
    </form>
@stop{{--
@section('scripts')
    <script>
        function desactivar_tarifa(id) {
            var form = $('#delete');
            var url=App.host +"/tarifas_material/"+id;

            swal({
                    title: "¡Desactivar tarifa!",
                    text: "¿Esta seguro de que deseas desactivar la tarifa?",
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
    </script>
@endsection
@stop--}}