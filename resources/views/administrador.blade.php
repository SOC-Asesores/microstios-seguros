<!doctype html>
<html lang="en">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
    <link rel="stylesheet" type="text/css" href="{{ url('css/slick.css') }}"/>
    <link rel="stylesheet" type="text/css" href="{{ url('css/slick-theme.css') }}"/>
    <link rel="stylesheet" href="{{ url('css/app.css') }}">
    <script src="https://kit.fontawesome.com/ab58011517.js" crossorigin="anonymous"></script>
    <title>Administrador Micrositios</title>
  </head>
  <body class="body-admin">
    <header>
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-2">
                    <img src="{{ url('img/logo-SOC.jpg') }}" alt="" class="img-fluid">
                </div>
                <div class="col-md-10">
                    <h1 class="text-center">Bienvenido {{ Auth::user()->name }}</h1>
                </div>
            </div>
        </div>
    </header>
    <main>
        @if ($type == 1)
            <div class="container">
                <div class="row">
                    <div class="col-12 mb-4">
                        <form action="{{route("search")}}" method="POST">
                            @csrf
                            <div class="input-group justify-content-center">
                                <div class="form-outline">
                                    <input type="search" id="form1" name="search" class="form-control" placeholder="Buscar" />
                                </div>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-search"></i>
                                </button>
                            </div>
                        </form>
                    </div>
                    <div class="col-12">
                        <table class="table table-hover">
                            <thead class="thead-dark">
                            <tr>
                                <th>Sucursal</th>
                                <th>Correo</th>
                                <th>Url</th>
                                <th class="text-center">Certificacion</th>
                            </tr>
                            </thead>
                            <tbody>
                                @foreach ($registro as $user)
                                    <tr>
                                        <td>{{$user->sucursal}}</td>
                                        <td>{{$user->email}}</td>
                                        <td><a href="{{url('broker')}}/{{$user->url}}">{{url('broker')}}/{{$user->url}}</a></td>
                                        <td class="text-center">
                                            <select class="custom-select" name="certificacion" id="{{$user->id}}">
                                                @switch($user->certificacion)
                                                    @case(1)
                                                        <option value="0">Sin Certificación</option>
                                                        <option selected value="1">Plata</option>
                                                        <option value="2">Oro</option>
                                                        <option value="3">Diamante</option>
                                                        @break

                                                    @case(2)
                                                        <option value="0">Sin Certificación</option>
                                                        <option value="1">Plata</option>
                                                        <option selected value="2">Oro</option>
                                                        <option value="3">Diamante</option>
                                                        @break

                                                    @case(3)
                                                        <option value="0">Sin Certificación</option>
                                                        <option value="1">Plata</option>
                                                        <option value="2">Oro</option>
                                                        <option selected value="3">Diamante</option>
                                                        @break
                                                    @default
                                                        <option selected value="0">Sin Certificación</option>
                                                        <option value="1">Plata</option>
                                                        <option value="2">Oro</option>
                                                        <option value="3">Diamante</option>
                                                @endswitch
                                            </select>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        @endif
    </main>
  </body>
 <!-- Optional JavaScript -->
    <!-- jQuery first, then Popper.js, then Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
    <script src="{{url('js/slick.min.js')}}"></script>
    <script src="{{url('js/main.js')}}"></script>
    <script>
        $('select[name="certificacion"]').on('change', function (e) {
            var id = $(this).attr("id");
            var optionSelected = $("option:selected", this);
            var valueSelected = this.value;
            e.preventDefault();
            $.ajax({
                    headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                    url: "{{route('certificado')}}",
                    type: 'POST',
                    data: {
                        id: id,
                        option: valueSelected,
                    },
                    success: function(response){
                        console.log(response);
                    }
            });
        });
    </script>
  </body>
</html>