# Vstack Models

Lembrando que a tabela deve ser criada normalmente utilizando as migrations do laravel e o model seguindo o padrão abaixo:<br>
- Estendendo da classe  marcusvbda\vstack\Models\DefaultModel;
- Declarando em $table o nome da tabela;
- Adicionando o escopo global OrderByScope, como no exemplo para que seu model nativamente traga todo resultado do forma descendente ( desnecessário em caso de models que não vão ter listagem );
- Em $cascadeDeletes definir o nome do relacionamento que irá excluir os itens relacionados ( SoftDelete ou não ) 
- Em $restrictDeletes definir o nome do relacionamento que caso tenha algum item relacionado ao objeto em questão vai impedir a exclusão;
- $restrictDeletes pode-se declarar também definindo a mensagem de restrição customizada (comentada no exemplo abaixo)
- Função estática hasTenant vai definir que se o model deve passar por escopo de tenant ou não ( default true ), neste caso do exemplo está definido como não pois o exemplo é um model tenant, em model de usuário por exemplo, deve-se deixar true ( default ) pois assim apenas os usuários com tenant_id igual ao tenant logado serão visualizados.

#### Executando o comando de criação de resource um model no padrão correto é criado automaticamente;

[Leia mais sobre Vstack Resources](RESOURCES.md)
<br>
<br>

```
<?php

namespace App\Http\Models;

use marcusvbda\vstack\Models\DefaultModel;
use App\User;
use App\Http\Models\Scopes\OrderByScope;
class Tenant extends DefaultModel
{
	protected $table = "tenants";
	// public $cascadeDeletes = [];
	public $restrictDeletes = ["users"];
    // public $restrictDeletes = [
	// 	"users" => "Não é possível excluir esse tenant, pois está vinculado a um ou mais usuários"
	// ];

    public static function hasTenant() 
	{
		return false;
	}

	public $casts = [
		"data" => "object",
	];

	public static function boot()
	{
		parent::boot();
		static::addGlobalScope(new OrderByScope(with(new static)->getTable()));
	}

    public function users()
	{
		return $this->hasMany(User::class);
	}
}

```
