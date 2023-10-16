## Inicializar


1 - Após baixar e extrair o arquivo vai abrir o arquivo .env que está na pasta \ba-support\.env e ver se está correto os dados para conexão com o banco de dados.

```sh
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=ba_support
DB_USERNAME=root
DB_PASSWORD=
```

2 - Crie o seguinte banco de dados:

```sh
CREATE DATABASE ba_support
```

3 - Vai abrir prompt de comando e direcionar para a pasta que baixou o arquivo usando comando "cd", ao chegar na pasta raiz que é 
"ba-support" execute o comando a seguir para baixar as dependências:
```sh
composer update
```

```sh
npm install
```
logo após o termino execute o comando a seguir para gerar as tabelas:

```sh
php artisan migrate
```

4 - Se você instalou o PHP localmente e gostaria de usar o servidor de desenvolvimento embutido do PHP para servir a sua aplicação, você pode usar o php artisan serve. Este comando irá iniciar um servidor de desenvolvimento em http://localhost:8000

```sh
php artisan serve
```

se caso usar xamp coloque dentro da pasta htdocs. execute apache e mysql.

http://127.0.0.1/ba-support/public/

se caso usar wammp coloque dentro da pasta www. execute apache e mysql.

http://127.0.0.1/ba-support/public/


Para compilar os arquivos do Vue.js, utilize o seguinte comando:

```sh
npm run watch
```








