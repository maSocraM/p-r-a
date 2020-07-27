# PHP-Rest-API (PRA)


## Propósito
Originalmente feita para um teste de uma oportunidade de emprego, onde a proposta foi a de criar uma API REST sem utilizar frameworks (PHP Vanilla), julguei ser um trabalho bem legal para ser evoluído e melhorado, tanto para ser utilizado como fins de estudo no entendimento e melhoria, tanto como fins profissionais (se julgado no nível deste propósito).

Está longe de ser um framework completo, até mesmo pelo fato de ter sido elaborado a partir de requisitos simples e para comprovação de conhecimentos em PHP, alguns design patterns e também buscando a filosofia *Clean Code*).

Existem inúmeras melhorias que podem ser feitas, que podem ser conferidas no ítem **"AQUI!"**.


## Instalação
Basta configurar um virtual host apontando para o subdiretório "public", se for utilizado um serviço diferente do Apache Web Server, como por exemplo o NGINX, é necessário configurar as regras de rewrite similares às existentes em **"public/.htaccess"**.


### Stack
    # PHP >= 7.x
    # Webserver (o Apache foi utilizado na criação deste)
    # Bibliotecas PHP:
        # zip
        # mysqli
        # pdo
        # pdo_mysql
        # gd
        # dom
        # json
        # xmlwriter
        # curl
        # sockets
        # exif
        # soap
        # xdebug (***FUNDAMENTAL!***)


## Configuração
O sistema de configuração é baseado em *arrays* e estas se encontram no arquivo **"app/Config.php"**, na variável **"$arr_config"**, que durante o dispatch da requisição fazem parte do conjunto de variáveis de ambiente que para serem acessadas é necessário utilizar a função:


```php
   getenv('<NOME_CONSTANTE>');
```

Onde ****<NOME_CONSTANTE>*** representa o nome dado na chave do *array*, lembrando que por se tratar de uma constante, o nome deve é convertido para maiúsculo, mesmo que a chave tenha sido digitada de outra forma.


### Banco de dados
Para configurar os dados de acesso a um banco de dados MySQL, basta alterar os valores das chaves, neste mesmo arquivo, com prefixo **"db_"**.


## Utilização


### Criação de rotas
O dispatcher utiliza a url como identificador do controller a ser chamado, como por exemplo, se for chadamo o endereço **"http://localhost/rota"** será feito uma busca pelo arquivo **"/app/controle/RotaController.php"**, facilitando assim a criação de novas rotas.

É possível obter maiores detalhes da estrutura de uma classe de controller no item **"AQUI!"**.

## Estrutura do framework
Baseado na organização e estrutura de arquivos utilizados em frameworks como Symfony e Laravel, a estrutura de diretórios é simples:

```
/app
    /controller
    /middleware
    /model
/public
```

Onde:
    - app => é o diretório principal da aplicação, onde se encontram demais diretórios e arquivos essenciais para o funcionamento do framework
    - controller => onde se encontram os arquivos de controle/rotas
    - middleware => aqui ficam os arquivos de core, que efetuam as operações internas (talvez não tenha sido um bom nome)
    - model => inicialmente pensado para armazenar os modelos de dados das tabelas, mas pelo propósito da época, consumiria muito tempo, então está somente a classe para controle de conexão com o banco de dados e querys
    - public => ponto inicial da aplicação


### Diretório Controller
Como já dito, neste diretório se encontram os arquivos de controle/rotas e a estrutura deve seguir o modelo:

```php
<?php

require_once getenv('SYSTEM_ROOT') . DIRECTORY_SEPARATOR . 'middleware/Response.php';
require_once getenv('SYSTEM_ROOT') . DIRECTORY_SEPARATOR . 'model/Db.php';
require_once getenv('SYSTEM_ROOT') . DIRECTORY_SEPARATOR . 'middleware/Util.php';
require_once getenv('SYSTEM_ROOT') . DIRECTORY_SEPARATOR . 'middleware/Security.php';


class UsersController
{
    protected $arr_fields = [];
    protected $arr_mandatory = [];

    public function __construct()
    {
        $this->arr_fields = ["campo1", "campo2", "campo3"]; // Lista de campos presentes neste controller
        $this->arr_mandatory = ["campo2", "campo3"]; // Lista contendo quais dos campos que são obrigatórios, validado pelo método 
    }

    // Recebe as requisições via método GET
    public function get($arr_params, $arr_body, $arr_headers)
    {
        ...
        $response = Response::message($int_http_code, $str_message, $page, $records, $total, $ret);
        return Response::create($int_http_code, $response);
    }

    // Recebe as requisições via método POST
    public function post($arr_params, $arr_body, $arr_headers)
    {
        ...
        $response = Response::message($int_http_code, $str_message, $page, $records, $total, $ret);
        return Response::create($int_http_code, $response);
    }
    
    // Recebe as requisições via método PUT
    public function put($arr_params, $arr_body, $arr_headers)
    {
        ...
        $response = Response::message($int_http_code, $str_message, $page, $records, $total, $ret);
        return Response::create($int_http_code, $response);
    }
    
    // Recebe as requisições via método DELETE
    public function delete($arr_params, $arr_body, $arr_headers)
    {
        ...
        $response = Response::message($int_http_code, $str_message, $page, $records, $total, $ret);
        return Response::create($int_http_code, $response);
    }
```

Desta forma, cada requisição que é feita ao controller, necessita um método de mesmo nome, por exemplo o método 'get' irá receber todas as requisições feita neste controller pelo método HTTP GET.

### Diretório **/** (raiz)
Os arquivos de base e essenciais para início da aplicação se encontram aqui:
    - **Bootstrap.php:** Responsável pelo início da aplicação, chamando todas as classes necessárias para isso
    - **Config.php:** Armazena todas as configurações iniciais do sistema

### Diretório **/public**
Ponto de início da aplicação, onde existe um arquivo **".htaccess"** para configuração de *"URL Amigável"* (conhecido como *mod_rewrite*) e o arquivo **"index.php"** responsável por iniciar o processo de *bootstrap*.
    

### Diretório **/app/middleware**
É neste diretório que se encontram os arquivos fundamentais para funcionamento do framework:
    - **Dispatcher.php:** Como o nome diz, efetua o carregamento classe de controlle, baseado na URL acessada
    - **Parameters.php:** Responsável por recuperar os parâmetros recebidos em formato URL, seguindo a seguinte lógica: **"http://localhost/<controller>/<parâmetro1>/<parâmetro2>/..."**
    - **Request.php:** Recupera a requisição recebida e a transforma de forma legível para ser consumida pelo controller
    - **Response.php:** Formata a resposta para ser enviada
    - **Routes.php:** Efetua a validação da existência dos arquivos controllers e métodos dentro destes
    - **Security.php**: Tem a função de criar e validar tokens JWT
    - **Util.php**: Classe utilitária com métodos estáticos de uso geral em todo o sistema

    
### Diretório /app/model
Neste diretório se encontra a classe **"Db.php"**, responsável pela conexão e desconexão com o banco de dados e também pela execução de queries


## Futuro
O início deste projeto, como já dito, foi para fins de avaliação de conhecimentos por parte da empresa em um teste, buscando formas simples de resolver o problema proposto.
Inicialmente não tenho intenções de inciar o processo de evolução da ideia, até mesmo porque já existem inúmeros frameworks no mercado, mas penso em utilizar esta estrutura para avançar em estudos, não somente do PHP, mas também da implementação de design patterns.


## Possíveis melhorias
Embora não seja ideia evoluir a ideia, é interessante utilizar esta estrutura para implementar e estudar ítens como:
    - Mais aderente aos design patterns, como: MVVM, singleton, injeção de dependências, entre outros;
    - Utilizar fortemente os conceitos como o SOLID e o Clean Code;
    - Criação e utilização de models mais precisos e multiuso, fazendo o modelo de tabelas ou mesmo de serviços externos;
    - Reestruturação e nomeação mais intuitiva das camadas e classes da aplicação;
    - Entre outros.
    
*Talvez* algum dia eu comece a melhorar desta forma, mas se você chegou até aqui, a licença é GPL, sinta-se a vontade em fazer pull-requests ou mesmo forks e começar os seus próprios estudos!
