# ⚡ MyTreino

Aplicação web desenvolvida para ajudar iniciantes na academia a organizar e acompanhar os treinos da semana.

## Tecnologias utilizadas

- PHP 8
- MySQL
- HTML5
- CSS3
- JavaScript
- Bootstrap 5

---

## Estrutura do projeto

```text
mytreino/
├── assets/
│   ├── css/
│   │   └── style.css
│   └── js/
│       └── app.js
├── config/
│   └── db.php
├── database/
│   └── database.sql
├── includes/
│   ├── auth.php
│   ├── header.php
│   ├── footer.php
│   └── helpers.php
├── index.php
├── login.php
├── registro.php
├── logout.php
├── perfil.php
├── treinos.php
├── treino_form.php
├── treino_excluir.php
├── exercicios.php
├── exercicio_form.php
└── exercicio_excluir.php
```

---

## Como executar o projeto

### 1. Instale o XAMPP

Faça o download do XAMPP e inicie os serviços **Apache** e **MySQL**.

### 2. Copie o projeto

Coloque a pasta `mytreino` dentro da pasta `htdocs`.

Exemplo no Windows:

```text
C:\xampp\htdocs\mytreino\
```

### 3. Crie o banco de dados

1. Abra o phpMyAdmin.
2. Clique na aba **SQL**.
3. Abra o arquivo `database/database.sql`.
4. Copie o conteúdo, cole no phpMyAdmin e execute.

Também é possível importar diretamente o arquivo `database.sql` pela opção **Importar**.

### 4. Verifique a conexão

As configurações padrão do XAMPP são:

```text
Usuário: root
Senha:
Banco: mytreino
```

Se o seu MySQL possuir senha, altere as informações no arquivo `config/db.php`.

### 5. Execute o sistema

Abra o navegador e acesse:

```text
http://localhost/mytreino/login.php
```

Cadastre um usuário e comece a utilizar o sistema.

---

## Funcionalidades

- Cadastro e login de usuários.
- Perfil com peso, altura e idade.
- Organização dos treinos por dia da semana.
- Cadastro, edição e exclusão de treinos.
- Cadastro, edição e exclusão de exercícios.
- Página inicial exibindo o treino do dia.
- Validação dos formulários em HTML, JavaScript e PHP.
- Consultas utilizando PDO com prepared statements.
- Interface responsiva para computadores e dispositivos móveis.

---

## Segurança

- Senhas armazenadas com `password_hash`.
- Verificação de login utilizando sessões.
- Proteção contra SQL Injection com Prepared Statements.
- Saídas tratadas com `htmlspecialchars`.
- Exclusão de registros somente por requisição POST.
- Cada usuário possui acesso apenas aos próprios dados.
