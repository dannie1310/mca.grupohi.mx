@extends('layout')
@section('content')

    <h1>SOLICITUDES ACTUALIZACIÓN</h1>
    {!! Breadcrumbs::render('solicitud-actualizacion.index') !!}
    <hr>
     <h3>BUSCAR SOLICITUDES</h3>
    {!! Form::open(['id' => 'solicitud_actualizacion_form','class' => 'solicitud_actualizacion_form']) !!}
    <input type="text" class="busca10" value="0" hidden>
    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label>FECHA INICIAL (*)</label>
                <input type="text" name="FechaInicial" class="form-control fecha_hoy">
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label>FECHA FINAL (*)</label>
                <input type="text" name="FechaFinal" class="form-control fecha_hoy">
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-3">
            <div class="form-group">
                <label>TIPO DE SOLICITUDES (*)</label>
                <select name="estatus" class="form-control estatus">
                        <option value="">Todos</option>
                        <option value="0">Pendientes</option>
                        <option value="1">Procesadas</option>
                        <option value="-1">Canceladas</option>
                </select>
            </div>
        </div>
    </div>

    <div class="form-group">
        <button class="btn btn-primary buscar" type="submit" ><i class="fa fa-search"></i> Buscar </button>
    </div>

    <p class="small">Los campos <strong>(*)</strong> son obligatorios.</p>
    {!! Form::close() !!}

    <hr>
    <div class="nota"></div>
    <div class="informacion" hidden>

        <h3>
            RESULTADOS DE LA BÚSQUEDA
        </h3>

        <div class="table-responsive">
            <table class="table table-hover table-bordered table-info small" id='ListaCamiones' >
                <thead class="thead-inverse">
                <tr>
                    <th style="text-align: center"> # </th>
                    <th style="text-align: center"> Economico </th>
                    <th style="text-align: center"> Propietario </th>
                    <th style="text-align: center"> Operador </th>
                    <th style="text-align: center"> Cubicación Real </th>
                    <th style="text-align: center"> Cubicación para Pago </th>
                    <th style="text-align: center"> Fecha de Registro  </th>
                    <th style="text-align: center"> Estatus </th>
                </tr>
                </thead>
                <tbody>

                </tbody>
            </table>
        </div>

@endsection

@section('scripts')
    <script>
        $(document).ready(function() {
            $('.fecha_hoy').datepicker({
                format: 'yyyy-mm-dd',
                language: 'es',
                autoclose: true,
                clearBtn: true,
                todayHighlight: true,
                endDate: '0d'
            });

            $('.fecha_hoy').val(App.timeStamp(1));

            $('.viajes').select2({
                placeholder: "--SELECCIONE--",
                closeOnSelect: false
            });


            $('.buscar').off().on('click',function (e) {
                e.preventDefault();

                var data = $('.solicitud_actualizacion_form').serialize();
                if($('.busca10').val() == 0){
                    var url = App.host + '/solicitud-actualizacion?type=busca10';
                    $('.busca10').val(1)
                }
                else{
                    var url = App.host + '/solicitud-actualizacion?type=buscar';
                }
               //alert(url);

                $.ajax({
                    type : 'GET',
                    url  : url,
                    data : data,
                    success:function (response) {
                         console.log(response);
                        tabla = '';
                        if(response.solicitudes.length) {
                            for (x = 0; x < response.solicitudes.length; x++) {
                                //console.log(x);     response.solicitudes[x].IdSolicitudActualizacion
                                y = x + 1;
                                switch (response.solicitudes[x].Estatus) {
                                    case -1:
                                        estatus = 'Cancelado';
                                        break;
                                    case 0:
                                        estatus = 'Pendiente';
                                        break;
                                    case 1:
                                        estatus = 'Procesada'
                                        break;
                                }
                                //console.log(response.solicitudes[x].Economico);
                                tabla = tabla + '<tr style="text-align: center"><td valign="bottom">' + y + '</td>';
                                tabla = tabla + '<td valign="bottom"><a href="{{ route('solicitud-actualizacion.show','')}}/' + response.solicitudes[x].IdSolicitudActualizacion + '  ">' + response.solicitudes[x].Economico + '</a></td>';
                                tabla = tabla + '<td valign="bottom">' + response.solicitudes[x].Propietario + '</td>';
                                tabla = tabla + '<td valign="bottom">' + response.solicitudes[x].Nombre + '</td>';
                                tabla = tabla + '<td valign="bottom">' + response.solicitudes[x].CubicacionReal + '</td>';
                                tabla = tabla + '<td valign="bottom">' + response.solicitudes[x].CubicacionParaPago + '</td>';
                                tabla = tabla + '<td valign="bottom">' + response.solicitudes[x].FechaHoraRegistro + '</td>';
                                tabla = tabla + '<td valign="bottom">' + estatus + '</td>';
                                tabla = tabla + '</tr>';

                            }
                        }
                        else{
                            x=0;
                        }
                        if(x==0){
                            $('.informacion').hide();
                            $('.nota').html('Ninguna solicitud coincide con los datos de consulta');
                        }
                        else{
                            $('.informacion').show();
                            $('.nota').html('');
                        }
                        j= $("#ListaCamiones tr").length;
                        for(y=0;y < j-1; y++ ){
                            $("#ListaCamiones tr:last").remove();
                        }
                        $('#ListaCamiones').append(tabla);
                    }
                });
            });
            $(".buscar").click();
        });
    </script>
@endsection