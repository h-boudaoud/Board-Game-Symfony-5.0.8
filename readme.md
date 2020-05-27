# Board game App
#### Symfony 5.0.8
Ce code sert uniquement à justifier mes connaissances du développements d'application avec Symfony.

This code is only used to justify my knowledge of application development with Symfony.


## Installation
```
# Clone the deposit!             <-->  Cloner le dépot!
git clone https://github.com/h-boudaoud/board-game.git

# Move in the folder             <-->  Se déplacer dans le dossier
cd board-game

# Install the dependencies!      <-->  Installer les dépendances !
composer install


#  Create the database           <-->  Créer la base de données
php bin/console doctrine:database:create

# perform the migrations         <-->  Exécuter les migrations
# php bin/console doctrine:migrations:migrate

#  Executer the fixture           <-->  Exécuter les fixtures
#php bin/console doctrine:fixtures:load --no-interaction

#  Launch the server             <-->  Lancer le serveur 
# execute run-server.sh or
run-server.sh
```

##  Console commands used to create this project 
### Install project
Install
```
composer create-project symfony/skeleton board-game
```
Initialize git 
```
git init && git add . && git commit -m "Initial commit"
git remote add origin https://github.com/h-boudaoud/board-game.git
git push -u origin master
```
### Install components
```
composer require maker twig validator annotations orm form && composer require profiler --dev

```
### Configure Database
In the file .env, change the parameters of variable  `DATABASE_URL`

Case:  Create a new Database or generate entities from an existing database
```
#  Create the database           <-->  Créer la base de données
php bin/console doctrine:database:create
```

Case: To start with an existing and immutable database model

```
#  Generate entities            <-->  Générer les modèles
php bin/console doctrine:mapping:import "App\Entity" annotation --path=src/Entity
```
<span class="height:20px;">![alt text](./!.png  "Warning")</span>
Doctrine is able to convert approximately 70-80% of the necessary mapping information based on fields
[Doc](https://symfony.com/doc/current/doctrine/reverse_engineering.html)

### Entities (CRUD)
#### new Entity and CRUD by CLI
```
php bin/console make:entity Myentity
## Add properties at MyEntity and generate a new migration
php bin/console make:migration
## To push migration in database
php bin/console doctrine:migrations:migrate

## if security-csrf is not installed
composer require security-csrf 

php bin/console make:crud Myentity
```
#### views width Twig
Change base.html.twig to use Bootstrap, Ajax and Jquery 
```
composer require symfony/asset
```
Add new folder `public/asset` for documents public `js, css, img, ...`

Change the content of the templates according to the display requested






## Author
h.boudaoud



