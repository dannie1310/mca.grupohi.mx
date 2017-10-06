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
            var indice=$("#rol").prop('selectedIndex');



        },

    }


    });