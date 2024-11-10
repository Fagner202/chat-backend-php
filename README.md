# Chat Backend PHP com Docker, MySQL e WebSocket

Este projeto é uma aplicação web desenvolvida em **PHP** que utiliza **Docker** para containerização, **MySQL** como banco de dados e **Ratchet** para comunicação em tempo real via **WebSocket**. A aplicação inclui funcionalidades de **autenticação JWT**, **gerenciamento de usuários** e um **sistema de chat em tempo real**.

## 🛠️ Tecnologias Utilizadas

- **PHP 8.2 com Apache**: Linguagem de programação e servidor web.
- **MySQL 8**: Banco de dados relacional.
- **Composer**: Gerenciador de dependências para PHP.
- **Ratchet**: Biblioteca para implementação de WebSockets.
- **Firebase JWT**: Autenticação via JSON Web Token.
- **Docker e Docker Compose**: Containerização e orquestração.

## 🚀 Como Iniciar o Projeto

### Pré-requisitos

Antes de começar, certifique-se de ter as seguintes ferramentas instaladas:

- **Docker** e **Docker Compose**.
- **Git** (opcional, para clonar o repositório).

### Passo 1: Clonar o Repositório

Para obter o código do projeto, execute os seguintes comandos:

```bash
git clone https://github.com/Fagner202/chat-backend-php.git
cd chat-backend-php
```

### Passo 2: Construir e Iniciar os Containers
```bash	
docker compose up -d
```
Construir as imagens Docker.
Iniciar os containers conforme definido no arquivo docker-compose.yml.

### Passo 3: Instalar Dependências do Composer

Acesse o terminal do container da aplicação:

```bash	
docker exec -it php-app bash
```	
Dentro do container, instale as dependências do projeto

```bash	
composer install
```

Após a instalação, saia do container

```bash	
exit
```

### Passo 4: Migrar o Banco de Dados

Crie as tabelas necessárias no banco de dados. Isso pode ser feito utilizando scripts SQL ou ferramentas de migração.

### Passo 5: Passo 6: Iniciar o Servidor de Chat

```bash	
docker exec -it php-app bash
```

O servidor estará rodando na porta 8081.

## 📚 Endpoints da API
Autenticação
POST /api/login
Realiza a autenticação de um usuário.

POST /api/register
Registra um novo usuário.

Usuários
GET /api/users
Lista todos os usuários cadastrados.

PUT /api/users/update
Atualiza as informações de um usuário.

DELETE /api/users/delete
Remove um usuário do sistema.

## ⚙️ Funcionalidades Principais
Autenticação JWT
Garante a segurança nas requisições através de tokens.

Gerenciamento de Usuários
Permite a criação, leitura, atualização e exclusão de usuários.

Chat em Tempo Real
Sistema de chat baseado em WebSockets, com suporte à criação de salas e comunicação entre múltiplos usuários.

Containerização com Docker
Facilita a implantação e escalabilidade, garantindo a consistência do ambiente de desenvolvimento e produção.

## 📝 Observações
Certifique-se de que as portas 8080 e 8081 estão disponíveis em sua máquina antes de iniciar os containers.
Os dados do banco de dados serão armazenados em um volume Docker chamado db_data, garantindo a persistência.
Personalize as chaves secretas e credenciais conforme necessário no arquivo .env.