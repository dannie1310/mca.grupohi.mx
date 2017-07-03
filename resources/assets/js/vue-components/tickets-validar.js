Vue.component('tickets-validar', {
    data:function() {
        return {
            code: '',
            items:{},
            click: false
        }
    },
    created: function() {

    },

    methods: {
        escanear: function (e) {
            var self = this;
            if ((e.keyCode == 13)){
                self.click = true;
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
                    alert(error.responseText);
                }
            });
        }


    }

});