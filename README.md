## Commands
Instalação
```
//adicione no providers config\app.php
marcusvbda\vstack\vStackServiceProvider::class

//adicione no app.scss
@import "../../../vendor/marcusvbda/vstack/src/Assets/scss/autoload.scss";

//adicione no app.js
require('../../../vendor/marcusvbda/vstack/src/Assets/js/components/autoload')
```

para criar um novo resource, você precisa executar o comando especificando o do resource, model e tabela, respectivamente
```
php artisan vstack:make-resource {resource} {model} {table}
```

para criar um filtro para o resource
```
php artisan vstack:make-filter {resource} {name} {type} {index} {label}
```

para criar um resource card metric
```
php artisan vstack:make-metric {resource} {name} {type}
```

os tipos de metrics
``` 
custom-content
group-graph
simple-counter
```

para executar as filas do vstack
```
php artisan queue:work --queue=mail,resource-import,alert-broadcasts
```
