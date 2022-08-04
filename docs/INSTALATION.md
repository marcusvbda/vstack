# Instalando Via Composer

```
//instalação
composer require marcusvbda/vstack
//adicione no providers config\app.php
marcusvbda\vstack\vStackServiceProvider::class

//crie um template templates.admin

//adicione no app.scss
@import "./vendor/marcusvbda/vstack/src/Assets/scss/autoload.scss";

//adicione no app.js
require('../../../vendor/marcusvbda/vstack/src/Assets/js/components/autoload');
VueApp.start();

php artisan vendor:publish
```

para criar um novo módulo de store vuex basta adicionar o seguinte trecho de código no seu app.js asntes do 'VueApp.start();'
```
VueApp.appendStoreModule("upsell", require("./stores/modules/module_name.module").default);

// Exemplo de modulo 
// const state = {};

// const getters = {};

// const mutations = {};

// export default {
//     namespaced: true,
//     state,
//     getters,
//     mutations,
// };
```