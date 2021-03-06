<h1>VIAJES</h1>
{!! Breadcrumbs::render('viajes_netos.autorizar') !!}
<hr>
<div class="table-responsive">
    {!! Form::open(['id' => 'viaje_neto_autorizar' , 'method' => 'patch', 'route' => ['viajes_netos.autorizar']]) !!}
    <table id="viajes_netos_autorizar" class="table table-condensed">
        <thead>
            <tr>
                <th>Folio</th>
                <th>Fecha Llegada</th>
                <th>Hora Llegada</th>
                <th>Camión</th>
                <th>Tiro</th>
                <th>Origen</th>
                <th>Material</th>
                <th>Registro</th>
                <th>Observaciones</th>
                <th><i style="color: green" class="fa fa-check"></i></th>
                <th><i style="color: red" class="fa fa-remove"></i></th>
            </tr>
        </thead>
        <tbody>
            @foreach($viajes as $viaje)
                <?php  $find=0; $fecha = Carbon\Carbon::createFromFormat('Y-m-d', $viaje->FechaLlegada);?>
                <tr>
                    <td>{{ $viaje->Code }}</td>
                    <td>{{ $viaje->FechaLlegada }}</td>
                    <td>{{ $viaje->HoraLlegada }}
                    <td>{{ $viaje->Camion }}</td>
                    <td>{{ $viaje->Tiro }}</td>
                    <td>{{ $viaje->Origen }}</td>
                    <td>{{ $viaje->Material }}</td>
                    <td>{{ $viaje->Registro }}</td>
                    <td>{{ $viaje->Observaciones }}</td>
                @foreach($cierre as $item)
                        @if($item['mes'] == $fecha->month && $item['anio'] == $fecha->year)
                                <?php $find++; ?>
                        @endif
                @endforeach

                @if($find !=0)
                    <td>
                        Periodo
                    </td>
                    <td>
                         Cerrado
                    </td>
                @else
                    @if($viaje->denegado == 1)
                        <td>DENEGADO</td>
                        <td>DENEGADO</td>
                    @else
                        <td>
                            <input id="{{$viaje->IdViajeNeto}}" type="checkbox" value="20" name="Estatus[{{$viaje->IdViajeNeto}}]"/>
                        </td>
                        <td>
                            <input id="{{$viaje->IdViajeNeto}}" type="checkbox" value="22" name="Estatus[{{$viaje->IdViajeNeto}}]"/>
                        </td>
                    @endif
                @endif
                </tr>
            @endforeach
        </tbody>
    </table> 
    <div class="form-group col-md-12" style="text-align: center; margin-top: 20px">         
        {!! Form::submit('Continuar', ['class' => 'btn btn-success']) !!}
    </div>
    {!! Form::close() !!}
</div>