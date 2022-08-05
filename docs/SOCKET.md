
# Vstack Socket
Para despachar um evento socket basta executar o comando abaixo;
```
use marcusvbda\vstack\Vstack;

Vstack::SocketEmit($event, $channel, [
   "lorem" => "ipsum
]);
```


### Socket Server ( nodejs )
Para adicionar um atalho para o socket server, criei um arquivo js com o nome que quiser na pasta root do sistema e copie o código abaixo;

```
node ./vendor/marcusvbda/vstack/src/Node/socket_server.js
```

em seguida este arquivo node precisa ser executado para o server está online

lembrando que o env deve ser configurado

```
SOCKET_SERVER=http://localhost:3003
SOCKET_SERVER_ENABLED=true
```