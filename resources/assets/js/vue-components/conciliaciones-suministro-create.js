/**
 * Created by DBENITEZ on 13/11/2017.
 */
Vue.component('conciliaciones-suministro-create', {
    data: function() {
        return {
            'form' : {
                'errors' : []
            },
        }
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
        confirmarRegistro: function(e) {
            e.preventDefault();

            swal({
                title: "¿Desea continuar con el registro?",
                text: "¿Esta seguro de que la información es correcta?",
                type: "info",
                showCancelButton: true,
                confirmButtonText: "Si",
                cancelButtonText: "No",
            }, () => this.registrar() );
        },

        registrar: function() {
            var _this = this;
            this.form.errors = [];
            var url = App.host + '/conciliacion/suminitro/new';
            var data = $('.form_conciliacion_create').serialize();

            $.ajax({
                url : url,
                data: data,
                type: 'POST',
                success: function(response) {
                    var conciliacion = response.conciliacion;
                    window.location.href = App.host + '/conciliacion/suministro/' + conciliacion.idconciliacion + '/edit';
                },
                error: function (error) {
                    App.setErrorsOnForm(_this.form, error.responseJSON);
                }
            });
        }
    }
});