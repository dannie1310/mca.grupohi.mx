<!-- Modal Historico -->
<div class="modal fade" id="historicoModal" tabindex="-1" role="dialog" aria-labelledby="historicoModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">HISTORICO DE CAMBIOS PARA {{$catalogo}}</h4>
            </div>
            <div class="modal-body">
                <div class="table-responsive">
                    <table class="table table-striped small">
                        @if(! count($rows))
                            <h4 style="text-align: center">NO EXISTEN REGISTROS EN EL HISTORICO DE {{$catalogo}}</h4>
                        @else
                        <thead>
                        <tr>
                            @foreach($headers as $header)
                                <th>{{$header}}</th>
                            @endforeach
                        </tr>
                        </thead>
                        <tbody>
                        @for($i = 0; $i < count($rows); $i++)
                            <tr>
                                <td>{{$i+1}}</td>
                                @for($j = 0; $j < count($rows[$i]); $j++)
                                <td style="white-space: nowrap">{{$rows[$i][$j]}}</td>
                                @endfor
                            </tr>
                        @endfor
                        </tbody>
                    </table>
                    @endif
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>