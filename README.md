# Vstack Docs 

<b>Vstack</b> é uma biblioteca desenvolvida as bases do framework LARAVEL baseada nas features oferecidas pelo [Laravel Nova](https://nova.laravel.com/docs/)

### Getting Started
- [Instalação Via Composer](docs/INSTALATION.md)

### Recursos
- [Vstack Models](docs/MODELS.md)
- [Vstack Resources](docs/RESOURCES.md)
- [Vstack Inputs](docs/INPUTS.md)
- [Vstack Socket](docs/SOCKET.md)

# Queues
Para o funcionamento completo do sistema é necessário que as filas abaixo estejam sendo executadas;
<br>

```
php artisan queue:work --queue=resource-import,resource-export,alert-broadcasts,event-broadcasts
```
