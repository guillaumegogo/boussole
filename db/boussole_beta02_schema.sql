-- phpMyAdmin SQL Dump
-- version 4.6.4
-- https://www.phpmyadmin.net/
--
-- Client :  127.0.0.1
-- Généré le :  Ven 20 Octobre 2017 à 09:18
-- Version du serveur :  10.2.6-MariaDB
-- Version de PHP :  5.6.25

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données :  `boussole`
--

-- --------------------------------------------------------

--
-- Structure de la table `bsl_demande`
--

CREATE TABLE `bsl_demande` (
  `id_demande` int(11) NOT NULL,
  `date_demande` datetime NOT NULL,
  `id_offre` int(11) NOT NULL,
  `contact_jeune` varchar(100) NOT NULL,
  `id_recherche` int(11) DEFAULT NULL,
  `id_hashe` varchar(255) DEFAULT NULL,
  `code_insee_jeune` varchar(5) DEFAULT NULL,
  `profil` text DEFAULT NULL,
  `date_traitement` datetime DEFAULT NULL,
  `commentaire` text DEFAULT NULL,
  `user_derniere_modif` int(11) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

-- --------------------------------------------------------

--
-- Structure de la table `bsl_formulaire`
--

CREATE TABLE `bsl_formulaire` (
  `id_formulaire` int(11) NOT NULL,
  `type` varchar(10) DEFAULT NULL COMMENT '"offre"/"mesure"',
  `id_theme` int(11) DEFAULT NULL,
  `id_territoire` int(11) DEFAULT NULL,
  `nb_pages` tinyint(4) NOT NULL DEFAULT 1,
  `actif` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `bsl_formulaire__page`
--

CREATE TABLE `bsl_formulaire__page` (
  `id_page` int(11) NOT NULL,
  `id_formulaire` int(11) NOT NULL,
  `titre` varchar(255) NOT NULL,
  `aide` text DEFAULT NULL,
  `ordre` tinyint(4) NOT NULL,
  `actif` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `bsl_formulaire__question`
--

CREATE TABLE `bsl_formulaire__question` (
  `id_question` int(11) NOT NULL,
  `id_page` int(11) NOT NULL,
  `libelle` varchar(255) NOT NULL,
  `html_name` varchar(100) NOT NULL,
  `ordre` tinyint(4) NOT NULL,
  `type` varchar(20) NOT NULL,
  `taille` tinyint(4) DEFAULT NULL,
  `obligatoire` tinyint(1) NOT NULL DEFAULT 0,
  `actif` tinyint(1) NOT NULL DEFAULT 1,
  `id_reponse` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `bsl_formulaire__reponse`
--

CREATE TABLE `bsl_formulaire__reponse` (
  `id_reponse` int(11) NOT NULL,
  `libelle` varchar(20) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `bsl_formulaire__valeur`
--

CREATE TABLE `bsl_formulaire__valeur` (
  `id_valeur` int(11) NOT NULL,
  `id_reponse` int(11) NOT NULL,
  `libelle` varchar(100) NOT NULL,
  `valeur` varchar(100) NOT NULL,
  `ordre` tinyint(4) DEFAULT NULL,
  `defaut` tinyint(1) NOT NULL DEFAULT 0,
  `actif` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `bsl_mesure`
--

CREATE TABLE `bsl_mesure` (
  `id_mesure` int(11) NOT NULL,
  `nom_mesure` varchar(255) NOT NULL,
  `description_mesure` text NOT NULL,
  `debut_mesure` date NOT NULL,
  `fin_mesure` date NOT NULL,
  `id_sous_theme` int(11) DEFAULT NULL,
  `id_professionnel` int(11) DEFAULT NULL,
  `adresse_mesure` varchar(255) DEFAULT NULL,
  `code_postal_mesure` varchar(5) DEFAULT NULL,
  `ville_mesure` varchar(50) DEFAULT NULL,
  `code_insee_mesure` varchar(5) DEFAULT NULL,
  `courriel_mesure` varchar(40) NOT NULL,
  `telephone_mesure` varchar(20) NOT NULL,
  `site_web_mesure` varchar(255) NOT NULL,
  `competence_geo` varchar(15) NOT NULL,
  `id_competence_geo` int(11) DEFAULT NULL,
  `zone_selection_villes` tinyint(1) NOT NULL DEFAULT 0,
  `actif_mesure` tinyint(1) NOT NULL DEFAULT 1,
  `creation_date` datetime DEFAULT NULL,
  `creation_user_id` int(11) DEFAULT NULL,
  `last_edit_date` datetime DEFAULT NULL,
  `last_edit_user_id` int(11) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

-- --------------------------------------------------------

--
-- Structure de la table `bsl_mesure_criteres`
--

CREATE TABLE `bsl_mesure_criteres` (
  `id_mesure` int(11) NOT NULL,
  `nom_critere` varchar(100) NOT NULL,
  `valeur_critere` varchar(100) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `bsl_offre`
--

CREATE TABLE `bsl_offre` (
  `id_offre` int(11) NOT NULL,
  `nom_offre` varchar(255) NOT NULL,
  `description_offre` text NOT NULL,
  `debut_offre` date NOT NULL,
  `fin_offre` date NOT NULL,
  `id_sous_theme` int(11) DEFAULT NULL,
  `id_professionnel` int(11) DEFAULT NULL,
  `adresse_offre` varchar(255) DEFAULT NULL,
  `code_postal_offre` varchar(5) DEFAULT NULL,
  `ville_offre` varchar(50) DEFAULT NULL,
  `code_insee_offre` varchar(5) DEFAULT NULL,
  `courriel_offre` varchar(40) NOT NULL,
  `telephone_offre` varchar(20) NOT NULL,
  `site_web_offre` varchar(255) NOT NULL,
  `delai_offre` int(11) NOT NULL,
  `zone_selection_villes` tinyint(1) NOT NULL DEFAULT 0,
  `actif_offre` tinyint(1) NOT NULL DEFAULT 1,
  `creation_date` datetime DEFAULT NULL,
  `creation_user_id` int(11) DEFAULT NULL,
  `last_edit_date` datetime DEFAULT NULL,
  `last_edit_user_id` int(11) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

-- --------------------------------------------------------

--
-- Structure de la table `bsl_offre_criteres`
--

CREATE TABLE `bsl_offre_criteres` (
  `id_offre` int(11) NOT NULL,
  `nom_critere` varchar(100) NOT NULL,
  `valeur_critere` varchar(100) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `bsl_professionnel`
--

CREATE TABLE `bsl_professionnel` (
  `id_professionnel` int(11) NOT NULL,
  `nom_pro` varchar(255) NOT NULL,
  `type_pro` varchar(100) DEFAULT NULL,
  `type_id` int(11) DEFAULT NULL,
  `statut_id` int(11) DEFAULT NULL,
  `description_pro` text DEFAULT NULL,
  `adresse_pro` varchar(255) DEFAULT NULL,
  `code_postal_pro` varchar(5) DEFAULT NULL,
  `ville_pro` varchar(50) DEFAULT NULL,
  `code_insee_pro` varchar(5) DEFAULT NULL,
  `courriel_pro` varchar(100) DEFAULT NULL,
  `telephone_pro` varchar(20) DEFAULT NULL,
  `site_web_pro` varchar(50) DEFAULT NULL,
  `courriel_referent_boussole` varchar(100) DEFAULT NULL,
  `telephone_referent_boussole` varchar(20) DEFAULT NULL,
  `visibilite_coordonnees` tinyint(1) NOT NULL DEFAULT 0,
  `delai_pro` int(11) DEFAULT NULL,
  `competence_geo` varchar(15) NOT NULL,
  `id_competence_geo` int(11) DEFAULT NULL,
  `zone_selection_villes` tinyint(1) NOT NULL DEFAULT 0,
  `editeur` tinyint(1) NOT NULL DEFAULT 0,
  `actif_pro` tinyint(4) NOT NULL DEFAULT 1,
  `creation_date` datetime DEFAULT NULL,
  `creation_user_id` int(11) DEFAULT NULL,
  `last_edit_date` datetime DEFAULT NULL,
  `last_edit_user_id` int(11) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

-- --------------------------------------------------------

--
-- Structure de la table `bsl_professionnel_themes`
--

CREATE TABLE `bsl_professionnel_themes` (
  `id_professionnel` int(11) NOT NULL,
  `id_theme` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `bsl_professionnel_villes`
--

CREATE TABLE `bsl_professionnel_villes` (
  `id_professionnel` int(11) NOT NULL,
  `code_insee` varchar(5) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `bsl_recherche`
--

CREATE TABLE `bsl_recherche` (
  `id_recherche` int(11) NOT NULL,
  `date_recherche` datetime NOT NULL,
  `code_insee` varchar(5) NOT NULL,
  `besoin` varchar(25) NOT NULL,
  `criteres` text NOT NULL,
  `nb_offres` int(11) NOT NULL DEFAULT 0
) ENGINE=MyISAM DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

-- --------------------------------------------------------

--
-- Structure de la table `bsl_territoire`
--

CREATE TABLE `bsl_territoire` (
  `id_territoire` int(11) NOT NULL,
  `nom_territoire` varchar(255) NOT NULL,
  `code_territoire` varchar(20) DEFAULT NULL,
  `actif_territoire` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `bsl_territoire_villes`
--

CREATE TABLE `bsl_territoire_villes` (
  `id_territoire` int(11) NOT NULL,
  `code_insee` varchar(5) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `bsl_theme`
--

CREATE TABLE `bsl_theme` (
  `id_theme` int(11) NOT NULL,
  `libelle_theme` varchar(120) NOT NULL,
  `id_theme_pere` int(11) DEFAULT NULL,
  `actif_theme` tinyint(1) NOT NULL DEFAULT 1,
  `ordre_theme` int(2) DEFAULT NULL,
  `libelle_theme_court` varchar(15) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `bsl_utilisateur`
--

CREATE TABLE `bsl_utilisateur` (
  `id_utilisateur` int(11) NOT NULL,
  `nom_utilisateur` varchar(255) DEFAULT NULL,
  `email` varchar(255) NOT NULL,
  `motdepasse` varchar(255) NOT NULL,
  `date_inscription` datetime NOT NULL,
  `id_statut` int(11) NOT NULL,
  `id_metier` int(11) DEFAULT NULL,
  `actif_utilisateur` tinyint(4) NOT NULL DEFAULT 1,
  `reinitialisation_mdp` varchar(255) DEFAULT NULL,
  `date_demande_reinitialisation` datetime DEFAULT NULL,
  `creation_user_id` int(11) DEFAULT NULL,
  `last_edit_date` datetime DEFAULT NULL,
  `last_edit_user_id` int(11) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

-- --------------------------------------------------------

--
-- Structure de la table `bsl__departement`
--

CREATE TABLE `bsl__departement` (
  `id_departement` varchar(3) NOT NULL,
  `nom_departement` varchar(40) NOT NULL,
  `id_region` int(2) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `bsl__droits`
--

CREATE TABLE `bsl__droits` (
  `id_statut` int(11) NOT NULL,
  `libelle_statut` varchar(30) NOT NULL,
  `demande_r` tinyint(4) NOT NULL,
  `demande_w` tinyint(4) NOT NULL,
  `offre_r` tinyint(4) NOT NULL,
  `offre_w` tinyint(4) NOT NULL,
  `mesure_r` tinyint(4) NOT NULL,
  `mesure_w` tinyint(4) NOT NULL,
  `professionnel_r` tinyint(4) NOT NULL,
  `professionnel_w` tinyint(4) NOT NULL,
  `utilisateur_r` tinyint(4) NOT NULL,
  `utilisateur_w` tinyint(4) NOT NULL,
  `formulaire_r` tinyint(4) NOT NULL,
  `formulaire_w` tinyint(4) NOT NULL,
  `theme_r` tinyint(4) NOT NULL,
  `theme_w` tinyint(4) NOT NULL,
  `territoire_r` tinyint(4) NOT NULL,
  `territoire_w` tinyint(4) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

-- --------------------------------------------------------

--
-- Structure de la table `bsl__parametres`
--

CREATE TABLE `bsl__parametres` (
  `id` int(11) NOT NULL,
  `libelle` varchar(40) NOT NULL,
  `liste` varchar(10) NOT NULL COMMENT 'type/statut'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `bsl__region`
--

CREATE TABLE `bsl__region` (
  `id_region` int(2) NOT NULL,
  `nom_region` varchar(40) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `bsl__ville`
--

CREATE TABLE `bsl__ville` (
  `code_insee` varchar(5) NOT NULL,
  `code_postal` varchar(5) NOT NULL,
  `nom_ville` varchar(100) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Index pour les tables exportées
--

--
-- Index pour la table `bsl_demande`
--
ALTER TABLE `bsl_demande`
  ADD PRIMARY KEY (`id_demande`);

--
-- Index pour la table `bsl_formulaire`
--
ALTER TABLE `bsl_formulaire`
  ADD PRIMARY KEY (`id_formulaire`),
  ADD UNIQUE KEY `id_theme` (`id_theme`,`id_territoire`);

--
-- Index pour la table `bsl_formulaire__page`
--
ALTER TABLE `bsl_formulaire__page`
  ADD PRIMARY KEY (`id_page`),
  ADD UNIQUE KEY `id_formulaire` (`id_formulaire`,`ordre`,`actif`);

--
-- Index pour la table `bsl_formulaire__question`
--
ALTER TABLE `bsl_formulaire__question`
  ADD PRIMARY KEY (`id_question`);

--
-- Index pour la table `bsl_formulaire__reponse`
--
ALTER TABLE `bsl_formulaire__reponse`
  ADD PRIMARY KEY (`id_reponse`),
  ADD UNIQUE KEY `libelle` (`libelle`);

--
-- Index pour la table `bsl_formulaire__valeur`
--
ALTER TABLE `bsl_formulaire__valeur`
  ADD PRIMARY KEY (`id_valeur`);

--
-- Index pour la table `bsl_mesure`
--
ALTER TABLE `bsl_mesure`
  ADD PRIMARY KEY (`id_mesure`);

--
-- Index pour la table `bsl_mesure_criteres`
--
ALTER TABLE `bsl_mesure_criteres`
  ADD UNIQUE KEY `id_mesure` (`id_mesure`,`nom_critere`,`valeur_critere`);

--
-- Index pour la table `bsl_offre`
--
ALTER TABLE `bsl_offre`
  ADD PRIMARY KEY (`id_offre`);

--
-- Index pour la table `bsl_offre_criteres`
--
ALTER TABLE `bsl_offre_criteres`
  ADD UNIQUE KEY `id_offre` (`id_offre`,`nom_critere`,`valeur_critere`);

--
-- Index pour la table `bsl_professionnel`
--
ALTER TABLE `bsl_professionnel`
  ADD PRIMARY KEY (`id_professionnel`);

--
-- Index pour la table `bsl_professionnel_themes`
--
ALTER TABLE `bsl_professionnel_themes`
  ADD UNIQUE KEY `id_professionnel` (`id_professionnel`,`id_theme`);

--
-- Index pour la table `bsl_professionnel_villes`
--
ALTER TABLE `bsl_professionnel_villes`
  ADD UNIQUE KEY `id_professionnel` (`id_professionnel`,`code_insee`);

--
-- Index pour la table `bsl_recherche`
--
ALTER TABLE `bsl_recherche`
  ADD PRIMARY KEY (`id_recherche`);

--
-- Index pour la table `bsl_territoire`
--
ALTER TABLE `bsl_territoire`
  ADD PRIMARY KEY (`id_territoire`);

--
-- Index pour la table `bsl_territoire_villes`
--
ALTER TABLE `bsl_territoire_villes`
  ADD UNIQUE KEY `id_territoire` (`id_territoire`,`code_insee`);

--
-- Index pour la table `bsl_theme`
--
ALTER TABLE `bsl_theme`
  ADD PRIMARY KEY (`id_theme`);

--
-- Index pour la table `bsl_utilisateur`
--
ALTER TABLE `bsl_utilisateur`
  ADD PRIMARY KEY (`id_utilisateur`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Index pour la table `bsl__departement`
--
ALTER TABLE `bsl__departement`
  ADD PRIMARY KEY (`id_departement`);

--
-- Index pour la table `bsl__droits`
--
ALTER TABLE `bsl__droits`
  ADD PRIMARY KEY (`id_statut`);

--
-- Index pour la table `bsl__parametres`
--
ALTER TABLE `bsl__parametres`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `id` (`id`,`libelle`,`liste`);

--
-- Index pour la table `bsl__region`
--
ALTER TABLE `bsl__region`
  ADD PRIMARY KEY (`id_region`);

--
-- Index pour la table `bsl__ville`
--
ALTER TABLE `bsl__ville`
  ADD UNIQUE KEY `code_insee` (`code_insee`,`code_postal`,`nom_ville`);

--
-- AUTO_INCREMENT pour les tables exportées
--

--
-- AUTO_INCREMENT pour la table `bsl_demande`
--
ALTER TABLE `bsl_demande`
  MODIFY `id_demande` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;
--
-- AUTO_INCREMENT pour la table `bsl_formulaire`
--
ALTER TABLE `bsl_formulaire`
  MODIFY `id_formulaire` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
--
-- AUTO_INCREMENT pour la table `bsl_formulaire__page`
--
ALTER TABLE `bsl_formulaire__page`
  MODIFY `id_page` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;
--
-- AUTO_INCREMENT pour la table `bsl_formulaire__question`
--
ALTER TABLE `bsl_formulaire__question`
  MODIFY `id_question` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;
--
-- AUTO_INCREMENT pour la table `bsl_formulaire__reponse`
--
ALTER TABLE `bsl_formulaire__reponse`
  MODIFY `id_reponse` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;
--
-- AUTO_INCREMENT pour la table `bsl_formulaire__valeur`
--
ALTER TABLE `bsl_formulaire__valeur`
  MODIFY `id_valeur` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=143;
--
-- AUTO_INCREMENT pour la table `bsl_mesure`
--
ALTER TABLE `bsl_mesure`
  MODIFY `id_mesure` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
--
-- AUTO_INCREMENT pour la table `bsl_offre`
--
ALTER TABLE `bsl_offre`
  MODIFY `id_offre` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=276;
--
-- AUTO_INCREMENT pour la table `bsl_professionnel`
--
ALTER TABLE `bsl_professionnel`
  MODIFY `id_professionnel` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=81;
--
-- AUTO_INCREMENT pour la table `bsl_recherche`
--
ALTER TABLE `bsl_recherche`
  MODIFY `id_recherche` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;
--
-- AUTO_INCREMENT pour la table `bsl_territoire`
--
ALTER TABLE `bsl_territoire`
  MODIFY `id_territoire` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;
--
-- AUTO_INCREMENT pour la table `bsl_theme`
--
ALTER TABLE `bsl_theme`
  MODIFY `id_theme` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;
--
-- AUTO_INCREMENT pour la table `bsl_utilisateur`
--
ALTER TABLE `bsl_utilisateur`
  MODIFY `id_utilisateur` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;
--
-- AUTO_INCREMENT pour la table `bsl__droits`
--
ALTER TABLE `bsl__droits`
  MODIFY `id_statut` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;
--
-- AUTO_INCREMENT pour la table `bsl__region`
--
ALTER TABLE `bsl__region`
  MODIFY `id_region` int(2) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
