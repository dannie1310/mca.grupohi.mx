/**
 * Created by DBENITEZ on 05/10/2017.
 */
Vue.component('periodocierre-administracion',{
    data: function () {
        return {
            usuarios: [],
            cierres_periodo: [],

            selected_usuario_id: '',
            selected_usuario: {},
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
                    clearBtn: true,
                    todayHighlight: true,
                    endDate: '0d'
                });
                $(el).val(App.timeStamp(1));
            }
        },
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


        },
        add_permiso_click: function (e) {


            e.preventDefault();

            var _this = this;
            var form = $('#cierre_form');
            var url = form.attr('action');
            var type = form.attr('method');
            var idsSeleccionados = [];
            $("#seleccionValues option").each(function (index) {
                idsSeleccionados.push(this.id);
            });
            $.ajax({
                type: type,
                url: url,
               data: {
                    'permisos_cierre': idsSeleccionados,
                    'usuario':$('#selUser').val()
                    },
                beforeSend: function () {
                    _this.guardando = true;
                },
                success: function (response) {
                    _this.roles.push(response.rol);
                    $("#cierre_form")[0].reset();
                    swal("Correcto!", "Se ha creado correctamente tu rol.", "success");
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
                }
            })
        }
    }


    });