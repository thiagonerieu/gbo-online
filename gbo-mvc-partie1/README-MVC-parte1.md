# GameBook Organiser — MVC, parte 1

Núcleo MVC + autoloader + cadastro / login / logout + página `my-projects`.
Esta parte contém **somente arquivos novos**: descompacte na raiz do repositório (`gbo-online/`).

## Requisitos
- PHP 8.1 ou superior, com as extensões `pdo_mysql` e `mbstring` (Laragon já traz).
- MySQL 8.x com o banco `gamebook_organiser` importado (`database/gamebook_organiser.sql`, v1.3).
- Apache com `mod_rewrite` (padrão no Laragon).

## Instalação (7 passos)
1. Descompacte o zip na raiz do repositório.
2. Crie o usuário do aplicativo: edite a senha em `database/create_app_user.sql` e execute-o como `root`.
3. Copie `config/config.example.php` para `config/config.php` e preencha `user` e `password`.
   **Antes disso, coloque `config/config.php` no `.gitignore`.**
4. Aponte o host virtual para a pasta `public/` (Laragon: Menu > Apache > Sites).
   Se não conseguir, copie `htaccess-root.example` para `.htaccess` (raiz) em substituição ao atual.
5. Abra `http://gbo-online.test/` : deve redirecionar para `/login`.
6. Entre com uma conta de teste (`alice@example.com` / `Alice#2026`) ou crie uma em `/signup`.
7. Apague as páginas antigas que viraram rotas quando você terminar de migrar o HTML.

## Rotas
| Método | URL | Ação |
|---|---|---|
| GET | `/` | redireciona para `/login` ou `/my-projects` |
| GET / POST | `/login` | formulário e autenticação |
| GET / POST | `/signup` | formulário e criação de conta (login automático) |
| POST | `/logout` | encerra a sessão |
| GET | `/account-recovery` | página provisória (parte 4) |
| GET | `/my-projects` | lista os projetos do autor |
| POST | `/my-projects` | cria um projeto (e a entry nº 1) |
| POST | `/my-projects/{project_id}/delete` | exclui o projeto (cascata) |

## Como integrar o seu HTML
As views em `app/views/` são **iniciais, com HTML mínimo**: substitua o HTML pelo seu e mantenha só:
- `<form method="post" action="<?= e(url('/login')) ?>">` e `<?= csrf_field() ?>` em todo formulário POST;
- os nomes dos campos (`email`, `password`, `first_name`, `last_name`, `nickname`, `password_confirm`, `name`, `language`);
- `old('campo')` e `field_error('campo')` para reexibir valores e erros;
- nos layouts: `<?= $content ?>`, o partial `flash`, `page_style()` / `page_script()`.
O layout carrega `styles/variables.css`, `styles/style.css` e, para cada página, `styles/<page>.css` e `scripts/<page>.js`
(`auth-login`, `auth-signup`, `app-my-projects`...) **somente se o arquivo existir**.

## Convenções
- Controllers finos; models só com SQL (PDO, requisições preparadas); services com as regras de negócio; views só com HTML.
- Toda saída passa por `e()` (anti-XSS). Todo POST exige o token CSRF (verificado no `Router`).
- Pastas em minúsculas; o arquivo de uma classe tem o nome exato dela (`UserModel.php`).
- Mensagens da interface em francês (centralizadas nos controllers/services).

## Testado (PHP 8.3, MariaDB 10.11, Apache 2.4)
Roteamento, CSRF, cadastro e validações, login com os hashes do script SQL, regeneração de sessão,
bloqueio por tentativas, XSS, controle de acesso (um autor não exclui o projeto de outro), cascata ao excluir,
`.htaccess` de `public/` e da raiz (bloqueio de `database/`, `app/`, `config/`, `.git`, `*.md`).
Confirme no seu MySQL 8.4.7 + Laragon.

## Limitações conhecidas
- O limitador de tentativas usa arquivos em `storage/throttle/` (não há limpeza automática).
- Recuperação de senha, conta, flowchart, edição de entries: partes 2 a 4.
