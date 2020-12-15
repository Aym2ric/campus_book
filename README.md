# kit_sf4.4_users
- Symfony 4.4
- Phpmyadmin
- Mysql
- Docker
- Apache

# Configuration

## Installation
- Vérifier que le dossier /.docker/data/ est supprimé
- Rennomer les différents conteneurs dans le fichier docker-compose.yml 
- Définir les différents ports dans le fichier docker-compose.yml 
- Mettre le bon nom du conteneur dans le fichier /.docker/config/vhosts/sf4.conf 
- docker-compose up -d
- docker exec -it -u root sf4_php bash
- cd sf4
- composer update

## Base de données
- Définir les accès à la base de données dans le fichier .env
- php bin/console doctrine:database:create
- php bin/console doctrine:schema:update --force
- php bin/console user:create test pass --super-admin

## Template
- Modifier le nom du site dans la template /templates/security/login.html.twig
- Changer l'îcon du site dans /public/assets/
- Changer le paramètre qui gère le nom du site dans /config/services.yaml (parameters: app.nomdusite:)
