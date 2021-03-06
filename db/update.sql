/*v1.01*/ 
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