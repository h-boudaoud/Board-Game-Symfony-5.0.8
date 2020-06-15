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



#### Security : Entity User CRUD manually

Add component security-bundle

```
composer require symfony/security-bundle
```
Update file `config/packages/security.yaml`

more info in :
  - security.yaml Symfony 5.1: [symfony.com/.../user_provider.html](https://symfony.com/doc/current/security/user_provider.html)
  - security.yaml 4.0: [symfony.com/.../security.html](https://symfony.com/doc/4.0/security.html)
  
```
# config/packages/security.yaml
security:
    # Users in memory
    providers:
        in_memory:
            memory:
                users:
                    john_admin: { password: '$2y$13$jxGxc ... IuqDju', roles: ['ROLE_ADMIN'] }
    
    firewalls:
        main:
            provider: users_in_memory
        
        # Using the form_login Authentication Provider  
        form_login:
            login_path: login
            check_path: login
            default_target_path: game_index    
        logout:
            path: logout
            target: game_index
    ..


    # Example Roles hierarchy  
    role_hierarchy:
        ROLE_STOREKEEPER: ROLE_USER
        ROLE_ADMIN: [ROLE_MODERATOR, ROLE_OPERATOR, ROLE_STOREKEEPER]
        ROLE_SUPER_ADMIN: ROLE_ADMIN

    
    #  Use A Different Password Encoder   
    encoders:
        # If No encoder has been configured for account "Symfony\Component\Security\Core\User\User"
        Symfony\Component\Security\Core\User\User: plaintext
        # Else
        Symfony\Component\Security\Core\User\User:
            algorithm: bcrypt
            cost: 12
 ```
  
  - Annotations : [symfony.com/.../security.html](https://symfony.com/doc/current/bundles/SensioFrameworkExtraBundle/annotations/security.html)
  ```
    # Example Annotation Twig : check if the current user has a certain role ("ROLE_ADMIN")
    {% if is_granted("ROLE_ADMIN") %}
        <a href="...">Delete</a> 
    {% endif %}
    
    # Example Annotation in controller:
    /**
     * @IsGranted("ROLE_ADMIN", statusCode=401, message="No access! Get out!")
    */
    class MyController ... or function index 
    
    # Example Annotation in the action method
    public function adminDashboard()
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
    
        // ...
    }

  ```





## Author
h.boudaoud



