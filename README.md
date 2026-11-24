C:\laragon\www\gamebook-organiser\
├── config\
│   ├── config.php            ← lê o .env e monta as configurações
│   └── .env                  ← credenciais do banco (nunca versionar)
├── database\
│   ├── gamebook_organiser.sql
│   └── gamebook_organiser_queries.sql
├── docs\
│   └── dictionnaire_de_donnees_gamebook_organiser.docx
├── src\                      ← PHP "de bastidores" (lógica e acesso ao banco)
│   ├── Database.php          ← conexão PDO
│   ├── Auth.php              ← login, sessão, papéis
│   ├── helpers.php           ← escape de saída, token CSRF
│   └── Repository\           ← uma classe por tabela, com as consultas preparadas
│       ├── UserRepository.php
│       ├── ProjectRepository.php
│       ├── EntryRepository.php
│       ├── ChoiceRepository.php
│       └── CharacterRepository.php
├── templates\                ← pedaços de HTML reutilizáveis
│   ├── header.php
│   └── footer.php
├── storage\                  ← arquivos gerados (exports PDF/Kindle, logs)
├── public\                   ← ÚNICA pasta exposta (document root)
│   ├── index.php
│   ├── login.php  ·  logout.php  ·  register.php
│   ├── projects.php          ← lista de projetos do autor
│   ├── editor.php            ← página do grafo e da edição das entries
│   ├── api\                  ← endpoints que devolvem/recebem JSON
│   │   ├── graph.php         ← nós e arestas para o vis.js
│   │   ├── entry_save.php
│   │   ├── choice_save.php
│   │   └── positions_save.php
│   └── assets\
│       ├── styles\style.css
│       ├── scripts\graph.js       ← inicializa o vis.js e chama api/graph.php
│       ├── scripts\editor.js      ← formulários e salvamento
│       └── vendor\vis-network\  ← cópia local do vis.js
├── .gitignore                ← inclui config\.env e storage\
└── README.md
