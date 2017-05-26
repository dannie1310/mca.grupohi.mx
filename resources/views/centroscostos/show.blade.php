<tr bgcolor="{{$type}}" id="{{$centro->IdCentroCosto}}" class="treegrid-{{$centro->IdCentroCosto}} treegrid-parent-{{$centro->IdPadre}}">
    <td>{{$centro->Descripcion}}</td>
    <td>{{$centro->Cuenta}}</td>
    <td>{{$centro->created_at}}</td>
    <td>{{$centro->user_registro }}</td>
    <td>{{$centro->estatus_string }}</td>
    <td>
        <a href="{{ route('centroscostos.show', $centro) }}" title="Ver" class="btn btn-xs btn-default"><i class="fa fa-eye"></i></a>
        @permission('editar-centroscostos')
        <a href="{{ route('centroscostos.edit', $centro) }}" title="Editar" class="btn btn-xs btn-info centrocosto_edit"><i class="fa fa-pencil"></i></a>
        @endpermission
        @permission('desactivar-centroscostos')
        @if($centro->Estatus == 1)
            <button type="submit" title="Desactivar" class="btn btn-xs btn-danger" onclick="desactivar_centro({{$centro->IdCentroCosto}})"><i class="fa fa-remove"></i></button>
        @else
            <button type="submit" title="Activar" class="btn btn-xs btn-success" onclick="activar_centro({{$centro->IdCentroCosto}})"><i class="fa fa-check"></i></button>
        @endif
        @endpermission
        @permission('crear-centroscostos')
        <a href="{{ route('centroscostos.create', $centro) }}" class="btn btn-success btn-xs centrocosto_create" type="button">
            <i class="fa fa-plus-circle"></i>
        </a>
        @endpermission
    </td>
</tr>
