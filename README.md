# Task API — Laravel REST + GraphQL

![PHP](https://img.shields.io/badge/PHP-8.3+-777BB4?style=flat&logo=php&logoColor=white)
![Laravel](https://img.shields.io/badge/Laravel-13.x-FF2D20?style=flat&logo=laravel&logoColor=white)
![GraphQL](https://img.shields.io/badge/GraphQL-Lighthouse-E10098?style=flat&logo=graphql&logoColor=white)
![MySQL](https://img.shields.io/badge/MySQL-8.x-4479A1?style=flat&logo=mysql&logoColor=white)
![License](https://img.shields.io/badge/license-MIT-green)

> API de gerenciamento de tarefas com Laravel 13, oferecendo **REST + GraphQL** sobre o mesmo domínio, autenticação por **Bearer Token (Sanctum)** e **Service Layer compartilhado**.

---

## 📸 Screenshots

### Documentação da API — Scramble + Stoplight Elements

[![Documentação da API](https://i.imgur.com/QMDqRix.png)](https://i.imgur.com/QMDqRix.png)

### GraphiQL Playground — Queries e Mutations

[![GraphiQL](https://i.imgur.com/9vcYKjL.png)](https://i.imgur.com/9vcYKjL.png)

### Testes automatizados — 11 passing

[![Testes](https://i.imgur.com/HefZa4E.png)](https://i.imgur.com/HefZa4E.png)

---

## 📋 Índice

- [Sobre o projeto](#-sobre-o-projeto)
- [Tecnologias](#-tecnologias)
- [Arquitetura](#-arquitetura)
- [Requisitos](#-requisitos)
- [Instalação](#-instalação)
- [Uso da API REST](#-uso-da-api-rest)
- [Uso da API GraphQL](#-uso-da-api-graphql)
- [Autenticação](#-autenticação)
- [Documentação interativa](#-documentação-interativa)
- [Testes](#-testes)
- [Estrutura do projeto](#-estrutura-do-projeto)
- [Boas práticas aplicadas](#-boas-práticas-aplicadas)
- [Licença](#-licença)

---

## 🎯 Sobre o projeto

Este projeto foi desenvolvido para demonstrar boas práticas de arquitetura em Laravel, oferecendo **duas interfaces de API em paralelo** (REST e GraphQL) sobre o mesmo domínio de negócio.

Principais características:

- **Autenticação por token** usando Laravel Sanctum
- **CRUD completo de Tarefas** vinculadas ao usuário autenticado
- **Service Layer** compartilhada entre REST e GraphQL, evitando duplicação
- **API Resources** para padronizar as respostas JSON
- **Factories e Seeders** para popular o banco
- **Testes automatizados** cobrindo autenticação, autorização e CRUD
- **Documentação OpenAPI** gerada automaticamente via Scramble
- **GraphiQL Playground** para testar queries e mutations GraphQL

---

## 🛠 Tecnologias

| Tecnologia | Versão | Uso |
|-----------|--------|-----|
| PHP | 8.3+ | Linguagem base |
| Laravel | 13.x | Framework principal |
| Laravel Sanctum | 4.x | Autenticação por token |
| Lighthouse | 6.x | Servidor GraphQL |
| mll-lab/laravel-graphiql | 2.x | Playground GraphQL |
| dedoc/scramble | 0.12+ | Documentação OpenAPI automática |
| MySQL | 8.x | Banco de dados |
| PHPUnit | 11.x | Testes automatizados |

---

## 🏗 Arquitetura

O projeto segue uma arquitetura em camadas que permite **REST e GraphQL compartilharem a mesma lógica de negócio**:

```
┌──────────────────┐        ┌──────────────────┐
│   REST API       │        │   GraphQL API    │
│ (TaskController) │        │  (Mutations)     │
└────────┬─────────┘        └────────┬─────────┘
         │                           │
         └─────────────┬─────────────┘
                       ▼
              ┌──────────────────┐
              │   TaskService    │  ← lógica de negócio
              └────────┬─────────┘
                       ▼
              ┌──────────────────┐
              │  Model (Task)    │  ← persistência
              └──────────────────┘
```

**Vantagens:**

- Uma única fonte de verdade para as regras de negócio
- Autorização e validação centralizadas
- Fácil de testar
- Novos "canais" (CLI, filas, WebSocket) podem reutilizar o mesmo Service

---

## ✅ Requisitos

- PHP 8.3 ou superior
- Composer 2.x
- MySQL 8.x (ou MariaDB 10.6+)

---

## 🚀 Instalação

### 1. Clonar o repositório

```bash
git clone https://github.com/seu-usuario/task-api.git
cd task-api
```

### 2. Instalar dependências

```bash
composer install
```

### 3. Copiar o arquivo de ambiente

```bash
cp .env.example .env
```

### 4. Gerar a chave da aplicação

```bash
php artisan key:generate
```

### 5. Configurar o banco de dados

Edite o `.env` com suas credenciais:

```env
DB_CONNECTION=mysql
DB_DATABASE=task_api
DB_USERNAME=root
DB_PASSWORD=
```

Crie o banco:

```sql
CREATE DATABASE task_api CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

### 6. Rodar as migrations

```bash
php artisan migrate
```

### 7. (Opcional) Popular com dados de exemplo

```bash
php artisan db:seed
```

Cria **5 usuários**, cada um com **3 tarefas**.

### 8. Iniciar o servidor

```bash
php artisan serve
```

Acesse em `http://localhost:8000`.

---

## 🌐 Uso da API REST

**Base URL**: `http://localhost:8000/api`

### Endpoints

| Método | Endpoint | Descrição | Autenticado |
|--------|----------|-----------|:---:|
| POST | `/register` | Cria um novo usuário | ❌ |
| POST | `/login` | Autentica e retorna token | ❌ |
| POST | `/logout` | Revoga o token atual | ✅ |
| GET | `/user` | Retorna usuário autenticado | ✅ |
| GET | `/tasks` | Lista tarefas do usuário | ✅ |
| POST | `/tasks` | Cria uma nova tarefa | ✅ |
| GET | `/tasks/{id}` | Detalha uma tarefa | ✅ |
| PUT | `/tasks/{id}` | Atualiza uma tarefa | ✅ |
| DELETE | `/tasks/{id}` | Remove uma tarefa | ✅ |

### Exemplo: registrar usuário

```bash
curl -X POST http://localhost:8000/api/register \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "name": "João Silva",
    "email": "joao@exemplo.com",
    "password": "senha12345"
  }'
```

**Resposta (201):**

```json
{
  "user": { "id": 1, "name": "João Silva", "email": "joao@exemplo.com" },
  "access_token": "1|abcdef123456...",
  "token_type": "Bearer"
}
```

### Exemplo: criar tarefa

```bash
curl -X POST http://localhost:8000/api/tasks \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -H "Authorization: Bearer SEU_TOKEN_AQUI" \
  -d '{"title": "Minha primeira tarefa", "description": "Testando a API"}'
```

**Resposta (201):**

```json
{
  "data": {
    "id": 1,
    "title": "Minha primeira tarefa",
    "description": "Testando a API",
    "completed": false,
    "user": { "id": 1, "name": "João Silva" },
    "created_at": "2026-09-24T18:00:00+00:00",
    "updated_at": "2026-09-24T18:00:00+00:00"
  }
}
```

---

## 🧬 Uso da API GraphQL

**Endpoint**: `http://localhost:8000/graphql`
**Playground**: `http://localhost:8000/graphiql`

### Queries

```graphql
type Query {
  me: User
  users: [User!]!
  user(id: ID!): User
  tasks: [Task!]!
  task(id: ID!): Task
}
```

### Mutations

```graphql
type Mutation {
  createTask(input: TaskInput!): Task
  updateTask(id: ID!, input: TaskUpdateInput!): Task
  deleteTask(id: ID!): Task
}
```

### Exemplo: criar tarefa via GraphQL

```graphql
mutation {
  createTask(input: {
    title: "Tarefa via GraphQL",
    description: "Criada pelo GraphiQL"
  }) {
    id
    title
    completed
    created_at
  }
}
```

### Autenticar no GraphiQL

1. Faça login via REST e copie o `access_token`
2. No GraphiQL, abra a aba **Headers**
3. Adicione:

```json
{
  "Authorization": "Bearer SEU_TOKEN_AQUI"
}
```

---

## 🔐 Autenticação

O projeto usa **Laravel Sanctum** com autenticação por **Bearer Token**.

### Fluxo

1. **Registrar** → `POST /api/register`
2. **Login** → `POST /api/login`
3. **Usar o token** em todas as requisições:

```
Authorization: Bearer 1|abcdef123456...
```

---

## 📖 Documentação interativa

### REST — Scramble + Stoplight Elements

```
http://localhost:8000/docs/api
```

Interface completa com:

- Todos os endpoints documentados
- Exemplos de request/response
- Esquema de segurança **Bearer** aplicado automaticamente
- Botão **Authorize** para inserir o token
- Botão **Try it out** para testar direto do navegador

### GraphQL — GraphiQL Playground

```
http://localhost:8000/graphiql
```

Interface interativa com autocomplete, documentação inline do schema e histórico de queries.

---

## 🧪 Testes

```bash
php artisan test
```

**Cobertura atual:**

- ✅ Requisição sem token retorna 401
- ✅ Listagem retorna apenas as tarefas do usuário
- ✅ Criação de tarefa com dados válidos
- ✅ Validação de título obrigatório (422)
- ✅ Visualização da própria tarefa
- ✅ Bloqueio ao visualizar tarefa de outro (403)
- ✅ Atualização da própria tarefa
- ✅ Deleção da própria tarefa
- ✅ Bloqueio ao deletar tarefa de outro

**Resultado:** 11 testes passando, 24 assertions.

---

## 📁 Estrutura do projeto

```
task-api/
├── app/
│   ├── Http/
│   │   ├── Controllers/Api/
│   │   │   ├── AuthController.php       # Registro, login, logout
│   │   │   └── TaskController.php       # CRUD REST
│   │   └── Resources/
│   │       ├── TaskResource.php
│   │       └── TaskCollection.php
│   ├── GraphQL/Mutations/
│   │   ├── CreateTask.php
│   │   ├── UpdateTask.php
│   │   └── DeleteTask.php
│   ├── Models/
│   │   ├── User.php
│   │   └── Task.php
│   └── Services/
│       └── TaskService.php
├── config/
│   ├── auth.php
│   ├── lighthouse.php
│   └── scramble.php
├── database/
│   ├── factories/
│   ├── migrations/
│   └── seeders/
├── graphql/
│   └── schema.graphql
├── routes/
│   └── api.php
├── tests/
│   └── Feature/Api/
│       └── TaskControllerTest.php
├── .env.example
├── .env.testing
├── phpunit.xml
└── README.md
```

---

## 🏅 Boas práticas aplicadas

- **Service Layer** — `TaskService` centraliza a lógica de negócio
- **API Resources** — respostas padronizadas e seguras
- **Route Model Binding** — resolve `Task` automaticamente
- **Validação em duas camadas** — Request + Rules do GraphQL
- **Dependency injection** — constructor property promotion
- **Autorização centralizada** — ownership check no Service
- **Test isolation** — banco de teste separado
- **Documentação automática** — Scramble gera OpenAPI do código
- **Compatibilidade com Laravel 13** — guards e serialização ajustados

---

## 📄 Licença

Este projeto está sob a licença MIT.

---

## 👤 Autor

**Seu Nome**

- GitHub: [@silas000](https://github.com/Silas000)
- LinkedIn: [Silas Rosário](https://linkedin.com/in/silas-rosario/)
