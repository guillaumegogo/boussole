`ALTER TABLE bsl_formulaire DROP INDEX id_theme;`
UPDATE `bsl_formulaire__valeur` SET `defaut`=0 WHERE 1;