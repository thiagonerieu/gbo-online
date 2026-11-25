C:\laragon\www\gamebook-organiser\
gbo-online/
├── public/                      ← document root
│   ├── index.php                ← ÚNICO ponto de entrada (front controller)
│   ├── .htaccess                ← envia tudo para index.php
│   └── assets/                  ← exatamente como está
├── app/
│   ├── core/                    Router · Controller · View · Database
│   ├── controllers/             Auth · Project · Flowchart · Entry · Account · Api
│   ├── models/                  User · Project · Entry · Choice · Character  (só SQL, com as jointures)
│   ├── services/                GraphService · EntryService (regras, shuffle, validação)
│   ├── utils/                   Csrf · Session · Validator · Response · helpers (e())
│   ├── views/
│   │   ├── layouts/             auth.php · app.php
│   │   ├── auth/                login · signup · account-recovery
│   │   └── app/                 my-projects · my-account · my-flowchart · entry
│   └── routes.php               ← tabela de rotas
├── config/ · database/ · docs/ · storage/ (exports, ignorado)
├── .htaccess                    ← raiz: bloqueia tudo fora de public/
└── README.md