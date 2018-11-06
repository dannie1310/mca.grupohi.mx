@extends('layout')

@section('content')
    <div class='success'></div>
    <h1>{{ strtoupper(trans('strings.tarifas_ruta_material')) }} </h1>
    {!! Breadcrumbs::render('tarifas_ruta_material.create') !!}
    <hr>
    @include('partials.errors')
    {!! Form::open(['route' => 'tarifas_ruta_material.store', 'method' => 'POST', 'id' => 'tarifa_ruta_material_create_form']) !!}
    <div class="form-group">
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <form>
                            <label for="id_ruta">RUTA</label>
                            <select class="form-control" name="id_ruta">
                                <option selected ="selected" value>--SELECCIONE--</option>
                                @foreach($rutas as $ruta)
                                    <option value="{{$ruta->IdRuta}}">{{$ruta->Clave}}{{$ruta->IdRuta}} ({{$ruta->origen->Descripcion}} - {{$ruta->tiro->Descripcion}})</option>
                                @endforeach
                            </select>
                    </form>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label for="id_material">MATERIAL</label>
                    {!! Form::select('id_material', $materiales, null, ['placeholder' => '--SELECCIONE--', 'class' => 'form-control']) !!}
                </div>
            </div>
        </div>
        <br>
        <div class="row">
            <div class="col-md-4">
                <label for="primer_km">TARIFA PRIMER KM</label>
                {!! Form::text('primer_km', null, ['class' => 'form-control', 'placeholder' => '0']) !!}
            </div>
            <div class="col-md-4">
                <label for="km_subsecuente">TARIFA KM SUBSECUENTES</label>
                {!! Form::text('km_subsecuente', null, ['class' => 'form-control', 'placeholder' => '0']) !!}
            </div>
            <div class="col-md-4">
                <label for="km_adicional">TARIFA KM ADICIONALES</label>
                {!! Form::text('km_adicional', null, ['class' => 'form-control', 'placeholder' => '0']) !!}
            </div>
        </div>
        <br>
        <div class="row">
            <div class="col-md-6">
                <label for="inicio_vigencia">INICIO VIGENCIA</label>
                {!! Form::date('inicio_vigencia', date("Y-m-d"), ['class' => 'form-control', 'placeholder' => '0']) !!}
            </div>
            <div class="col-md-6">
                <label for="idtipo_tarifa">TIPO DE TARIFA</label>
                {!! Form::select('idtipo_tarifa', $tipos, null, ['placeholder' => '--SELECCIONE--', 'class' => 'form-control']) !!}
            </div>
        </div>
        <br>
        <div class="form-group col-md-12" style="text-align: center; margin-top: 20px">
            {!! Form::submit('Guardar', ['class' => 'btn btn-success','id'=>'tarifa_store']) !!}
        </div>
    </div>
    {!! Form::close() !!}
@stop

@section('scripts')
    <script>
        $(document).ready(function(){
            $('#tarifa_store').off().on('click', function (e) {
                e.preventDefault();
                var form = $('#tarifa_ruta_material_create_form');
                form.submit();
            })
        });
    </script>
@endsection