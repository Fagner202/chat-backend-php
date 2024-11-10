Projeto PHP com Docker, MySQL e WebSocket
Este projeto é uma aplicação web em PHP que utiliza Docker para containerização, MySQL como banco de dados e Ratchet para comunicação em tempo real via WebSocket. A aplicação inclui autenticação JWT, gerenciamento de usuários e um sistema de chat em tempo real.

Tecnologias Utilizadas
PHP 8.2 com Apache
MySQL 8
Composer para gerenciamento de dependências PHP
Ratchet para implementação de WebSockets em PHP
Firebase JWT para autenticação JSON Web Token
Docker e Docker Compose para containerização
Como Iniciar o Projeto
Siga os passos abaixo para configurar e executar o projeto:

Pré-requisitos:

Ter o Docker e o Docker Compose instalados em sua máquina.
Opcionalmente, ter o Git instalado para clonar o repositório.
Clonar o Repositório:

git clone [https://github.com/Fagner202/chat-backend-php.git]
cd [chat-backend-php]

Configurar Variáveis de Ambiente:

Crie um arquivo .env se necessário, contendo as configurações de ambiente, como credenciais do banco de dados e chaves secretas para JWT.
Construir e Iniciar os Containers:

No diretório do projeto, execute:
docker-compose up -d

Isso construirá as imagens e iniciará os serviços definidos no docker-compose.yml.
Instalar Dependências do Composer:

Acesse o terminal do container da aplicação:
docker exec -it php-app bash

Dentro do container, instale as dependências:
composer install

Saia do container:
exit

Migrar o Banco de Dados:

Configure o banco de dados, criando as tabelas necessárias. Isso pode ser feito via scripts SQL ou utilizando ferramentas de migração.
Iniciar o Servidor de Chat:

docker exec -it php-app php chat-server.php

O servidor estará escutando na porta 8081.
Acessar a Aplicação:

A aplicação web estará disponível em http://localhost:8080.
Endpoints da API
POST /api/login: Autenticação de usuários.
POST /api/register: Registro de novos usuários.
GET /api/users: Listar usuários cadastrados.
PUT /api/users/update: Atualizar informações de um usuário.
DELETE /api/users/delete: Remover um usuário.

Funcionalidades Principais
Autenticação JWT: Segurança nas requisições através de tokens JWT.
Gerenciamento de Usuários: Criação, leitura, atualização e exclusão de usuários.
Chat em Tempo Real: Sistema de chat utilizando WebSockets, permitindo criação de salas e comunicação entre múltiplos usuários.
Containerização com Docker: Fácil implantação e escalabilidade através de containers Docker.
Observações
Certifique-se de que as portas 8080 e 8081 não estejam sendo utilizadas por outros serviços em sua máquina.
Os dados do banco de dados serão persistidos no volume db_data, definido no docker-compose.yml.
Personalize as configurações conforme necessário, especialmente as chaves secretas e credenciais.