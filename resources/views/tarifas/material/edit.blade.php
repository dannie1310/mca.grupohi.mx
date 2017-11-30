@extends('layout')

@section('content')
    <div class='success'></div>
     <h1>{{ strtoupper(trans('strings.tarifas_material')) }} </h1>
    {!! Breadcrumbs::render('tarifas_material.edit',$tarifas) !!}
     <hr>
     @include('partials.errors')

     {!! Form::model($tarifas, ['route' => ['tarifas_material.update', $tarifas], 'method' => 'PATCH', 'id' => 'tarifas_material_update_form']) !!}

    {{--<label>{{ $tarifas->material }}</label>--}}
          <div class="row">
              <div class="col-md-6">
                  <label>MATERIAL</label>
                  {!! Form::label($tarifas->material, null, ['class' => 'form-control', 'placeholder' => '0','disabled']) !!}
              </div>
              <div class="col-md-6">
                  <label>INICIO VIGENCIA</label>
                  {!! Form::label($tarifas->InicioVigencia->format("Y-d-m H:i:s"), null, ['class' => 'form-control', 'placeholder' => '0','disabled']) !!}
              </div>
              <div class="col-md-6">
                  <label>TARIFA PRIMER KM</label>
                  {!! Form::label($tarifas->PrimerKM, null, ['class' => 'form-control', 'placeholder' => '0','disabled']) !!}
              </div>
              <div class="col-md-6">
                  <label>TARIFA KM SUBSECUENTES</label>
                  {!! Form::label($tarifas->KMSubsecuente, null, ['class' => 'form-control', 'placeholder' => '0','disabled']) !!}
              </div>
              <div class="col-md-6">
                  <label>TARIFA KM ADICIONALES</label>
                  {!! Form::label($tarifas->KMAdicional, null, ['class' => 'form-control', 'placeholder' => '0','disabled']) !!}
              </div>
              <div class="col-md-6">
                  <label for="idtarifas_tipo">TIPO DE TARIFA</label>
                  {!! Form::select('idtarifas_tipo', $tipos, null, ['placeholder' => '--SELECCIONE--', 'class' => 'form-control']) !!}
              </div>
              <div class="col-md-6">
                  <label  for="idtarifa" value ={{$tarifas->IdTarifa}}></label>
              </div>

          </div>

          <div class="form-group col-md-12" style="text-align: center; margin-top: 20px">
              {!! Form::submit('Guardar', ['class' => 'btn btn-success']) !!}
          </div>
      {!! Form::close() !!}
  @stop


@section('scripts')
    <script>
        $(document).ready(function(){
            $('#tarifas_material_update').off().on('click', function (e) {
                e.preventDefault();
                var form = $('#tarifas_material_update_form');
                swal({
                    title: "Guardar Cambios",
                    text: "¿Esta seguro de que desea actualizar el tipo de tarifa?",
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonText: "Si, Actualizar",
                    cancelButtonText: "No, Cancelar",
                    cancelButtonColor: "#ec6c62",
                    confirmButtonColor: "#467028"
                }, () => form.submit());
            })
        });
    </script>
@endsection