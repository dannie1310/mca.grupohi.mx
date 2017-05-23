/**
 * Created by JFEsquivel on 27/04/2017.
 */

Vue.component('configuracion-diaria', {
    props: ['rol_checador'],

    data: function () {
        return {
            checadores : [],
            tiros      : [],
            origenes   : [],
            esquemas   : [],
            perfiles   : [],
            telefonos  : [],
            current_checador : null,
            form: {
                errors : []
            },
            guardando  : false,
            cargando   : false
        }
    },

    created: function () {
        this.initialize();
    },

    computed: {
        para_origen: function () {
            return this.perfiles.filter(function(perfil) {
                if(perfil.id_esquema == '1') {
                    return true;
                }
                return false;
            });
        },

        para_tiro: function () {
            return this.perfiles.filter(function(perfil) {
                if(perfil.id_esquema == '2') {
                    return true;
                }
                return false;
            });
        }
    },

    methods: {
        initialize: function () {
            var _this = this;
            var url = App.host + '/init/configuracion-diaria';

            $.ajax({
                type       : 'GET',
                url        : url,
                beforeSend : function () {
                    _this.cargando = true;
                },
                success    : function (response) {
                    _this.checadores = response.checadores;
                    _this.checadores.forEach(function (checador) {
                        Vue.set(checador, 'guardando', false);
                    });
                    _this.tiros = response.tiros;
                    _this.tiros.forEach(function (tiro) {
                        Vue.set(tiro, 'guardando' , false);
                    });

                    _this.origenes = response.origenes;
                    _this.perfiles = response.perfiles;
                    _this.esquemas = response.esquemas;
                    _this.telefonos = response.telefonos;
                },
                error      : function (error) {
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
                complete   : function () {
                    _this.cargando = false;
                }
            });
        },

        tiro_by_id: function (id) {
            var result = {};
            this.tiros.forEach(function (tiro) {
                if (tiro.id == id) {
                    result = tiro;
                }
            });
            return result;
        },

        origen_by_id: function (id) {
            var result = {};
            this.origenes.forEach(function (origen) {
                if(origen.id == id) {
                    result = origen;
                }
            });
            return result;
        },

        set_ubicacion: function (user, e) {
            var id = $(e.currentTarget).val();
            if (user.configuracion.tipo == 1) {
                Vue.set(user.configuracion, 'ubicacion', this.tiro_by_id(id));
            } else if (user.configuracion.tipo == 0) {
                Vue.set(user.configuracion, 'ubicacion', this.origen_by_id(id));
            }
            Vue.set(user.configuracion, 'id_perfil', '');
            Vue.set(user.configuracion, 'turno', '');
        },

        clear_ubicacion: function (user) {
            Vue.set(user.configuracion, 'ubicacion', {
                id: '',
                descripcion: ''
            });
        },

        guardar_configuracion: function (user) {
            var data = {
                'id_usuario' : user.id,
                'tipo' : user.configuracion.tipo,
                'id_ubicacion' : user.configuracion.ubicacion.id,
                'id_perfil' : user.configuracion.id_perfil,
                'turno' : user.configuracion.turno
            };

            var _this = this;
            var url = App.host + '/configuracion-diaria?type=ubicacion';

            $.ajax({
                type: 'POST',
                url: url,
                data: data,
                beforeSend: function () {
                    user.guardando = true;
                },
                success: function (response) {
                    var checador = response.checador;
                    checador.guardando = false;
                    Vue.set(_this.checadores, _this.checadores.indexOf(user), checador);
                    swal({
                        type : 'success',
                        title : '¡Configuración Correcta!',
                        text: 'Configuración establecida correctamente<br> para el usuario </strong>' + user.nombre +'</strong>',
                        html: true
                    });
                },
                error: function (error) {
                    if (error.status == 422) {
                        swal({
                            type : 'error',
                            title : '¡Error!',
                            text : App.errorsToString(error.responseJSON)
                        });
                    } else if (error.status == 500) {
                        swal({
                            type: 'error',
                            title: '¡Error!',
                            text: App.errorsToString(error.responseText)
                        });
                    }
                },
                complete: function () {
                    user.guardando = false;
                }
            });
        },

        quitar_configuracion: function (user) {
            var _this = this;
            swal({
                type : 'warning',
                title : '¡Alerta!',
                text : '¿Realmente desea eliminar la configuración para el checador<br>'+user.nombre+'?',
                html : true,
                showCancelButton: true,
                confirmButtonText: "Si, eliminar",
                cancelButtonText: "No, cancelar",
            }, () => _this.eliminar(user));
        },

        eliminar:function (user) {
            var url = App.host + '/configuracion-diaria/' + user.configuracion.id;
            $.ajax({
                type: 'POST',
                url: url,
                data: {
                    _method: 'DELETE'
                },
                beforeSend: function () {
                    user.guardando = true;
                },
                success: function () {
                    swal({
                        type: 'success',
                        text: 'Configuración eliminada correctamente',
                        title: 'Información'
                    });
                    Vue.set(user, 'configuracion',{
                        tipo : '',
                        ubicacion : {
                            id : '',
                            descripcion : ''
                        },
                        id_perfil : '',
                        turno: ''
                    });
                },
                error: function (error) {
                    if (error.status == 422) {
                        swal({
                            type : 'error',
                            title : '¡Error!',
                            text : App.errorsToString(error.responseJSON)
                        });
                    } else if (error.status == 500) {
                        swal({
                            type: 'error',
                            title: '¡Error!',
                            text: App.errorsToString(error.responseText)
                        });
                    }
                },
                complete: function () {
                    user.guardando = false;
                }
            });
        },

        confirmar_quitar_checador: function (user) {
            var _this = this;
            swal({
                type : 'warning',
                title : '¡Alerta!',
                text : "¿Realmente desea quitar el permiso de 'Checador' para el usuario<br><strong>"+user.nombre+"</strong>?",
                html : true,
                showCancelButton: true,
                confirmButtonText: "Si, quitar",
                cancelButtonText: "No, cancelar",
            }, () => _this.quitar_checador(user));
        },

        quitar_checador: function (user) {
            var _this = this;
            var url = App.host + '/user/' + user.id + '/roles/' + _this.rol_checador;
            $.ajax({
                type: 'POST',
                url: url,
                data: {
                    _method: 'DELETE'
                },
                beforeSend: function () {
                    user.guardando = true;
                },
                success: function (response) {
                    Vue.delete(_this.checadores, _this.checadores.indexOf(user));
                    _this.telefonos = response.telefonos;
                },
                error: function (error) {
                    if (error.status == 422) {
                        swal({
                            type : 'error',
                            title : '¡Error!',
                            text : App.errorsToString(error.responseJSON)
                        });
                    } else if (error.status == 500) {
                        swal({
                            type: 'error',
                            title: '¡Error!',
                            text: App.errorsToString(error.responseText)
                        });
                    }
                }
            });
        },

        /** Mostrar datos de ususario al que se le asignará un teléfono **/
        asignar_telefono: function (user) {
            Vue.set(this, 'current_checador', user);
            this.form.errors = [];
            $('#telefonos_modal').modal('show');
        },

        /** Ocultar datos de ususario al que se le asignará un teléfono **/
        cancelar_asignacion: function () {
            Vue.set(this, 'current_checador', null);
            $('#telefonos_modal').modal('hide');
        },
        confirmar_asignacion: function (e) {
            e.preventDefault();
            var _this = this;
            var data = $('#asignar_telefono_form').serialize();
            var url = App.host + '/configuracion-diaria?type=telefono';

            $.ajax({
                type: 'POST',
                url: url,
                data: data,
                beforeSend: function() {
                    _this.guardando = true;
                },
                success: function (response) {
                    $('#telefonos_modal').modal('hide');
                    Vue.set(this, 'current_user', null);
                    swal({
                        type : 'success',
                        title : '¡Configuración Correcta!',
                        text : 'Teléfono <strong>' + response.checador.telefono.info +  '</strong> configurado correctamente para el checador <br><strong>' + response.checador.nombre + '</strong>',
                        html: true
                    });
                    Vue.set(_this.checadores, _this.checadores.indexOf(_this.current_checador), response.checador);
                    _this.telefonos = response.telefonos;
                },
                error: function (error) {
                    if (error.status == 422) {
                        /*swal({
                            type : 'error',
                            title : '¡Error!',
                            text : App.errorsToString(error.responseJSON)
                        })*/
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
            });
        }
    }
});