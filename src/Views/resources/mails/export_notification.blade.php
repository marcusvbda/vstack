<?php 
    $appName = config("app.name"); 
?>
<p>Olá {{$user->name}},</p>
<p>Segue abaixo a url de download da sua planilha de {{$resource->label()}}</p>
<p>
    <a href="{{$route}}" target="_BLANK">{{$route}}</a>
</p>
<p style='margin-top:30px'>Obrigado, {{$appName}}"