<?php 
    $appName = config("app.name"); 
?>
<p>Olá {{$user->name}},</p>
<p>Segue em anexo sua planilha de {{$resource->label()}}</p>
<p style='margin-top:30px'>Obrigado, {{$appName}}"