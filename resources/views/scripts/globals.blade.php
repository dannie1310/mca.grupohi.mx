<!-- App Globals -->
<script>
    window.App = {
        // Token CSRF de Laravel
        csrfToken: '{{ csrf_token() }}',
        host: '{{ url("/") }}',
        tablefilterBasePath: '{{ asset("tablefilter"). '/'}}',

        // ID del Usuario Actual
        userId: {!! Auth::check() ? Auth::id() : 'null' !!},

        // Transformar los errores y asignarlos al formulario
        setErrorsOnForm: function (form, errors) {
            if (typeof errors === 'object') {
                form.errors = _.flatten(_.toArray(errors));
            } else {
                var ind1 = errors.indexOf('<span class="exception_message">');
                var cad1 = errors.substring(ind1);
                var ind2 = cad1.indexOf('</span>');
                var cad2 = cad1.substring(32,ind2);
                if(cad2 != ""){
                    form.errors.push( cad2);
                }else{
                    form.errors.push('Un error grave ocurrió. Por favor intente otra vez.');
                }
            }
        },
        errorsToString: function(errors) {
            if (typeof errors === 'object') {
                return _.flatten(_.toArray(errors));
            } else {
                var ind1 = errors.indexOf('<span class="exception_message">');
                var cad1 = errors.substring(ind1);
                var ind2 = cad1.indexOf('</span>');
                var cad2 = cad1.substring(32,ind2);
                if(cad2 != ""){
                    return cad2;
                }else{
                    return 'Un error grave ocurrió. Por favor intente otra vez.';
                }
            }
        },
        timeStamp: function(type) {
            var today = new Date();
            var dd = today.getDate();
            var mm = today.getMonth() + 1; //January is 0!
            var yyyy = today.getFullYear();
            var hh = today.getHours();
            var min = today.getMinutes();

            if(dd < 10) {dd = '0' + dd}
            if(mm < 10) {mm = '0' + mm}
            if(hh < 10) {hh = '0' + hh}
            if(min < 10) {min = '0' + min}

            var date = yyyy + '-' + mm + '-' + dd;
            var time = hh + ":" + min;

            return type == 1 ? date : time;
        }
    }
</script>
<script src="https://www.gstatic.com/firebasejs/5.5.6/firebase.js"></script>
<script type="text/javascript">
    // Initialize Firebase
    var config = {
        apiKey: "AIzaSyBzJ-AQ26h0_Tjl6Lev93cdRpF-sYOILq4",
        authDomain: "sistema-control-de-acarreos.firebaseapp.com",
        databaseURL: "https://sistema-control-de-acarreos.firebaseio.com",
        projectId: "sistema-control-de-acarreos",
        storageBucket: "sistema-control-de-acarreos.appspot.com",
        messagingSenderId: "258473960894"
    };
    firebase.initializeApp(config);
    database = firebase.database();

    if("{!! Auth::user() !!}") {
        firebase.database().ref('/users/' + {!! Auth::check() ? Auth::id() : 'null' !!} + '/session_id').set("{!! (Session::getId())?Session::getId():'' !!}");
    }

    firebase.database().ref('/users/' + {!! Auth::check() ? Auth::id() : 'null' !!}).on('value', function(snapshot2) {
        v = snapshot2.val();
        if(snapshot2.val() != null) {
            if (v.session_id != "{!! (Session::getId())?Session::getId():'' !!}") {
                alert("¡Se tiene una sesión activa en otro dispositivo!.");
                setTimeout(function(){
                    window.location = '/auth/logout';
                },1000,"JavaScript");
            }
        }
    });
</script>