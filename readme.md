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
## 
# php bin/console doctrine:database:create

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



## Author
h.boudaoud



