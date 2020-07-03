# Neymo

## Stack technique : 

#### Dev


<strong>Containers</strong> : Docker

<strong>Serveur web</strong> : NGINX

<strong>SGBD</strong> : MySQL

<strong>Client MySQL</strong> : adminer

<strong>Language</strong> : PHP => Symfony

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

<strong>Clef privé </strong> : ```openssl genpkey -out config/jwt/private.pem -aes256 -algorithm rsa -pkeyopt rsa_keygen_bits:4096```

<strong>Clef public </strong> : ```openssl pkey -in config/jwt/private.pem -out config/jwt/public.pem -pubout```

Modifier la variable ```JWT_PASSPHRASE```, avec la passphrase que vous aurez définis

## Le projet est disponible sous Docker 🐳

<strong>Executez ce script</strong> : ```sh init.sh```


<strong>Back-Office/API</strong> : <a href="http://localhost">ICI</a>


<strong>Pour lancer une commande symfony dans le container</strong> : `docker container exec -ti app bash`


<strong>Client MySQL</strong> : <a href='http://localhost:8080'>ICI</a>


<strong>Documentation de l'API</strong> : <a href="http://localhost/api/doc">ICI</a>
 
<br>

## Composants utilisés

<strong>Doctrine </strong> : ORM

<strong>Faker</strong> : Afin de rendre l'application plus réaliste on genére un jeu de fausse données

<strong>jwt-authentication-bundle</strong> : Authentification API avec JWT

<strong>api-doc-bundle</strong> : Gérer la doc de l'api 

<strong>cors-bundle</strong> : CORS policy

<strong>Mailer</strong> : Envoi d'email

 
## Identifiant de connexion à l'administrateur de la gouvernance

<strong>Email</strong> : ``admin@neymo.com``

<strong>Password</strong> : ``123456``

<br>

## Identifiant de connexion à l'API

<strong>Particulier</strong> : ``` { "username" : "particular@neymo.com", "password" : "123456" }```

<strong>Professionnel</strong> : ``` { "username" : "company@neymo.com", "password" : "123456" }```
