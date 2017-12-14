/**
 * Created by DBENITEZ on 08/12/2017.
 */
// register modal component
Vue.component('modal-modificar', {
    template: '#modal-template'
});

Vue.component('suministro-modificar', {
    data : function() {
        return {
            'viajes_netos' : [],
            'cargando' : false,
            'guardando' : false,
            'form' : {
                'data' : {
                    'Cubicacion' : '',
                    'IdOrigen' : '',
                    'IdMaterial' : '',
                    'FolioMina' : '',
                    'FolioSeguimiento' : '',
                    'Volumen' : ''
                },
                'errors' : []
            },
        }
    },

    directives: {
        datepicker: {
            inserted: function(el) {
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

        tablefilter: {
            inserted: function(el) {
                var val_config = {
                    auto_filter: true,
                    watermark: [
                        '#',
                        'Fecha',
                        'Origen',
                        'Camion',
                        'Cubic.',
                        'Material',
                        'Código',
                        'Folio de Mina',
                        'Folio de Seguimiento',
                        'Volumen',
                        'Modificar'
                    ],
                    col_0: 'none',
                    col_1: 'select',
                    col_2: 'select',
                    col_3: 'select',
                    col_4: 'none',
                    col_5: 'select',
                    col_6: 'select',
                    col_7: 'input',
                    col_8: 'input',
                    col_9: 'none',

                    base_path: App.tablefilterBasePath,
                    paging: false,
                    rows_counter: false,
                    rows_counter_text: 'Viajes: ',
                    btn_reset: true,
                    btn_reset_text: 'Limpiar',
                    clear_filter_text: 'Limpiar',
                    loader: true,
                    help_instructions: false,
                    extensions: [{ name: 'sort' }]
                };
                var tf = new TableFilter('viajes_netos_modificar', val_config);
                tf.init();
            }
        }
    },

    methods: {

        buscar: function(e) {

            e.preventDefault();

            var _this = this;

            this.cargando = true;
            this.form.errors = [];

            var data = $('.form_buscar').serialize();
            var url = App.host + '/suministro_netos?action=modificar&' + data;

            this.$http.get(url).then((response) => {
                _this.cargando = false;
                if(! response.body.viajes_netos.length) {
                    swal('¡Sin Resultados!', 'Ningún viaje coincide con los datos de consulta', 'warning');
                } else {
                    _this.viajes_netos = response.body.viajes_netos;
                }
            }, (error) => {
                _this.cargando = false;
                swal('¡Error!', App.errorsToString(error.body), 'error');
            });
        },

        modificar: function(viaje) {

            var _this = this;

            swal({
                    title: "¿Desea continuar con la modificación?",
                    text: "¿Esta seguro de que la información es correcta?",
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonText: "Si",
                    cancelButtonText: "No",
                    confirmButtonColor: "#ec6c62"
                },
                function () {
                    _this.guardando = true;
                    _this.form.errors = [];
                    var data = _this.form.data;

                    _this.$http.post(App.host + '/suministro_netos', {'type' : 'modificar', '_method' : 'PATCH', 'IdViajeNeto' : viaje.IdViajeNeto,  data}).then((response) => {
                        swal({
                            type: response.body.tipo,
                            title : '',
                            text: response.body.message,
                            showConfirmButton: true,
                            html:true
                        });

                        viaje.Cubicacion = response.body.viaje.Cubicacion;
                        viaje.FolioMina = response.body.viaje.FolioMina;
                        viaje.FolioSeguimiento = response.body.viaje.FolioSeguimiento;
                        viaje.Origen = response.body.viaje.Origen;
                        viaje.IdOrigen = response.body.viaje.IdOrigen;
                        viaje.Material = response.body.viaje.Material;
                        viaje.IdMaterial = response.body.viaje.IdMaterial;
                        viaje.Volumen = response.body.viaje.Volumen;

                        viaje.ShowModal = false;
                        _this.guardando = false;
                    }, (error) => {
                        _this.guardando = false;
                        viaje.ShowModal = false;
                        swal({
                            type: 'error',
                            title: '¡Error!',
                            text: App.errorsToString(error.body),
                            html: true
                        });
                    });
                });
        },

        showModal: function(viaje) {
            viaje.ShowModal = true;
            this.initializeData(viaje);
        },

        initializeData: function(viaje) {
            this.form.data.Cubicacion = viaje.Cubicacion;
            this.form.data.IdOrigen = viaje.IdOrigen;
            this.form.data.IdMaterial = viaje.IdMaterial;
            this.form.data.FolioMina = viaje.FolioMina;
            this.form.data.FolioSeguimiento = viaje.FolioSeguimiento;
            this.form.data.Volumen = viaje.Volumen;
        }
    }
});