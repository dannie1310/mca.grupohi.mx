@extends('layout')

@section('content')
<h1>ETAPAS DE PROYECTO
  <a href="{{ route('etapas.create') }}" class="btn btn-success pull-right"><i class="fa fa-plus"></i> Nueva Etapa</a>
    <a href="{{ route('csv.etapas-proyecto') }}" style="margin-right: 5px" class="btn btn-info pull-right"><i class="fa fa-file-excel-o"></i> Descargar</a>
</h1>
{!! Breadcrumbs::render('etapas.index') !!}
<div class="table-responsive">
  <table class="table table-striped small">
    <thead>
      <tr>
        <th>ID Etapa</th>
        <th>Descripción</th>
        <th>Estatus</th>
        <th>Acciones</th>
      </tr>
    </thead>
    <tbody>
      @foreach($etapas as $etapa)
        <tr>
          <td>
            <a href="{{ route('etapas.show', $etapa) }}">#{{ $etapa->IdEtapaProyecto }}</a>
          </td>
          <td>{{ $etapa->Descripcion }}</td>
          <td>{{ $etapa->present()->estatus }}</td>
          <td>
              <a href="{{ route('etapas.edit', [$etapa]) }}" class="btn btn-info btn-xs" title="Editar"><i class="fa fa-pencil"></i></a>
              @if($etapa->Estatus == 1)
              <a href="{{ route('etapas.destroy', [$etapa]) }}" class="btn btn-danger btn-xs element_destroy activo" title="Inhabilitar"><i class="fa fa-ban"></i></a>
              @else
              <a href="{{ route('etapas.destroy', [$etapa]) }}" class="btn btn-success btn-xs element_destroy inactivo" title="Habilitar"><i class="fa fa-check"></i></a>
              @endif
          </td>
        </tr>
      @endforeach
    </tbody>
  </table>
</div>
@stop