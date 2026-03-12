# MissasPE - Backend

API REST em Laravel para gerenciamento de paróquias, igrejas e horários de missas.

## 🚀 Como Rodar

### 1. Configurar .env
Antes de qualquer coisa, crie seu arquivo de ambiente:

Abra o arquivo `.env` e configure as credenciais do seu banco **PostgreSQL**:
```env
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=MissasPE
DB_USERNAME=seu_usuario
DB_PASSWORD=sua_senha
```

### 2. Configuração Rápida
O comando abaixo instala as dependências, gera a chave de segurança e executa as migrações:
```bash
composer run setup
```

### 2. Executar o Servidor
```bash
php artisan serve
```
Acesse em: `http://127.0.0.1:8000`

---

## 🐳 Rodando com Docker
Se preferir usar Docker:
```bash
docker build -t MissasPE-backend .
docker run -d -p 8080:80 --env-file .env --name MissasPE-api MissasPE-backend
```

---

## 🗄️ Banco de Dados (Manual)
Se precisar criar a estrutura manualmente via SQL:
[database/schema.sql]