# Neymo

## Stack technique : 

#### Dev


<strong>Containers</strong> : Docker

<strong>Serveur web</strong> : NGINX

<strong>SGBD</strong> : MySQL 5.7

<strong>Client MySQL</strong> : Adminer 4.7

<strong>Language</strong> : PHP 7.4 => Symfony 5.1

<strong>SMTP</strong> : Mailhog (dev) & mailjet (Prod)

<br>
 
#### Prod
<strong>Serveur web</strong> : NGINX

<strong>SGBD</strong> : MySQL

<strong>Infrastructure cloud</strong> : DigitalOcean

<strong>Système d'exploitation</strong> : Ubuntu 18.04

<br>

## Génerer clefs pour JWT 

<strong>Créer le dossier </strong> : ```mkdir -p config/jwt```

<strong>Clef privée </strong> : ```openssl genpkey -out config/jwt/private.pem -aes256 -algorithm rsa -pkeyopt rsa_keygen_bits:4096```

<strong>Clef publique </strong> : ```openssl pkey -in config/jwt/private.pem -out config/jwt/public.pem -pubout```

Modifier la variable ```JWT_PASSPHRASE```, avec la passphrase que vous aurez défini

## Le projet est disponible sous Docker 🐳

<strong>Exécuter ce script</strong> : ```sh init.sh```


<strong>Back-Office/API</strong> : <a href="http://localhost">ICI</a>


<strong>Pour lancer une commande Symfony dans le container</strong> : `docker container exec -ti app bash`


<strong>Client MySQL</strong> : <a href='http://localhost:8080'>ICI</a>


<strong>Documentation de l'API</strong> : <a href="http://localhost/api/doc">ICI</a>

### Authentification à l'API via ApiDoc :

<img src="http://fotoforum.fr/photos/2020/07/08.1.gif" >
 
<br>

## Composants utilisés

<strong>Doctrine</strong> : ORM

<strong>jwt-authentication-bundle</strong> : Authentification API avec JWT

<strong>api-doc-bundle</strong> : Gérer la doc de l'API 

<strong>cors-bundle</strong> : CORS policy

<strong>Mailer</strong> : Envoi d'email

<strong>mailjet-apiv3-php</strong> : Permet l'envoi d'email

<strong>Faker</strong> : Création des faux jeux de données

<strong>php-credit-card-validator</strong> : Vérification la validité des informations d'une carte bancaire

<strong>Security</strong> : Gérer le login et les rôles

<br>

### MDP

<img src="https://cdn.discordapp.com/attachments/724913624551784479/730455259545206794/Screenshot_2020-07-08_at_17.54.24.png" >

## Argumentaire détaillé

Notre back-end se divise en 3 parties :

### Interface Super-Adminisitrateur 

La première partie de notre application est un back-office administré uniquement par nous-mêmes, Neymo, agence digitale, pour gérer toutes les gouvernances et leurs administrateurs. 
Nous avons listé les fonctionnalités dont nous avions besoin :
  * Lister toutes les gouvernances
  * Créer, afficher les détails et modifier une gouvernance
  * Créer, lister, modifier et supprimer les administrateurs d'une gouvernance
  
### Interface Administrateur
 
 Cette interface permet aux administrateurs de la gouvernance de pouvoir gérer leur propre gouvernances et leurs adhérents. Ainsi, un administrateur peut :
 * Modifier ses propres informations
 * Lister, créer et supprimer les autres administrateurs de la gouvernance
 * Gérer les différents types d'adhérents (commercant/particulier)
 * Valider ou refuser les demandes d'inscription en attente (envoie d'email de confirmation)
 * [WIP] Voir les détails de la gouvernance
 * [WIP] Accéder à un dashboard pour visualiser les statistiques de la monnaie locale

### API Adhérent
 Cette interface permet de spécifier les différentes fonctionnalités auxquelles ont accès les adhérents.
 <a href="https://neymo-api.benjaminadida.fr/api/doc">Documentation NelmioApiDoc</a>
 
 Tout notre back a été fait en Symfony 5.1.
 Pour ce faire, nous avons utilisé différentes technos pour nos environnements (prod/dev) :
 
 <strong>Dev</strong>
 
 Docker : Nous avons décidé de dockeriser tout notre back pour faciliter l'installation et harmoniser notre environnement entre développeurs.
 Il est composé de 5 images :
 * Le container "app" qui créer un environnement PHP, composer...
 * Le container "db", le server MySQL où est hébergé notre base de données
 * Le container "adminer", le client MySQL qui permet d'avoir une interface graphique de la base de données
 * Le container "web-server", le server web NGINX qui permet d'avoir un server simple à configurer
 * Le container "mailhog", qui propose deux types de service : un SMTP et une interface pour visualiser les mails

 <strong>Prod</strong>
 
* NGINX : pour avoir un serveur web simple d'utilisation
* MySQL : pour avoir une base de données qui s'accorde bien avec Symfony grâce à son ORM, Doctrine
* DigitalOcean : pour avoir une infrastructure cloud qui fournit une solide documentation
* Ubuntu 18.0 : pour avoir un système d'exploitation facile d'accès et modulable
 
## Identifiant de connexion à l'administrateur de la gouvernance

<strong>Email</strong> : ``admin@neymo.com``

<strong>Password</strong> : ``123456``
<br>

## Identifiant de connexion à l'API

<strong>Particulier</strong> : ``` { "username" : "particular@neymo.com", "password" : "123456" }```

<strong>Professionnel</strong> : ``` { "username" : "company@neymo.com", "password" : "123456" }```
