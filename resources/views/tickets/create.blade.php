@extends('layout')
@section('content')
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<body>

        <div class="row">
            <div class="form-group  col-sm-12 text-center">
                <h3><strong>VALIDACI&Oacute;N TICKET ACARREOS</strong> </h3>
            </div>
        </div>

        <div class="row">
            <div class="col-sm-12 ">
                <div class="form-group text-center text-uppercase text-info">
                    <h1><strong> <?php echo $info->barras     ?></strong></h1>
                </div>
            </div>
        </div>
        @if($info->proyecto !=" ")
        <div class="row">
            <div class="col-sm-12">
                <div class="form-group">
                    <label>Proyecto: </label>
                    <input class="form-control" readonly="true" value=" <?php echo $info->proyecto     ?>" />

                </div>
            </div>
        </div>
        @endif
        @if($info->camion !=" ")
        <div class="row">
            <div class="col-sm-12">
                <div class="form-group">
                    <label>Cami&oacute;n: </label>
                    @if($info->camion=="No se encontro en la base de datos")
                        <input class="form-control" style="color: #FF0000" readonly="true" value="<?php echo $info->camion     ?>" />
                    @else
                        <input class="form-control" readonly="true" value="<?php echo $info->camion     ?>" />
                    @endif
                </div>
            </div>
        </div>
        @endif

        <div class="row">
            <div class="col-sm-12">
                <div class="form-group">
                    <label>Cubicaci&oacute;n: </label>
                    <input class="form-control" readonly="true" value="<?php echo $info->cubicacion     ?>" />
                </div>
            </div>
        </div>
        @if($info->material !=" ")
        <div class="row">
            <div class="col-sm-12">
                <div class="form-group">
                    <label>Material: </label>
                    @if($info->material=="No se encontro en la base de datos")
                        <input class="form-control" style="color: #FF0000" readonly="true" value="<?php echo $info->material     ?>" />
                    @else
                        <input class="form-control" readonly="true" value="<?php echo $info->material     ?>" />
                    @endif
                </div>
            </div>
        </div>
        @endif
        @if($info->origen !=" ")
        <div class="row">
            <div class="col-sm-12">
                <div class="form-group">
                    <label>Origen: </label>
                    @if($info->origen=="No se encontro en la base de datos")
                        <input class="form-control" style="color: #FF0000" readonly="true" value="<?php echo $info->origen     ?>" />
                    @else
                        <input class="form-control" readonly="true" value="<?php echo $info->origen     ?>" />
                    @endif
                </div>
            </div>
        </div>
        @endif
        @if($info->fechaSalida !=" ")
        <div class="row">
            <div class="col-sm-12">
                <div class="form-group">
                    <label>Fecha de Salida: </label>
                    <input class="form-control" readonly="true" value="<?php echo $info->fechaSalida     ?>" />
                </div>
            </div>
        </div>
        @endif
        @if($info->destino !=" ")
        <div class="row">
            <div class="col-sm-12">
                <div class="form-group">
                    <label>Destino: </label>
                    @if($info->destino=="No se encontro en la base de datos")
                        <input class="form-control" style="color: #FF0000" readonly="true" value="<?php echo $info->destino     ?>" />
                    @else
                        <input class="form-control" readonly="true" value="<?php echo $info->destino     ?>" />
                    @endif
                </div>
            </div>
        </div>
        @endif
        @if($info->fechaLlegada !=" ")
        <div class="row">
            <div class="col-sm-12">
                <div class="form-group">
                    <label>Fecha de Llegada: </label>
                    <input class="form-control" readonly="true" value="<?php echo $info->fechaLlegada     ?>" />
                </div>
            </div>
        </div>
        @endif
        <div class="row">
            <div class="col-sm-12">
                <div class="form-group">
                    <label>Checador Inicio: </label>
                    @if($info->ChInicio=="No se encontro en la base de datos")
                        <input class="form-control" style="color: #FF0000" readonly="true" value="<?php echo $info->ChInicio     ?>" />
                    @else
                        <input class="form-control" readonly="true" value="<?php echo $info->ChInicio     ?>" />
                    @endif
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12">
                <div class="form-group">
                    <label>Checador Cierre: </label>
                    @if($info->ChCierre=="No se encontro en la base de datos")
                        <input class="form-control" style="color: #FF0000" readonly="true" value="<?php echo $info->ChCierre     ?>" />
                    @else
                        <input class="form-control" readonly="true" value="<?php echo $info->ChCierre     ?>" />
                    @endif

                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12">
                <div class="form-group">
                    <label>Tipo Suministro: </label>
                    <input class="form-control" readonly="true" value="<?php echo $info->tipo_suministro ?>" />

                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12">
                <div class="form-group">
                    <label>Tipo Suministro: </label>
                    <input class="form-control" readonly="true" value="<?php echo $info->folio_mina ?>" />

                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12">
                <div class="form-group">
                    <label>Tipo Suministro: </label>
                    <input class="form-control" readonly="true" value="<?php echo $info->folio_seg ?>" />

                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12">
                <div class="form-group">
                    <label>Tipo Suministro: </label>
                    <input class="form-control" readonly="true" value="<?php echo $info->volumen ?>" />

                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12">
                <div class="form-group">
                    <label>Tipo Suministro: </label>
                    <input class="form-control" readonly="true" value="<?php echo $info->tipo_permiso ?>" />

                </div>
            </div>
        </div>


</body>
</html>
@stop