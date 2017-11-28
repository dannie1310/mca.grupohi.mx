@extends('layout')
@section('content')
    <h1>SOLICITUD DE ACTUALIZACION
        ({{$solicitud->Estatus_string}})
    </h1>
    {!! Breadcrumbs::render('solicitud-actualizacion.show', $solicitud) !!}

    <hr>

    <form id="solicitud_actualizacion" action="{{ route('solicitud-actualizacion.update', $solicitud) }}" method="POST">
        {{ csrf_field() }}
        <input type="hidden" name="_method" value="PATCH">

    <table align="center" class="wy-table-bordered">
        <thead>
        <tr align="center">
            <td></td>
            <td>Información Camión</td>
            <td>Datos Solicitud</td>
        </tr>
        </thead>
        <tbody>
        <tr>
            <td colspan="3"><hr></td>
        </tr>
        <tr>
            <td><label>Sindicato:</label></td>
            <td> <input type="text" class="form-control" value="{{ $camion->sindicato }}" disabled> </td>
            <td> <input type="text" class="form-control" value="{{ $solicitud->sindicato }}" disabled></td>
        </tr>
        <tr>
            <td><label>Empresa:</label></td>
            <td><input type="text" class="form-control" value="{{ $camion->empresa }}" disabled></td>
            <td><input type="text" class="form-control" value="{{ $solicitud->empresa }}" disabled></td>
        </tr>
        <tr>
            <td><label>Propietario:</label></td>
            <td><input type="text" class="form-control" value="{{ $camion->Propietario  }}" disabled></td>
            <td><input type="text" class="form-control" value="{{ $solicitud->Propietario  }}" disabled></td>
        </tr>
        <tr>
            <td colspan="3"><hr></td>
        </tr>
        <tr>
            <td><label>Operador:</label></td>
            <td><input type="text" class="form-control" value="{{ $camion->operador }}" disabled></td>
            <td><input type="text" class="form-control" value="{{ $solicitud->operador  }}" disabled></td>
        </tr>
        <tr>
            <td><label>No Licnecia:</label></td>
            <td><input type="text" class="form-control" value="{{ $camion->operador ? $camion->operador->NoLicencia:'' }}" disabled></td>
            <td><input type="text" class="form-control" value="{{ $solicitud->operador ? $solicitud->operador->NoLicencia:''  }}" disabled></td>
        </tr>
        <tr>
            <td><label>Vigencia:</label></td>
            <td><input type="text" class="form-control" value="{{ $camion->operador ? $camion->operador->VigenciaLicencia:''  }}" disabled></td>
            <td><input type="text" class="form-control" value="{{ $solicitud->operador ? $solicitud->operador->VigenciaLicencia:''  }}" disabled></td>
        </tr>
        <tr>
            <td colspan="3"><hr></td>
        </tr>
        <tr>
            <td><label>No Economico:</label></td>
            <td><input type="text" class="form-control" value="{{ $camion->Economico  }}" disabled></td>
            <td><input type="text" class="form-control" value="{{ $solicitud->Economico  }}" disabled></td>
        </tr>
        <tr>
            <td><label>Placas Camión:</label></td>
            <td><input type="text" class="form-control" value="{{ $camion->Placas }}" disabled></td>
            <td><input type="text" class="form-control" value="{{ $solicitud->Placas }}" disabled></td>
        </tr>
        <tr>
            <td><label>Placas Caja:</label></td>
            <td><input type="text" class="form-control" value="{{ $camion->PlacasCaja }}" disabled></td>
            <td><input type="text" class="form-control" value="{{ $solicitud->PlacasCaja }}" disabled></td>
        </tr>
        <tr>
            <td><label>Marca:</label></td>
            <td><input type="text" class="form-control" value="{{ $camion->marca }}" disabled></td>
            <td><input type="text" class="form-control" value="{{ $solicitud->marca }}" disabled></td>
        </tr>
        <tr>
            <td><label>Modelo:</label></td>
            <td><input type="text" class="form-control" value="{{ $camion->Modelo }}" disabled></td>
            <td><input type="text" class="form-control" value="{{ $solicitud->Modelo }}" disabled></td>
        </tr>
        <tr>
            <td colspan="3"><hr></td>
        </tr>
        <tr>
            <td><label>Ancho:</label></td>
            <td><input type="text" class="form-control" value="{{ $camion->Ancho }}" disabled></td>
            <td><input type="text" class="form-control" value="{{ $solicitud->Ancho }}" disabled></td>
        </tr>
        <tr>
            <td><label>Largo:</label></td>
            <td><input type="text" class="form-control" value="{{ $camion->Largo }}" disabled></td>
            <td><input type="text" class="form-control" value="{{ $solicitud->Largo }}" disabled></td>
        </tr>
        <tr>
            <td><label>Alto:</label></td>
            <td><input type="text" class="form-control" value="{{ $camion->Alto }}" disabled></td>
            <td><input type="text" class="form-control" value="{{ $solicitud->Alto }}" disabled></td>
        </tr>
        <tr>
            <td><label>Gato:</label></td>
            <td><input type="text" class="form-control" value="{{ $camion->EspacioDeGato  }}" disabled></td>
            <td><input type="text" class="form-control" value="{{ $solicitud->Gato  }}" disabled></td>
        </tr>
        <tr>
            <td colspan="3"><hr></td>
        </tr>
        <tr>
            <td><label>Extensión:</label></td>
            <td><input type="text" class="form-control" value="{{ $camion->AlturaExtension }}" disabled></td>
            <td><input type="text" class="form-control" value="{{ $solicitud->Extension }}" disabled></td>
        </tr>
        <tr>
            <td><label>Disminución:</label></td>
            <td><input type="text" class="form-control" value="{{ $camion->Disminucion }}" disabled></td>
            <td><input type="text" class="form-control" value="{{ $solicitud->Disminucion }}" disabled></td>
        </tr>
        <tr>
            <td colspan="3"><hr></td>
        </tr>
        <tr>
            <td><label><label for="CubicacionReal">Cubicación Real:</label></label></td>
            <td><input type="text" class="form-control" value="{{ $camion->CubicacionReal }}" disabled></td>
            <td><input type="text" class="form-control" value="{{ $solicitud->CubicacionReal }}" disabled></td>
        </tr>
        <tr>
            <td><label for="CubicacionPago">Cubicación Pago:</label></td>
            <td><input type="text" class="form-control" value="{{ $camion->CubicacionParaPago }}" disabled></td>
            <td><input type="text" class="form-control" value="{{ $solicitud->CubicacionParaPago }}" disabled></td>
            @if($solicitud->CubicacionParaPago > 40 )
            <tr>
                <td></td>
                <td><p class="bg-danger">La Cubicación supera el máximo permitido - </p></td>
                <td><p class="bg-danger">No se podra actualizar estos datos.</p></td>
            </tr>
            @endif
        </tr>
        <tr>
            <td colspan="3"><hr></td>
        </tr>
        <tr>
            <td><label>Fotos Camión</label></td>
            <td colspan="2" width="550" >
                <div id="carousel-example-generic" class="carousel slide" data-ride="carousel" >
                    <!-- Indicators -->
                    <ol class="carousel-indicators">
                        <li data-target="#carousel-example-generic" data-slide-to="1"></li>
                        <li data-target="#carousel-example-generic" data-slide-to="2"></li>
                        <li data-target="#carousel-example-generic" data-slide-to="3"></li>
                        <li data-target="#carousel-example-generic" data-slide-to="4"></li>
                    </ol>
                    <!-- Wrapper for slides -->


                    <div class="carousel-inner" role="listbox">
                        <?php $x = 0; ?>
                        @foreach($solicitud->solicitudImagenes as $imagen)
                            <div class="item <?php if($x == 0) { echo 'active'; $x=$x+1;} ?>">
                                <img src="{{ 'data:image/png;base64,'.$imagen->Imagen }}" alt="...">
                                <div class="carousel-caption">
                                    {{ $imagen->TipoC_string }}
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <!-- Controls -->
                    <a class="left carousel-control" href="#carousel-example-generic" role="button" data-slide="prev">
                        <span class="glyphicon glyphicon-chevron-left" aria-hidden="true"></span>
                        <span class="sr-only">Previous</span>
                    </a>
                    <a class="right carousel-control" href="#carousel-example-generic" role="button" data-slide="next">
                        <span class="glyphicon glyphicon-chevron-right" aria-hidden="true"></span>
                        <span class="sr-only">Next</span>
                    </a>
                </div>
            </td>
        </tr>
        <tr>
            <td colspan="3"><hr></td>
        </tr>
        <tr>
            <td><label>Fotos Solicitud</label></td>
            <td colspan="2" width="550" >
                <div id="carousel-example-generic1" class="carousel slide" data-ride="carousel">
                    <!-- Indicators -->
                    <ol class="carousel-indicators">
                        <li data-target="#carousel-example-generic" data-slide-to="1"></li>
                        <li data-target="#carousel-example-generic" data-slide-to="2"></li>
                        <li data-target="#carousel-example-generic" data-slide-to="3"></li>
                        <li data-target="#carousel-example-generic" data-slide-to="4"></li>
                    </ol>

                    <!-- Wrapper for slides -->
                    <div class="carousel-inner" role="listbox">
                        <?php $x = 0; ?>
                        @foreach($solicitud->solicitudImagenes as $imagen)
                                <div class="item <?php if($x == 0) { echo 'active'; $x=$x+1;} ?>">
                                <img src="{{ 'data:image/png;base64,'.$imagen->Imagen }}" alt="...">
                                <div class="carousel-caption">
                                    {{ $imagen->tipoc_string }}
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <!-- Controls -->
                    <a class="left carousel-control" href="#carousel-example-generic1" role="button" data-slide="prev">
                        <span class="glyphicon glyphicon-chevron-left" aria-hidden="true"></span>
                        <span class="sr-only">Previous</span>
                    </a>
                    <a class="right carousel-control" href="#carousel-example-generic1" role="button" data-slide="next">
                        <span class="glyphicon glyphicon-chevron-right" aria-hidden="true"></span>
                        <span class="sr-only">Next</span>
                    </a>
                </div>
            </td>
        </tr>
        <tr>
            <td colspan="3"><hr></td>
        </tr>
        <tr>
            <td>Observaciones</td>
            <td colspan="2"><input type="text" class="form-control MotivoRechazo" name="MotivoRechazo" value="{{ $solicitud->MotivoRechazo }}" readonly></td>
        </tr>
        <tr>
            <td colspan="3"><hr></td>
        </tr>
        </tbody>
        <tfoot>

        <tr>
            <td colspan="3" align="center">
                @if(Auth::user()->can(['aprobar-solicitud-actualizar']))
                    <div <?php if($solicitud->Estatus != 0 ){ echo 'hidden';}?>>
                        @if($solicitud->CubicacionParaPago <= 40)
                        <button class="btn btn-success reactivar">Actualizar</button>
                        @endif
                        <button class="btn btn-danger cancelar">Cancelar</button>
                    </div>

                @endif
                <a href="{{ route('solicitud-actualizacion.index') }}" class="btn btn-info pull-right">Regresar</a>
            </td>
        </tr>

        <tr>
            <td colspan="3"><hr></td>
        </tr>
        </tfoot>
    </table>
    </form>

@endsection
@section('scripts')
    <script>

        $(document).ready(function(){
            $('.reactivar').off().on('click', function (e) {

                e.preventDefault();
                var form = $('#solicitud_actualizacion');
                swal({
                        title: "Actualizar datos del Camión",
                        text: "¿Esta seguro de que desea Actualizar la Información del Camión?",
                        type: "info",
                        confirmButtonText: "Si, Actualizar",
                        cancelButtonText: "No",
                        showCancelButton: true,
                        closeOnConfirm: false,
                        showLoaderOnConfirm: true,
                    },
                    function(isConfirm){
                        if (isConfirm) {
                            form.submit();
                            swal("Actualizado!", "Camión Actualizado.", "success");
                        } else {
                            swal("Error", "La cubicacion es mayor a lo permitido", "error");
                        }
                    });


            });
            $('.cancelar').off().on('click', function (e) {
                e.preventDefault();

                var form = $('#solicitud_actualizacion');
                swal({
                        title: "Cancelar!",
                        text: "Motivo de Cancelación de la Solicitud:",
                        type: "input",
                        showCancelButton: true,
                        closeOnConfirm: false,
                        animation: "slide-from-top",
                        inputPlaceholder: "Motivo"
                    },
                    function(inputValue){
                        if (inputValue === false) return false;

                        if (inputValue === "") {


                            swal.showInputError("Escribir el motivo de la Cancelación!!");

                            return false
                        }
                        $('.MotivoRechazo').val(inputValue);
                        swal("Cancelado!", "Motivo: " + inputValue, "success");
                        form.submit();
                    });
            })

        });



    </script>
@endsection

