# boussole

Projet en développement. 

PHP/MariaDB/JS. 

Développé par la DJEPVA / Ministère chargé de la Jeunesse, avec l'accompagnement de la société La bonne agence (www.labonneagence.com).
Licence AGPL.

A savoir : 
- le projet héberge deux sites fonctionnant sur la même base de données : le site grand public et l'extranet d'administration.

- le site public se trouve à la racine du projet. l'extranet se trouve dans le répertoire /extranet. tous les autres répertoires appartiennent au site public, à l'exception de /src, qui est partagé par les deux environnements. 

- les deux sites sont développés en architecture modèle-vue-contrôleur :
-- pour le site public, les contrôleurs sont à la racine, les vues dans /view et le modèle dans /src/web
-- pour l'extranet, les contrôleurs sont dans /extranet, les vues dans /extranet/view, le modèle dans /src/admin