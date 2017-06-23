@extends('layout')
@section('content')
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<body>

        <div class="row col-sm-12">
            <div class="form-group text-center">
                <h4> VALIDACI&Oacute;N TICKET ACARREOS</h4>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12">
                <div class="form-group">
                    <label>C&oacute;digo de Barras: </label>
                    <input class="form-control" readonly="true" style="font-size: 20px" value="<?php echo $info->barras     ?>" />
                    <h2></h2>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-sm-12">
                <div class="form-group">
                    <label>Proyecto: </label>
                    <input class="form-control" readonly="true" value=" <?php echo $info->proyecto     ?>" />
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12">
                <div class="form-group">
                    <label>Cami&oacute;n: </label>
                    <input class="form-control" readonly="true" value="<?php echo $info->camion     ?>" />
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12">
                <div class="form-group">
                    <label>Cubicaci&oacute;n: </label>
                    <input class="form-control" readonly="true" value="<?php echo $info->cubicacion     ?>" />
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12">
                <div class="form-group">
                    <label>Material: </label>
                    <input class="form-control" readonly="true" value="<?php echo $info->material     ?>" />
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12">
                <div class="form-group">
                    <label>Origen: </label>
                    <input class="form-control" readonly="true" value="<?php echo $info->origen     ?>" />
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12">
                <div class="form-group">
                    <label>Fecha de Salida: </label>
                    <input class="form-control" readonly="true" value="<?php echo $info->fechaSalida     ?>" />
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12">
                <div class="form-group">
                    <label>Destino: </label>
                    <input class="form-control" readonly="true" value="<?php echo $info->destino     ?>" />
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12">
                <div class="form-group">
                    <label>Fecha de Llegada: </label>
                    <input class="form-control" readonly="true" value="<?php echo $info->fechaLlegada     ?>" />
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12">
                <div class="form-group">
                    <label>Checador Inicio: </label>
                    <input class="form-control" readonly="true" value="<?php echo $info->ChInicio     ?>" />
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12">
                <div class="form-group">
                    <label>Checador Cierre: </label>
                    <input class="form-control" readonly="true" value="<?php echo $info->ChCierre     ?>" />
                </div>
            </div>
        </div>


</body>
</html>
@stop