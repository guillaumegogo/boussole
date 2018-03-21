/*v1.03

ALTER TABLE `territoire` ADD `description_territoire` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL AFTER `nom_territoire`;
ALTER TABLE `professionnel` CHANGE `site_web_pro` `site_web_pro` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL;
*/
/*v1.02

ALTER TABLE `_offre` CHANGE `courriel_offre` `courriel_offre` VARCHAR(100) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;
*/

/*v1.01*/
/*
ALTER TABLE `recherche` ADD `id_territoire` INT NULL DEFAULT NULL AFTER `code_insee`;

UPDATE `recherche` as r
LEFT JOIN `territoire_villes` as tv on tv.code_insee=r.code_insee
LEFT JOIN `territoire` as te on te.id_territoire=tv.id_territoire AND te.actif_territoire=1
SET r.id_territoire=te.id_territoire 
WHERE te.actif_territoire=1 ;*/

/*v1.00
RENAME TABLE `bsl_demande` TO `demande`;
RENAME TABLE `bsl_formulaire` TO `formulaire`;
RENAME TABLE `bsl_formulaire__page` TO `formulaire__page`;
RENAME TABLE `bsl_formulaire__question` TO `formulaire__question`;
RENAME TABLE `bsl_formulaire__reponse` TO `formulaire__reponse`;
RENAME TABLE `bsl_formulaire__valeur` TO `formulaire__valeur`;
RENAME TABLE `bsl_mesure` TO `mesure`;
RENAME TABLE `bsl_mesure_criteres` TO `mesure_criteres`;
RENAME TABLE `bsl_offre` TO `offre`;
RENAME TABLE `bsl_offre_criteres` TO `offre_criteres`;
RENAME TABLE `bsl_professionnel` TO `professionnel`;
RENAME TABLE `bsl_professionnel_themes` TO `professionnel_themes`;
RENAME TABLE `bsl_professionnel_villes` TO `professionnel_villes`;
RENAME TABLE `bsl_recherche` TO `recherche`;
RENAME TABLE `bsl_territoire` TO `territoire`;
RENAME TABLE `bsl_territoire_villes` TO `territoire_villes`;
RENAME TABLE `bsl_theme` TO `theme`;
RENAME TABLE `bsl_utilisateur` TO `utilisateur`;
RENAME TABLE `bsl__departement` TO `_departement`;
RENAME TABLE `bsl__droits` TO `_droits`;
RENAME TABLE `bsl__parametres` TO `_parametres`;
RENAME TABLE `bsl__region` TO `_region`;
RENAME TABLE `bsl__ville` TO `_ville`;

UPDATE `formulaire__valeur` SET `valeur` = '*' WHERE `formulaire__valeur`.`id_valeur` = 63;
UPDATE `formulaire__valeur` SET `valeur` = '*' WHERE `formulaire__valeur`.`id_valeur` = 106;
UPDATE `formulaire__valeur` SET `valeur` = '*' WHERE `formulaire__valeur`.`id_valeur` = 56;
UPDATE `mesure` SET `id_professionnel`=79 WHERE `id_professionnel`=37;

('Centre régional d''Information Jeunesse de Provence-Alpes', NULL, 14, 0, '', '96 la Canebière', '13001', 'Marseille 01', '13201', '04 91 24 33 50', 'crijpa@crijpa.fr', 'www.crijpa.fr', '*', '*', 0, 7, 'regional', 17, 0, 0, 1, '2018-01-30 15:59:21', 1, NULL, NULL),
INSERT INTO `professionnel` (`nom_pro`, `type_pro`, `type_id`, `statut_id`, `description_pro`, `adresse_pro`, `code_postal_pro`, `ville_pro`, `code_insee_pro`, `courriel_pro`, `telephone_pro`, `site_web_pro`, `courriel_referent_boussole`, `telephone_referent_boussole`, `visibilite_coordonnees`, `delai_pro`, `competence_geo`, `id_competence_geo`, `zone_selection_villes`, `editeur`, `actif_pro`, `creation_date`, `creation_user_id`, `last_edit_date`, `last_edit_user_id`) VALUES
('Centre régional information jeunesse - Normandie Caen', NULL, 14, 0, '', '16 rue Neuve Saint-Jean', '14000', 'Caen', '14118', 'ij@infojeunesse.fr', '02 31 27 80 80', 'www.infojeunesse.fr', '*', '*', 0, 7, 'regional', 13, 0, 0, 1, '2018-01-30 16:00:08', 1, NULL, NULL),
('Centre régional d''Information Jeunesse de Bourgogne', NULL, 14, 0, '', '2 rue des Corroyeurs Boîte LL1', '21000', 'Dijon', '21231', 'documentation@ijbourgogne.com', '03 80 44 18 35', 'www.ijbourgogne.com', '*', '*', 0, 7, 'regional', 2, 0, 0, 1, '2018-01-30 16:00:58', 1, '2018-01-30 16:01:43', 1),
('Centre régional d''Information Jeunesse de Franche-Comté', NULL, 14, 0, '', '27 rue de la République', '25000', 'Besancon', '25056', 'contact@jeunes-fc.com', '03 81 21 16 16', 'www.jeunes-fc.com', '*', '*', 0, 7, 'regional', 2, 0, 0, 1, '2018-01-30 16:02:43', 1, NULL, NULL),
('Centre régional d''Information Jeunesse du Midi-Pyrénées Toulouse', NULL, 14, 0, '', '17 rue de Metz', '31000', 'Toulouse', '31555', 'contact@crij.org', '05 61 21 20 20', 'www.crij.org', '*', '*', 0, 7, 'regional', 15, 0, 0, 1, '2018-01-30 16:03:37', 1, NULL, NULL),
('Centre régional d''Information Jeunesse d''Aquitaine Bordeaux', NULL, 14, 0, '', '125 cours Alsace et Lorraine', '33000', 'Bordeaux', '33063', 'cija@cija.net', '05 56 56 00 56', 'www.info-jeune.net', '*', '*', 0, 7, 'regional', 14, 0, 0, 1, '2018-01-30 16:04:36', 1, NULL, NULL),
('Centre régional d''Information Jeunesse Languedoc-Roussillon', NULL, 14, 0, '', '3 avenue Charles Flahault', '34000', 'Montpellier', '34172', 'info@crij-montpellier.com', '04 67 04 36 66', 'www.crij-montpellier.com', '*', '*', 0, 7, 'regional', 15, 0, 0, 1, '2018-01-30 16:05:17', 1, NULL, NULL),
('Centre régional d''Information Jeunesse de Bretagne', NULL, 14, 0, '', '4 Bis cours des Alliés', '35000', 'Rennes', '35238', 'contact@crij-bretagne.com', '02 99 31 47 48', 'www.ij-bretagne.com', '/', '/', 0, 7, 'regional', 3, 0, 0, 1, '2018-01-30 16:06:01', 1, NULL, NULL),
('Centre régional d''Information Jeunesse des Pays-de-la-Loire', NULL, 14, 0, '', '37 rue Saint-Léonard', '44000', 'Nantes', '44109', 'crij@infos-jeunes.fr', '02 51 72 94 50', 'www.infos-jeunes.fr', '*', '*', 0, 7, 'regional', 16, 0, 0, 1, '2018-01-30 16:06:52', 1, NULL, NULL),
('Centre régional d''Information Jeunesse du Centre', NULL, 14, 0, '', '3 rue de la Cholerie', '45000', 'Orleans', '45234', 'crij@ijcentre.fr', '02 38 78 91 78', 'www.informationjeunesse-centre.fr', '/', '/', 0, 7, 'regional', 4, 0, 0, 1, '2018-01-30 16:07:54', 1, NULL, NULL),
('Centre régional d''Information Jeunesse de Champagne-Ardenne', NULL, 14, 0, '', '41 rue de Talleyrand', '51100', 'Reims', '51454', 'ijca@crij-ca.fr', '03 26 79 84 79', 'www.jeunes-ca.fr', '*', '*', 0, 7, 'regional', 6, 0, 0, 1, '2018-01-30 16:08:37', 1, NULL, NULL),
('Centre régional d''Information Jeunesse de Lorraine', NULL, 14, 0, '', '20 quai Claude le Lorrain', '54000', 'Nancy', '54395', 'accueil@crijlorraine.org', '03 83 37 04 46 ', 'www.jeunesenlorraine.org', '/', '/', 0, 7, 'regional', 6, 0, 0, 1, '2018-01-30 16:09:14', 1, NULL, NULL),
('Centre régional d''Information Jeunesse Hauts de France - Nord Pas de Calais', NULL, 14, 0, '', '2 rue Nicolas Leblanc', '59000', 'Lille', '59350', 'doc@crij-npdc.fr', '03 20 12 87 30', 'www.crij-hdf.fr', '/', '/', 0, 7, 'regional', 9, 0, 0, 1, '2018-01-30 16:10:04', 1, NULL, NULL),
('Espace Info Jeunes', NULL, 14, 0, '', '5 rue Saint-Genès', '63000', 'Clermont Ferrand', '63113', 'espace.info.jeunes@orange.fr', '04 73 92 30 50', 'www.info-jeunes.net', '*', '*', 0, 7, 'regional', 1, 0, 0, 1, '2018-01-30 16:38:28', 1, NULL, NULL),
('Centre régional Information Jeunesse d''Alsace Sémaphore', NULL, 14, 0, '', '7-9 rue du Moulin', '68100', 'Mulhouse', '68224', 'contact@crij-alsace.fr', '03 89 66 33 13', 'www.crij-alsace.fr', '*', '*', 0, 7, 'regional', 6, 0, 0, 1, '2018-01-30 16:43:00', 1, NULL, NULL),
('Centre régional d''Information Jeunesse Rhône-Alpes', NULL, 14, 0, '', '66 cours Charlemagne', '69002', 'Lyon 02', '69382', 'crijlyon@crijrhonealpes.fr', '04 72 77 00 66', 'www.crijrhonealpes.fr', '*', '*', 0, 7, 'regional', 1, 0, 0, 1, '2018-01-30 16:43:39', 1, NULL, NULL),
('Centre régional information jeunesse Normandie Rouen', NULL, 14, 0, '', '84 rue Beauvoisine', '76000', 'Rouen', '76540', 'contact@crijnormandierouen.fr', '02 32 10 49 49', 'www.crijnormandierouen.fr', '*', '*', 0, 7, 'regional', 13, 0, 0, 1, '2018-01-30 16:44:18', 1, NULL, NULL),
('Centre régional d''Information Jeunesse Hauts de France - Picardie', NULL, 14, 0, '', '50 rue Riolan', '80000', 'Amiens', '80021', 'contact@crij-picardie.fr', '06 45 53 73 62', 'www.crij-hdf.fr', '*', '*', 0, 7, 'regional', 9, 0, 0, 1, '2018-01-30 16:44:57', 1, NULL, NULL),
('Centre Régional Information Jeunesse Poitou-Charentes / Maison de l''Europe de la Vienne', NULL, 14, 0, '', '64 rue Gambetta', '86000', 'Poitiers', '86194', 'info@ij-poitou-charentes.org', '05 49 60 68 68', 'www.pourlesjeunes.com', '*', '*', 0, 7, 'regional', 14, 0, 0, 1, '2018-01-30 16:49:09', 1, NULL, NULL),
('Centre régional d''Information Jeunesse du Limousin', NULL, 14, 0, '', '13 cours Jourdan , Carré Jourdan', '87000', 'Limoges', '87085', 'info@crijlimousin.org', '05 55 10 08 00', 'www.crijlimousin.org', '*', '*', 0, 7, 'regional', 14, 0, 0, 1, '2018-01-30 16:50:04', 1, NULL, NULL),
('Centre régional d''Information Jeunesse de Guadeloupe', NULL, 14, 0, '', 'Immeuble des Fonctionnaires 2 boulevard Légitimus', '97110', 'Pointe A Pitre', '97120', 'crij.guadeloupe@gmail.com', '05 90 90 13 10', 'www.crij-guadeloupe.org', '*', '*', 0, 7, 'regional', 7, 0, 0, 1, '2018-01-30 16:51:57', 1, NULL, NULL),
('Centre régional d''Information Jeunesse de La Réunion', NULL, 14, 0, '', '28 rue Jean Chatel', '97400', 'St Denis', '97411', 'crij-reunion@crij-reunion.com', '02 62 20 98 20', 'www.crij-reunion.com', '*', '*', 0, 7, 'regional', 11, 0, 0, 1, '2018-01-30 16:52:44', 1, NULL, NULL),
('Centre régional d''Information Jeunesse de la Côte d''Azur', NULL, 14, 0, '', '19 rue Gioffredo', '06000', 'Nice', '06088', 'crij@ijca.fr', '04 93 80 93 93', 'www.ijca.fr', '*', '*', 0, 7, 'regional', 17, 0, 0, 1, '2018-01-30 16:53:24', 1, NULL, NULL);
*/

/* ajout au script v1 ? 

TRUNCATE bsl_demande;
TRUNCATE bsl_recherche;
DELETE FROM `bsl_utilisateur` WHERE `actif_utilisateur` = 0;*/


	
/*
ALTER TABLE `v1__bsl_theme` ADD `id_territoire` INT(11) NULL AFTER `libelle_theme_court`;
ALTER TABLE `v1__bsl__parametres` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `v1__bsl__parametres` CHANGE `liste` `liste` VARCHAR(10) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT 'type/statut/...';
ALTER TABLE `v1__bsl_theme` ADD UNIQUE( `actif_theme`, `libelle_theme_court`, `id_territoire`);
INSERT INTO `v1__bsl__parametres` (`libelle`, `liste`) VALUES ('emploi', 'theme'), ('logement', 'theme'), ('santé', 'theme');
UPDATE `v1__bsl_theme` SET `libelle_theme_court` = 'santé' WHERE `v1__bsl_theme`.`id_theme` = 11;
UPDATE `v1__bsl_theme` SET `id_territoire` = '0' WHERE `v1__bsl_theme`.`id_theme` = 1; UPDATE `v1__bsl_theme` SET `id_territoire` = '0' WHERE `v1__bsl_theme`.`id_theme` = 2; UPDATE `v1__bsl_theme` SET `id_territoire` = '0' WHERE `v1__bsl_theme`.`id_theme` = 11;

UPDATE `v1__bsl__droits` SET `theme_w` = '2' WHERE `v1__bsl__droits`.`id_statut` = 2;*/

/* update data pre v1
- première étape : créer manuellement les thèmes territorialisés et les formulaires manquant (en dupliquant ça va vite)
- seconde étape : les requêtes suivantes :*/

/*
UPDATE `v1__bsl_professionnel_themes` AS pt
JOIN `v1__bsl_professionnel` AS p ON p.`id_professionnel`=pt.`id_professionnel` AND p.`competence_geo`="territoire"
JOIN `v1__bsl_theme` AS old_t ON old_t.`id_theme`=pt.`id_theme`
JOIN `v1__bsl_theme` AS new_t ON new_t.`libelle_theme_court`=old_t.`libelle_theme_court` AND new_t.`id_territoire`=p.`id_competence_geo`
SET pt.`id_theme`=new_t.`id_theme`;

UPDATE `v1__bsl_offre` as o 
JOIN `v1__bsl_professionnel` as p ON p.`id_professionnel`=o.`id_professionnel` AND p.`competence_geo`="territoire" 
JOIN `v1__bsl_theme` AS old_st ON o.`id_sous_theme`=old_st.`id_theme` 
JOIN `v1__bsl_theme` AS new_st ON new_st.`libelle_theme`=old_st.`libelle_theme` 
JOIN `v1__bsl_theme` AS new_t ON new_t.`id_theme`=new_st.`id_theme_pere` AND new_t.`id_territoire`=p.`id_competence_geo` 
SET o.`id_sous_theme`=new_st.`id_theme`;*/


/*beta04*/
/*ALTER TABLE `bsl_formulaire` DROP INDEX `id_theme`;
UPDATE `bsl_formulaire__valeur` SET `defaut`=0 WHERE 1;
UPDATE `bsl_formulaire` SET `id_territoire` = '0' WHERE `bsl_formulaire`.`id_formulaire` = 1; 
UPDATE `bsl_formulaire` SET `id_territoire` = '0' WHERE `bsl_formulaire`.`id_formulaire` = 2;
ALTER TABLE `bsl_utilisateur` CHANGE `motdepasse` `motdepasse` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL;
ALTER TABLE `bsl__parametres` ADD PRIMARY KEY (`id`), ADD UNIQUE KEY `id` (`id`,`libelle`,`liste`);*/