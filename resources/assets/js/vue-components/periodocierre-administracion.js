/**
 * Created by DBENITEZ on 05/10/2017.
 */
Vue.component('periodocierre-administracion',{
    data: function () {
        return {
            usuarios: [],
            cierres_periodo: [],
            select:[],
            selected_usuario_id: '',
            selected_usuario: {},
            historico:[],
            cargando: false,
            guardando: false,
            form: {
                errors: []
            }
        }
    },
    created: function () {
        this.init();
    },
    directives: {
        datepicker: {
            inserted: function (el) {
                $(el).datepicker({
                    format: 'yyyy-mm-dd',
                    language: 'es',
                    autoclose: true,
                    startDate:'0d'
                });
                $(el).val(App.timeStamp(1));
            }
        },
        timepicker: {
            inserted: function (el) {
                $(el).timepicker({
                    format: 'hh:mm a',
                    language: 'es',
                    autoclose: true,
                });
            }
        },
    },
    computed:{

    },
    methods: {
        init: function () {
            var url = App.host + '/administracion/cierre_usuario_configuracion/cierre_periodo/init';
            var _this = this;
            $.ajax({
                type: 'GET',
                url: url,
                beforeSend: function () {
                    _this.cargando = true;
                },
                success: function (response) {
                    _this.usuarios = response.usuarios;
                    _this.cierres_periodo = response.cierres;
                    _this.historico = response.historico;
                },
                error: function (error) {
                    if (error.status == 422) {
                        App.setErrorsOnForm(_this.form, error.responseJSON);
                    } else if (error.status == 500) {
                        swal({
                            type: 'error',
                            title: '¡Error!',
                            text: App.errorsToString(error.responseText)
                        });
                    }
                },
                complete: function () {
                    _this.cargando = false;
                }
            });
        },
        select_usuario: function () {
            var indice = $("#rol").prop('selectedIndex');
            if(this.selected_usuario_id) {
                Vue.set(this, 'selected_usuario', this.usuarios[indice - 1]);
            }else {
                Vue.set(this, 'selected_usuario', {});
            }

        },
        add_permiso_click: function (e) {
            e.preventDefault();

            var _this = this;
            var form = $('#cierre_form');
            var url = form.attr('action');
            var type = form.attr('method');
            $.ajax({
                type: type,
                url: url,
                data: {
                    'cierresSelect': _this.select,
                    'usuario':$('#selUser').val(),
                    'fecha_inicial':$('#FechaInicial').val(),
                    'hora_inicial':$('#HoraInicial').val(),
                    'fecha_final':$('#FechaFinal').val(),
                    'hora_final':$('#HoraFinal').val()
                },
                beforeSend: function () {
                    _this.guardando = true;
                    $( "#btnCierre" ).prop( "disabled", true );
                },
                success: function (response) {
                    _this.usuarios = response.usuarios;
                    _this.cierres_periodo = response.cierres;
                    _this.historico = response.historico;
                    swal("Correcto!", "Se ha creado correctamente la configuracion.", "success");
                },
                error: function (error) {
                    if (error.status == 422) {
                        App.setErrorsOnForm(_this.form, error.responseJSON);
                    } else if (error.status == 500) {
                        swal({
                            type: 'error',
                            title: '¡Error!',
                            text: App.errorsToString(error.responseText)
                        });
                    }
                },
                complete: function () {
                    _this.guardando = false;
                    $( "#btnCierre" ).prop( "disabled", false );


                }
            });
        },
    }


    });