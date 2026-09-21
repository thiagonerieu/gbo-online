# gbo-online

Game Book Organizer

gbo-online/
│
├── config/
│ └── database.php # connexion à la base de données (PDO)
│
├── app/
│ ├── models/
│ │ ├── user.php
│ │ └── grafo.php # ex. : modèle qui récupère des données pour vis.js
│ │
│ ├── controllers/
│ │ ├── userController.php
│ │ └── grafoController.php
│ │
│ └── views/
│ ├── layout/
│ │ ├── header.php
│ │ └── footer.php
│ ├── utilisateur/
│ │ ├── index.php
│ │ └── form.php
│ └── graphe/
│ └── visualiser.php # page utilisant vis.js
│
├── public/
│ ├── index.php # point d'entrée (contrôleur frontal)
│ ├── css/
│ │ └── style.css
│ ├── js/
│ │ ├── app.js
│ │ └── grafo.js # initialisation de vis.js
│ └── assets/vendor/vis.js/ # bibliothèque vis.js téléchargée localement
│
├── core/
│ ├── Router.php
│ ├── Controller.php # contrôleur de base
│ └── Model.php # modèle de base (connexion commune)
│
└── .htaccess # redirige tout vers public/index.php
