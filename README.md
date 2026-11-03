# Sistema de Gerenciamento de Livros

Aplicação web desenvolvida em **PHP 8.4 e Symfony 7.4** para cadastro e gerenciamento de livros, autores e assuntos, com relatório de livros agrupados por autor e exportação para PDF.

## Tecnologias

- PHP 8.4 e Symfony 7.4
- Doctrine ORM e Doctrine Migrations
- MySQL 8
- Twig e Bootstrap 5
- Dompdf para geração de relatórios em PDF
- Docker Compose (PHP-FPM, Nginx e MySQL)

## Funcionalidades

- Cadastro, listagem, visualização, edição e exclusão de livros, autores e assuntos.
- Associação de múltiplos autores e múltiplos assuntos a um livro.
- Validação de campos obrigatórios, limites de tamanho, valores positivos e vínculos obrigatórios.
- Bloqueio da exclusão de autores ou assuntos associados a livros.
- Exclusão de livros sem remover os autores e assuntos cadastrados.
- Mensagens de confirmação e erro e proteção CSRF nas exclusões.
- Relatório de livros agrupados por autor, disponível em HTML e PDF.
- Interface responsiva com Bootstrap 5.

## Modelo de dados

O banco possui cinco tabelas principais:

| Tabela | Finalidade |
| --- | --- |
| `Livro` | Título, editora, edição, ano de publicação e valor. |
| `Autor` | Cadastro de autores. |
| `Assunto` | Cadastro de assuntos. |
| `Livro_Autor` | Relacionamento muitos-para-muitos entre livros e autores. |
| `Livro_Assunto` | Relacionamento muitos-para-muitos entre livros e assuntos. |

A VIEW **`vw_relatorio_livros`** reúne os dados necessários ao relatório. Sua criação faz parte das migrations do projeto. O relatório agrupa os resultados por autor e consolida os assuntos de cada livro, evitando linhas duplicadas no resultado apresentado.

## Pré-requisitos

- Docker Desktop com Docker Compose
- Git (opcional, para clonar o repositório)

Não é necessário instalar PHP, Composer ou MySQL diretamente no computador.

## Como executar

### 1. Obter o projeto

Clone o repositório ou extraia os arquivos disponibilizados e abra um terminal na pasta do projeto.

### 2. Configurar o ambiente

Crie o arquivo **`.env.local`** na raiz do projeto com a conexão abaixo, ajustando usuário e senha para os valores configurados no serviço `db` do `docker-compose.yml`:

```dotenv
DATABASE_URL="mysql://livros_user:livros_password@db:3306/projeto_livros?serverVersion=8.0.32&charset=utf8mb4"
```

> A configuração acima usa credenciais **exclusivamente locais de desenvolvimento**. Não reutilize senhas reais ou de produção.

### 3. Iniciar os containers

```bash
docker compose up -d --build
```

### 4. Instalar dependências PHP

```bash
docker compose exec app composer install
```

### 5. Criar o esquema do banco e a VIEW

```bash
docker compose exec app php bin/console doctrine:migrations:migrate --no-interaction
```

Esse comando aplica as migrations de criação das tabelas e da VIEW de relatório.

### 6. Acessar a aplicação

Abra **http://localhost:8080** no navegador.

> O banco inicia sem dados de exemplo. Cadastre autores e assuntos antes de cadastrar livros e gerar o relatório.

## Páginas disponíveis

| Funcionalidade | Endereço |
| --- | --- |
| Página inicial | `http://localhost:8080/` |
| Livros | `http://localhost:8080/livro/` |
| Autores | `http://localhost:8080/autor/` |
| Assuntos | `http://localhost:8080/assunto/` |
| Relatório HTML | `http://localhost:8080/relatorio/livros` |
| Relatório PDF | `http://localhost:8080/relatorio/livros/pdf` |

## Relatório

A consulta é realizada por meio da VIEW MySQL **`vw_relatorio_livros`**, usando Doctrine DBAL. Os registros são agrupados por autor e por livro; os assuntos de cada livro são consolidados para exibição.

A versão HTML utiliza Twig e Bootstrap. A versão PDF utiliza um template Twig específico e **Dompdf**, em formato A4 paisagem, com valores monetários no padrão brasileiro.

Um livro associado a mais de um autor aparece no grupo de cada autor correspondente.

## Verificações técnicas

```bash
docker compose exec app php bin/console about
docker compose exec app php bin/console doctrine:schema:validate
docker compose exec app php bin/console doctrine:migrations:status
docker compose exec app php bin/console lint:twig templates/
docker compose exec app php bin/console lint:container
```

## Estrutura do projeto

```text
src/
  Controller/       Controllers e rotas
  Entity/           Entidades e relacionamentos Doctrine
  Form/             Formulários Symfony
migrations/         Criação das tabelas e da VIEW
config/             Configurações Symfony e Doctrine
templates/          Interfaces Twig e template do PDF
public/             Ponto de entrada web
Dockerfile          Imagem PHP da aplicação
docker-compose.yml  Serviços de aplicação, Nginx e MySQL
```

## Observações

- A aplicação foi desenvolvida para execução local por Docker Compose.
- O arquivo `.env.local`, dependências instaladas (`vendor/`) e arquivos temporários (`var/`) não devem ser versionados.
- O relatório depende da VIEW criada pelas migrations.
