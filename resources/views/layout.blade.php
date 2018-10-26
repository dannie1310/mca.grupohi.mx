<!DOCTYPE html>
<html lang="es">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <title>{{ trans('strings.app_name') }}</title>
    <link rel="stylesheet" href="{{ asset(elixir('css/app.css')) }}">
    <script type="text/javascript" scr = "jquery.min.js"></script>
    <link href="//cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.css" rel="stylesheet"/>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
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
                  alert("Cierre de sesion");
                  setTimeout(function(){
                      window.location = '/auth/logout';
                  },800,"JavaScript");


              }
          }
        });
    </script>
    @include('scripts.globals')

@yield('styles')
  </head>
  <body>
    @include('partials.nav')

    <div class="container">
        @include('flash::message')
        @yield('content')
    </div>
    <br>
    <script src="{{ asset('tablefilter/tablefilter.js')}}"></script>
    <script src="{{ asset(elixir("js/app.js")) }}"></script>
    <script src="https://www.gstatic.com/firebasejs/5.5.5/firebase.js"></script>
    @yield('scripts')
  </body>
</html>