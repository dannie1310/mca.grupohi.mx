@extends('layout')

@section('content')
    <h1>REPORTES</h1>
    {!! Breadcrumbs::render('reportes.viajes_netos.completo')  !!}
    <hr>
    <h3>BUSCAR VIAJES</h3>
    @include('partials.errors')
    {!! Form::open(['method' => 'GET', 'route' => ['reportes.viajes_netos.completo.show'], 'id' => 'form_reporte_viajes_netos']) !!}
    <input type="hidden" name="action" value />
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label>FECHA INICIAL</label>
                    <input type="text" class="date start form-control" name="FechaInicial" value="{{ old('FechaInicial') }}" />
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label>HORA INICIAL</label>
                    <input type="text" class="time start form-control" name="HoraInicial" value="{{ old('HoraInicial') }}" />
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label>FECHA FINAL</label>
                    <input type="text" class="date end form-control" name="FechaFinal" value="{{ old('FechaFinal') }}" />
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label>HORA FINAL</label>
                    <input type="text" class="time end form-control" name="HoraFinal" value="{{ old('HoraFinal') }}" />
                </div>
            </div>
        </div>

    <div class="form-group">
        <button type="submit" class="btn btn-success excel">GENERAR REPORTE</button>
        <button type="submit" class="btn btn-primary view">VISTA PREVIA</button>
    </div>

    {!! Form::close() !!}
@stop
@section('scripts')
    <script>

        $('.fecha').keyup(function () {
            var aux = $(this).val();
            $('.fecha_f').val(aux);

        })
        $('.view').off().on('click', function (e) {
            e.preventDefault();
            $('input[name=action]').val('view');
            $('form').submit();
        });

        $('.excel').off().on('click', function (e) {
            e.preventDefault();
            $('input[name=action]').val('excel');
            $('form').submit();
        });
        // initialize input widgets first
        $('#form_reporte_viajes_netos .time').timepicker({
            'timeFormat' : 'hh:mm:ss a',
            'showDuration': true
        });

        $('#form_reporte_viajes_netos .date').datepicker({
            format: 'yyyy-mm-dd',
            language: 'es',
            autoclose: true,
            clearBtn: true,
            todayHighlight: true,
            endDate: '0d'
        });

        if(! $('#form_reporte_viajes_netos .date').val()) {
            $('#form_reporte_viajes_netos .date').val(App.timeStamp(1));
        }

        if(! $('#form_reporte_viajes_netos .time.start').val()) {
            $('#form_reporte_viajes_netos .time.start').val('12:00:00 am');
        }

        if(! $('#form_reporte_viajes_netos .time.end').val()) {
            $('#form_reporte_viajes_netos .time.end').val('11:59:59 pm');
        }
    </script>
@stop