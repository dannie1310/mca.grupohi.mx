Vue.component('conciliaciones-edit', {
    data: function() {
        return {
            'tipo'         : '',
            'resultados'   : [],
            'conciliacion' : {
                'id'       : '',
                'detalles' : [],
                'detalles_nc' : []
            },
            'form' : {
                'errors' : [],
                'costos' : [],
                'id_costo' : '',
                'usuario': '',
                'clave': ''
            },
            'guardando'  : false,
            'fetching'   : false,
            'fecha_cambio' : '',
            'api' : {
                'url_api': 'http://sao.grupohi.mx',   /// 'http://localhost:8000'   ////'http://sao.grupohi.mx'
                'token' : ''
            }

        }
    },

    props:[
        'user', 'database_name', 'id_obra'
    ],

    directives: {
        datepicker: {
            inserted: function (el) {
                $(el).datepicker({
                    format: 'yyyy-mm-dd',
                    language: 'es',
                    autoclose: true,
                    clearBtn: true,
                    todayHighlight: true,
                    endDate: '0d'
                });
                $(el).val(App.timeStamp(1));
            }
        },

        datepickerconciliacion: {
            inserted: function(el) {
                $(el).datepicker({
                    format: 'yyyy-mm-dd',
                    language: 'es',
                    autoclose: true,
                    clearBtn: true,
                    todayHighlight: true,
                    endDate: '0d'
                });
            }
        },

        fileinput: {
            inserted: function (el) {
                $(el).fileinput({
                    language: 'es',
                    theme: 'fa',
                    showPreview: false,
                    showUpload: true,
                    uploadAsync: true,
                    maxFileConut: 1,
                    autoReplate: true,
                    allowedFileExtensions: ['xls', 'xml', 'csv', 'xlsx'],
                    layoutTemplates: {
                        actionUpload: '',
                        actionDelete: '',
                    }
                });
            }
        }
    },

    created: function () {
        this.fetchConciliacion();
    },

    computed: {
        cancelados: function() {
            var _this = this;
            return _this.conciliacion.detalles.filter(function(detalle) {
                if(detalle.estado === -1) {
                    return true;
                }
                return false;
            });
        },

        conciliados: function () {
            var _this = this;
            return _this.conciliacion.detalles.filter(function (detalle) {
                if(detalle.estado === 1) {
                    return true;
                }
                return false;
            });
        },

        manuales: function() {
            var _this = this;
            return _this.conciliacion.detalles.filter(function (detalle) {
                return ((detalle.estatus_viaje >= 20&& detalle.estatus_viaje <= 29) && detalle.estado === 1);
            });
        },

        moviles: function() {
            var _this = this;
            return _this.conciliacion.detalles.filter(function (detalle) {
                return ((detalle.estatus_viaje >= 0&& detalle.estatus_viaje <= 9) && detalle.estado === 1);
            });
        }
    },

    methods: {

        fetchConciliacion: function() {
            this.fetching = true;
            var _this = this;
            var url = $('#id_conciliacion').val();
            console.log(url);
            this.$http.get(url).then(response => {
                _this.conciliacion = response.body.conciliacion;
                console.log(response.body.conciliacion);
                this.fetching = false;
                _this.fecha_cambio = _this.conciliacion.fecha;
            }, error => {
                this.fetching = false;
                App.setErrorsOnForm(_this.form, error.body);
            });
        },

        confirmarRegistro: function(e) {
            e.preventDefault();

            swal({
                title: "¿Desea continuar con la conciliación?",
                text: "¿Esta seguro de que la información es correcta?",
                type: "warning",
                showCancelButton: true,
                confirmButtonText: "Si",
                cancelButtonText: "No",
                confirmButtonColor: "#ec6c62"
            }, () => this.registrar() );
        },

        cancelar: function(e) {
            e.preventDefault();
            var _this = this;
            var url = $(e.target).attr('href');
            swal({
                title: "¡Cancelar Conciliación!",
                text: "¿Esta seguro de que deseas cancelar la conciliación?",
                type: "input",
                showCancelButton: true,
                closeOnConfirm: false,
                inputPlaceholder: "Motivo de la cancelación.",
                confirmButtonText: "Si, Cancelar",
                cancelButtonText: "No",
                showLoaderOnConfirm: true

            },
            function(inputValue){
                if (inputValue === false) return false;
                if (inputValue === "") {
                    swal.showInputError("Escriba el motivo de la cancelación!");
                    return false
                }
                $.ajax({
                    url: url,
                    type : 'POST',
                    data : {
                        _method : 'DELETE',
                        motivo : inputValue
                    },
                    success: function(response) {
                        if(response.status_code = 200) {
                            swal({
                                type: 'success',
                                title: '¡Hecho!',
                                text: 'Conciliación cancelada correctamente',
                                showCancelButton: false,
                                confirmButtonText: 'OK',
                                closeOnConfirm: true
                            },
                            function () {
                                _this.fetchConciliacion();
                            });
                        }
                    },
                    error: function (error) {
                        swal({
                            type: 'error',
                            title: '¡Error!',
                            text: App.errorsToString(error.responseText)
                        });
                        _this.fetchConciliacion();
                    }
                });
            });
        },

        cerrar: function(e) {
            e.preventDefault();

            var _this = this;
            var url = App.host + '/conciliaciones/' + _this.conciliacion.id;

            if(! this.conciliados.length) {
                swal({
                    type: 'warning',
                    title: "¡Cerrar Conciliación!",
                    text: 'No se puede cerrar la conciliación ya que no tiene viajes conciliados',
                    closeOnConfirm: true,
                    showCancelButton: false,
                    confirmButtonText: "OK"
                });

            } else {
                swal({
                        title: "¡Cerrar Conciliación!",
                        text: "¿Desea cerrar la conciliación?",
                        type: "info",
                        showCancelButton: true,
                        closeOnConfirm: false,
                        confirmButtonText: "Si, Cerrar",
                        cancelButtonText: "No",
                        showLoaderOnConfirm: true
                    },
                    function () {
                        $.ajax({
                            url: url,
                            type: 'POST',
                            data: {
                                _method: 'PATCH',
                                action: 'cerrar'
                            },
                            success: function (response) {
                                if (response.status_code = 200) {
                                    swal({
                                            type: 'success',
                                            title: '¡Hecho!',
                                            text: 'Conciliación cerrada correctamente',
                                            showCancelButton: false,
                                            confirmButtonText: 'OK',
                                            closeOnConfirm: true
                                        },
                                        function () {
                                            _this.fetchConciliacion();
                                        });
                                }
                            },
                            error: function (error) {
                                swal({
                                    type: 'error',
                                    title: '¡Error!',
                                    text: 'Panda '+App.errorsToString(error.responseText)
                                });
                                _this.fetchConciliacion();
                            }
                        });
                    });
            }
        },

        aprobar: function () {
            $('#sesionSAO').modal('show');
        },

        getToken: function (e) {
            var _this = this;

            var url = _this.api.url_api + '/api/auth';
            this.guardando = true;
            $.ajax({
                url:url,
                type: 'POST',
                headers:{
                    usuario: _this.user.usuario,
                    clave: _this.form.clave
                },
                success: function (response) {
                    _this.form.clave = "";
                   _this.api.token = response.token;
                   _this.getCostos(e);
                },
                error: function (error) {
                    _this.form.clave = "";
                    $('#sesionSAO').modal('hide');
                    _this.guardando = false;
                    swal({
                        type: 'error',
                        title: '¡Error!',
                        text: 'No se Puede Iniciar Sesión '
                    });
                }
            })
        },

        getCostos: function(e){
            e.preventDefault();
            var _this = this;
            var url = _this.api.url_api + '/api/conciliacion/costos';
            this.guardando = true;
            $.ajax({
                url: url,
                type: 'GET',
                headers:{
                    database_name: _this.database_name,
                    id_obra: _this.id_obra,
                    Authorization: 'Bearer '+_this.api.token
                },
                data:{
                    rfc: _this.conciliacion.rfc,
                    id_empresa: _this.conciliacion.id_empresa,
                    id_sindicato: _this.conciliacion.id_sindicato,
                    id_tarifa: _this.conciliacion.detalles[0].id_tarifa
                },
                success: function (response) {
                    if(response.length === 0){
                        _this.conciliar(e);
                    }else {
                        $('#sesionSAO').modal('hide');
                        _this.form.clave = "";
                        _this.guardando = false;
                        _this.form.costos = response;
                        $('#tipo_gasto').modal('show');
                    }
                },
                error: function (error) {
                    _this.form.clave = "";
                    _this.guardando = false;
                    $('#sesionSAO').modal('hide');
                    swal({
                        type: 'error',
                        title: '¡Error!',
                        text: 'No se Puede Recuperar los Costos '
                    });

                }
            });
        },

        conciliar: function(e){
            e.preventDefault();
            var _this = this;
            var url = App.host + '/api/conciliar';
            this.guardando = true;
            $.ajax({
                url: url,
                type: 'GET',
                data:{
                    id_conciliacion: _this.conciliacion.id,
                    id_costo: _this.form.id_costo,
                    cumplimiento: _this.conciliacion.f_inicial,
                    vencimiento: _this.conciliacion.f_final,
                    sindicato: _this.conciliacion.sindicato
                },
                success: function (response) {
                    _this.form.id_costo = "";
                    _this.enviarConciliacion(response);
                },error: function(xhr, status, error) {
                    _this.form.id_costo = "";
                    _this.guardando = false;
                    $('#sesionSAO').modal('hide');
                    $('#tipo_gasto').modal('hide');
                    var err = eval("(" + xhr.responseText + ")");
                    swal({
                        type: 'error',
                        title: '¡Error!',
                        text: 'Error al generar la Conciliación:\n' + err.message
                    });
                }
            });
        },
        enviarConciliacion: function (data) {
            var _this = this;
            var mensaje = 'Error al generar la Conciliación ';
            var url = _this.api.url_api + '/api/conciliacion';
            $.ajax({
                url:url,
                type: 'POST',
                headers:{
                    database_name: _this.database_name,
                    id_obra: _this.id_obra,
                    Authorization: 'Bearer '+_this.api.token
                },
                data: data,
                success: function (response) {
                    _this.registrar_conciliacion(response.id_transaccion);
                    _this.aprobar1();
                    swal({
                            type: 'success',
                            title: '¡Hecho!',
                        text: 'Conciliacion Registrada Correctamente \nNo. de Folio de Estimación : ' + response.numero_folio,
                        showCancelButton: false,
                        confirmButtonText: 'OK',
                        closeOnConfirm: true
                        });
                        _this.guardando = false;
                        $('#sesionSAO').modal('hide');
                        $('#tipo_gasto').modal('hide');
                },error: function(xhr, status, error) {
                    if(!xhr.statusText === 'error'){
                        var err = eval("(" + xhr.responseText + ")");
                        mensaje += ' : \n' + err;
                    }

                    swal({
                        type: 'error',
                        title: '¡Error!',
                        text: mensaje
                    }, function () {
                        _this.guardando = false;
                        $('#sesionSAO').modal('hide');
                        $('#tipo_gasto').modal('hide');
                    });
                }
            });
        },

        registrar_conciliacion: function (estimacion) {
            var _this = this;
            var url = App.host + '/api/conciliar/estimacion';
            $.ajax({
                url:url,
                type: 'POST',
                data:{
                    id_conciliacion: _this.conciliacion.id,
                    id_estimacion: estimacion
                },
                success: function (response) {
                },
                error: function (error) {
                    swal({
                        type: 'error',
                        title: '¡Error!',
                        text: 'Error al Registrar la Estimación Generada\n'
                    });
                }
            });
        },

        aprobar1: function() {
            var _this = this;
                var url = App.host + '/conciliaciones/' + _this.conciliacion.id;
            $.ajax({
                url: url,
                type : 'POST',
                data : {
                    _method : 'PATCH',
                    action : 'aprobar'
                },
                success: function(response) {
                    _this.fetchConciliacion();
                },
                error: function (error) {
                    swal({
                        type: 'error',
                        title: '¡Error!',
                        text: App.errorsToString(error.responseText)
                    });
                    _this.fetchConciliacion();
                }
            });
        },

        revertir_aprovacion:function () {
            var _this = this;
            var url = App.host + '/conciliaciones/' + _this.conciliacion.id;
            $.ajax({
                url: url,
                type : 'POST',
                data : {
                    _method : 'PATCH',
                    action : 'revertir'
                },
                success: function(response) {
                    _this.form.clave = "";
                    _this.guardando = false;
                    $('#sesionSAO').modal('hide');
                    $('#tipo_gasto').modal('hide');
                },
                error: function (error) {
                    _this.form.clave = "";
                    _this.guardando = false;
                    $('#sesionSAO').modal('hide');
                    $('#tipo_gasto').modal('hide');

                }
            });
        },

        sesion_estimacion: function (e) {
            e.preventDefault();
            $('#revertir_estimacion').modal('show');
        },

        token_revertir: function () {
            var _this = this;
            this.guardando = true;
            var url = _this.api.url_api + '/api/auth';
            $.ajax({
                url:url,
                type: 'POST',
                headers:{
                    usuario: _this.user.usuario,
                    clave: _this.form.clave
                },
                success: function (response) {
                    _this.form.clave = "";
                    _this.api.token = response.token;
                    _this.revertir();

                },
                error: function (error) {
                    _this.form.clave = "";
                    _this.guardando = false;
                    $('#revertir_estimacion').modal('hide');
                    swal({
                        type: 'error',
                        title: '¡Error!',
                        text: 'No se Puede Iniciar Sesión '

                    });
                }
            })
        },

        revertir: function () {
            var _this = this;
            var url = _this.api.url_api + '/api/conciliacion/' + _this.conciliacion.id;
            $.ajax({
                url:url,
                type: 'DELETE',
                headers:{
                    database_name: _this.database_name,
                    id_obra: _this.id_obra,
                    Authorization: 'Bearer '+_this.api.token
                },
                success: function (response) {
                    _this.conciliacion_revertir();
                },error: function(xhr, status, error) {
                    var mensaje = "";
                    _this.guardando = false;
                    $('#revertir_estimacion').modal('hide');
                    var err = eval("(" + xhr.responseText + ")");
                    err = err.message + '';
                    console.log(err);
                    var res = err.split(":");
                    console.log(typeof res[0], res[0]);
                    if (res[0] === "1") {
                        mensaje = "No se puede revertir la Conciliación porque la Estimación asociada con folio: " + res[1] + " a sido aprobada "
                    } else if(res[0] === "2") {
                        mensaje = res[1];
                    }
                    else
                    {
                        mensaje = '<table class="table table-striped">'
                            + '<div class="form-group"><div class="row"><div class="col-md-12">'
                            + '<label><h4>Error al Revertir la Conciliación</h4></label>'
                            + '<label><h4>La Estimación tiene asociadas las siguientes transacciones</h4></label>'
                            + '</div></div></div>'
                            + '<thead><tr><th align="right">Tipo</th><th align="center">Folio</th></tr></thead>'
                            + '<tbody>';
                        for (var i = 0; i < res.length; i++) {
                            mensaje += '<tr><td align="left">' + res[i] + '</td><td align="left">' + res[i + 1] + '</td></tr>';
                            i = i + 1;
                        }
                        mensaje += '</tbody></table>';
                    }

                    swal({
                        html: true,
                        type: 'error',
                        title: '¡Error!',
                        text: mensaje
                    });
                }
            })
        },
        conciliacion_revertir: function() {
            var _this = this;
            var url = App.host + '/conciliaciones/' + _this.conciliacion.id;
            $.ajax({
                url: url,
                type : 'POST',
                data : {
                    _method : 'PATCH',
                    action : 'revertir'
                },
                success: function(response) {
                    if(response.status_code = 200) {
                        swal({
                                type: 'success',
                                title: '¡Hecho!',
                                text: 'La conciliación se ha revertido correctamente.',
                                showCancelButton: false,
                                confirmButtonText: 'OK',
                                closeOnConfirm: true
                            },
                            function () {
                                _this.guardando = false;
                                $('#revertir_estimacion').modal('hide');
                                _this.fetchConciliacion();
                            });
                    }
                },
                error: function (error) {
                    swal({
                        type: 'error',
                        title: '¡Error!',
                        text: App.errorsToString(error.responseText)
                    });
                    _this.guardando = false;
                    $('#revertir_estimacion').modal('hide');
                    _this.fetchConciliacion();
                }
            });
        },
        registrar: function() {
            var _this = this;
            this.form.errors = [];
            this.guardando = true;

            var url = $('.form_registrar').attr('action');
            var data = $('.form_registrar').serialize();

            $.ajax({
                url: url,
                type: "POST",
                data: data,
                success: function (response)
                {
                    _this.guardando = false;
                    $('#resultados').modal('hide');
                    _this.resultados = [];

                    swal({
                        type: 'success',
                        title: '¡Viajes Conciliados Correctamente!',
                        text: response.registros + ' Viajes conciliados',
                        showConfirmButton: true
                    });

                    _this.fetchConciliacion();
                },
                error: function (error) {
                    swal({
                        type: 'error',
                        title: '¡Error!',
                        text: App.errorsToString(error.responseText)
                    });
                    _this.fetchConciliacion();
                }
            });
        },

        agregar: function(e) {
            e.preventDefault();

            this.form.errors = [];
            var _this = this;
            var url = $('.form_buscar').attr('action');
            var data = $('.form_buscar').serialize();
            this.guardando = true;


            $.ajax({
                url  : url,
                data : data,
                type : 'POST',
                success: function (response) {
                    if(response.status_code == 201){
                        if(response.detalles != null) {
                            _this.conciliacion.detalles.push(response.detalles);

                            _this.guardando = false;
                            swal({
                                type: 'success',
                                title: '¡Viaje Conciliado Correctamente!',
                                text: response.registros + ' Viajes conciliados',
                                showConfirmButton: false,
                                timer: 500
                            });
                            _this.conciliacion.importe = response.importe;
                            _this.conciliacion.volumen = response.volumen;
                            _this.conciliacion.num_viajes += 1;
                            _this.conciliacion.rango = response.rango;
                            _this.conciliacion.importe_viajes_manuales = response.importe_viajes_manuales;
                            _this.conciliacion.volumen_viajes_manuales = response.volumen_viajes_manuales;
                            _this.conciliacion.porcentaje_importe_viajes_manuales = response.porcentaje_importe_viajes_manuales;
                            _this.conciliacion.porcentaje_volumen_viajes_manuales = response.porcentaje_volumen_viajes_manuales;
                            _this.conciliacion.volumen_viajes_moviles = response.volumen_viajes_moviles;
                            _this.conciliacion.importe_viajes_moviles = response.importe_viajes_moviles;

                            $('.ticket').val('');
                            $('.ticket').focus();

                        } else {
                            _this.guardando = false;
                            swal({
                                type: 'warning',
                                title: '¡Error!',
                                text: response.msg,
                                showConfirmButton: true,
                                timer: 1500
                            });

                            $('.ticket').val('');
                            $('.ticket').focus();
                        }
                    }else if(response.status_code == 500){
                        _this.conciliacion.detalles_nc.push(response.detalles_nc);
                        _this.guardando = false
                        $('.ticket').val('');
                        $('.ticket').focus();

                        swal({
                            type: 'error',
                            title: '¡Error!',
                            text: response.detalles_nc.detalle_alert,
                            html: true
                        });
                    }
                },
                error: function (error) {
                    _this.guardando = false
                    $('.ticket').val('');
                    $('.ticket').focus();
                    
                    swal({
                        type: 'error',
                        title: '¡Error!',
                        text: App.errorsToString(error.responseText)
                    });
                }
            })
        },

        buscar: function(e) {
            e.preventDefault();

            var _this = this;
            this.form.errors = [];
            this.guardando = true;

            var data = $('.form_buscar').serialize();
            this.$http.get(App.host + '/viajes?tipo=conciliar&' + data).then((response) => {
                _this.resultados = response.body.data;
                if(_this.resultados.length) {
                    _this.guardando = false;
                    $('#resultados').modal('show');
                } else {
                    _this.guardando = false;
                    swal({
                        type: 'warning',
                        title: '¡Sin Resultados!',
                        text: 'Ningún viaje coincide con los datos de consulta',
                        showConfirmButton: true
                    });
                }
            }, (error) => {
                _this.guardando = false;
                App.setErrorsOnForm(this.form, error.body);

            });
        },

        cambiar_cubicacion: function (detalle) {

            var _this = this;
            swal({
                title: "¡Cambiar Cubicación!",
                text: "Cubicación Actual : " + detalle.cubicacion_camion,
                type: "input",
                showCancelButton: true,
                closeOnConfirm: false,
                inputPlaceholder: "Nueva Cubicación.",
                confirmButtonText: "Si, Cambiar",
                cancelButtonText: "No",
                showLoaderOnConfirm: true
            },
            function(inputValue){
                if (inputValue === false) return false;
                if (inputValue === "") {
                    swal.showInputError("¡Escriba la nueva Cubicación!");
                    return false
                } if (! $.isNumeric(inputValue)) {
                    swal.showInputError("¡Por favor introduzca sólo números!");
                    return false;
                }
                $.ajax({
                    url: App.host + '/viajes/' + detalle.id ,
                    type : 'POST',
                    data : {
                        _method : 'PATCH',
                        cubicacion : inputValue,
                        tipo : 'cubicacion',
                        id_conciliacion: _this.conciliacion.id
                    },
                    success: function(response) {
                        if(response.status_code = 200) {
                            _this.fetchConciliacion();
                            swal({
                                type: 'success',
                                title: '¡Hecho!',
                                text: 'Cubicacion cambiada correctamente',
                                showCancelButton: false,
                                confirmButtonText: '' +
                                'OK',
                                closeOnConfirm: true
                            });
                        }
                    },
                    error: function (error) {
                        swal({
                            type: 'error',
                            title: '¡Error!',
                            text: App.errorsToString(error.responseText)
                        });
                        _this.fetchConciliacion();
                    }
                });
            });
        },

        eliminar_detalle: function (idconciliacion_detalle) {
            var _this = this;
            var url = App.host + '/conciliacion/' + this.conciliacion.id + '/detalles/' + idconciliacion_detalle;
            swal({
                title: "¡Cancelar viaje de la Conciliación!",
                text: "¿Esta seguro de que deseas quitar el viaje de la conciliación?",
                type: "input",
                showCancelButton: true,
                closeOnConfirm: false,
                inputPlaceholder: "Motivo de la cancelación.",
                confirmButtonText: "Si, Quitar",
                cancelButtonText: "No",
                showLoaderOnConfirm: true

            },
            function(inputValue){
                if (inputValue === false) return false;
                if (inputValue === "") {
                    swal.showInputError("Escriba el motivo de la cancelación!");
                    return false
                }
                _this.guardando = true;
                _this.$http.post(url, {_method : 'DELETE', motivo : inputValue}).then((response) => {
                    if(response.body.status_code == 200) {
                        _this.guardando = false;
                        _this.fetchConciliacion();
                        swal({
                            type: 'success',
                            title: '¡Hecho!',
                            text: 'Viaje cancelado correctamente',
                            showCancelButton: false,
                            confirmButtonText: 'OK',
                            closeOnConfirm: true
                        });
                    }
                }, (error) => {
                    _this.guardando = false;
                    swal({
                        type: 'error',
                        title: '¡Error!',
                        text: App.errorsToString(error.body)
                    });
                    _this.fetchConciliacion();
                });
            });
        },

        modificar_detalles: function () {
            var _this = this;
            var url = $('.form_update').attr('action');
            var data = $('.form_update').serialize();

            $.ajax({
                url: url,
                data: data,
                type: 'POST',
                beforeSend: function () {
                    _this.guardando = true;
                },
                success: function (response) {
                    swal('¡Hecho!', 'Datos actualizados correctamente', 'success');
                    _this.conciliacion.importe_pagado = response.importe_pagado;
                    _this.conciliacion.importe_pagado_sf = response.importe_pagado_sf;
                    _this.conciliacion.volumen_pagado = response.volumen_pagado;
                    _this.conciliacion.volumen_pagado_sf = response.volumen_pagado_sf;
                },
                error:function(error) {
                    if(error.status == 422) {
                        swal({
                            type: 'error',
                            title: '¡Error!',
                            text: App.errorsToString(error.responseJSON)
                        });
                    } else if(error.status == 500) {
                        swal({
                            type: 'error',
                            title: '¡Error!',
                            text: App.errorsToString(error.responseText)
                        });
                        _this.fetchConciliacion();
                    }
                },
                complete: function () {
                    _this.guardando = false;
                    $('#detalles_conciliacion').modal('hide');
                }
            });
        },
        
        verificar_revertible: function () {
            
        }
    }
});
