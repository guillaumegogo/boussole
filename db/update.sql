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

/**** créer avec l'appli les thèmes emploi et logement sur reims et le thème emploi sur coeur d'essone ****/