# Projeto Laravel - Guia de Instalação e Uso

Este projeto utiliza o framework Laravel para o back-end e integra um front-end configurado. Este guia explica como instalar, configurar e iniciar o projeto.

## Pré-requisitos:

* PHP (>= 8.1) com as extensões necessárias.
* Composer (gerenciador de dependências do PHP).
* Node.js (>= 16.x) e npm ou yarn para gerenciar pacotes do front-end.
* Banco de Dados (MySQL/PostgreSQL ou outro compatível com Laravel).

## Passos de Instalação:

~~~front-end
    npm install
~~~

~~~back-end
    composer install
~~~

## Gerar Chave da Aplicação:

~~~php
php artisan key:generate
~~~

## Configurar o Banco de Dados

~~~.env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=nome_do_banco
DB_USERNAME=usuario
DB_PASSWORD=senha
~~~

## Executar as migrações:

```php
    php artisan migrate
```

## Popular o Banco de Dados ( Opcional )

~~~php
    php artisan db:seed
~~~

#Inicialização do Projeto

~~~front-end
    npm run dev
~~~

~~~back-end
    php artisan serve
~~~

Se encontrar problemas, verifique a documentação oficial do Laravel ou abra uma issue no repositório do projeto.

🚀 Bom desenvolvimento e obrigado pela oportunidade!

