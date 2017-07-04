Vue.component('tickets-validar', {
    data:function() {
        return {
            code: '',
            items:{},
            click: false,
            error: ''
        }
    },
    created: function() {

    },

    methods: {
        escanear: function (e) {
            var self = this;
            if ((e.keyCode == 13)){
                self.click = true;
                self.error = '';
                self.items = {};
                self.decodificar(e);
            }
        },
        decodificar: function(e) {
            e.preventDefault();
            var self = this;
            var url = 'http://localhost:8000/tickets/validar?data=' + self.code;

            $.ajax({
                type : 'get',
                url: url,
                beforeSend: function() {
                },
                success: function(response) {
                    self.items = response;
                },
                error: function(error) {
                    self.error = '¡¡TICKET INVÁLIDO!!';
                }
            });
        },
        limpiar: function () {
            var self = this;
            self.code = '';
            self.error = '';
            self.items = {};
        }


    }

});