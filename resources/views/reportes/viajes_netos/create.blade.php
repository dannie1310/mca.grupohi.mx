@extends('layout')

@section('content')
    <h1>REPORTES</h1>
    {!! Breadcrumbs::render('reportes.viajes_netos')  !!}
    <hr>
    <h3>BUSCAR VIAJES</h3>
    @include('partials.errors')
    {!! Form::open(['method' => 'GET', 'route' => ['reportes.viajes_netos.show'], 'id' => 'form_reporte_viajes_netos']) !!}
    <input type="hidden" name="action" value />
        <div class="row">
            <div class="col-md-12">
                <div class="form-group">
                    <label>FECHA</label>
                    <input type="text" class="date start form-control"  id="fecha" name="FechaInicial" value="{{ old('FechaInicial') }}" />
                </div>
            </div>
            <div class="col-md-2 hide">
                <div class="form-group">
                    <label>HORA</label>
                    <input type="text" class="form-control" name="HoraInicial" value="00:00:00 am" />
                </div>
            </div>
        </div>
        <div class="row hide">
            <div class="col-md-6">
                <div class="form-group">
                    <label>FECHA Final</label>
                    <input type="text" class="form-control" id="fecha_f" name="FechaFinal" value="0" />
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label>HORA Final</label>
                    <input type="text" class="time end form-control" name="HoraFinal" value="11:59:59 pm" />
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