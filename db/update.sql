/*beta04*/
/*ALTER TABLE `bsl_formulaire` DROP INDEX `id_theme`;
UPDATE `bsl_formulaire__valeur` SET `defaut`=0 WHERE 1;
UPDATE `bsl_formulaire` SET `id_territoire` = '0' WHERE `bsl_formulaire`.`id_formulaire` = 1; 
UPDATE `bsl_formulaire` SET `id_territoire` = '0' WHERE `bsl_formulaire`.`id_formulaire` = 2;
ALTER TABLE `bsl_utilisateur` CHANGE `motdepasse` `motdepasse` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL;*/
	
ALTER TABLE `bsl_theme` ADD `id_territoire` INT(11) NULL AFTER `libelle_theme_court`;
ALTER TABLE `bsl__parametres` CHANGE `liste` `liste` VARCHAR(10) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT 'type/statut/...';
ALTER TABLE `bsl__parametres` CHANGE `id` `id` INT(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `bsl_theme` ADD UNIQUE( `actif_theme`, `libelle_theme_court`, `id_territoire`);
INSERT INTO `bsl__parametres` (`libelle`, `liste`) VALUES ('emploi', 'theme'), ('logement', 'theme'), ('santé', 'theme');
UPDATE `bsl_theme` SET `libelle_theme_court` = 'santé' WHERE `bsl_theme`.`id_theme` = 11;
UPDATE `bsl_theme` SET `id_territoire` = '0' WHERE `bsl_theme`.`id_theme` = 1; UPDATE `bsl_theme` SET `id_territoire` = '0' WHERE `bsl_theme`.`id_theme` = 2; UPDATE `bsl_theme` SET `id_territoire` = '0' WHERE `bsl_theme`.`id_theme` = 11;

/********************/
UPDATE `bsl__droits` SET `theme_w` = '2' WHERE `bsl__droits`.`id_statut` = 2;

/* update data pre v1
- première étape : créer manuellement les thèmes territorialisés et les formulaires manquant (en dupliquant ça va vite)
- seconde étape : les requêtes suivantes :*/

UPDATE `test__bsl_professionnel_themes` AS pt
JOIN `test__bsl_professionnel` AS p ON p.`id_professionnel`=pt.`id_professionnel` AND p.`competence_geo`="territoire"
JOIN `test__bsl_theme` AS old_t ON old_t.`id_theme`=pt.`id_theme`
JOIN `test__bsl_theme` AS new_t ON new_t.`libelle_theme_court`=old_t.`libelle_theme_court` AND new_t.`id_territoire`=p.`id_competence_geo`
SET pt.`id_theme`=new_t.`id_theme`;

UPDATE `test__bsl_offre` as o 
JOIN `test__bsl_professionnel` as p ON p.`id_professionnel`=o.`id_professionnel` AND p.`competence_geo`="territoire" 
JOIN `test__bsl_theme` AS old_st ON o.`id_sous_theme`=old_st.`id_theme` 
JOIN `test__bsl_theme` AS new_st ON new_st.`libelle_theme`=old_st.`libelle_theme` 
JOIN `test__bsl_theme` AS new_t ON new_t.`id_theme`=new_st.`id_theme_pere` AND new_t.`id_territoire`=p.`id_competence_geo` 
SET o.`id_sous_theme`=new_st.`id_theme`
/*o.id_offre, p.id_professionnel, o.`id_sous_theme`, new_st.id_theme, new_t.id_theme, p.`id_competence_geo`, new_t.`id_territoire` */
