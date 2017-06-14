-- phpMyAdmin SQL Dump
-- version 4.6.4
-- https://www.phpmyadmin.net/
--
-- Client :  127.0.0.1
-- Généré le :  Jeu 08 Juin 2017 à 10:16
-- Version du serveur :  5.7.14
-- Version de PHP :  5.6.25

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

--
-- Base de données :  `boussole`
--

-- --------------------------------------------------------

--
-- Structure de la table `bsl_professionnel_villes`
--

CREATE TABLE `bsl_professionnel_villes` (
  `id_professionnel` int(11) NOT NULL,
  `code_insee` varchar(5) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Contenu de la table `bsl_professionnel_villes`
--

INSERT INTO `bsl_professionnel_villes` (`id_professionnel`, `code_insee`) VALUES
(1, '51437'),
(1, '51439'),
(1, '51448'),
(1, '51451'),
(1, '51454'),
(1, '51458'),
(1, '51464'),
(1, '51466'),
(11, '75113'),
(23, '54001'),
(24, '59001'),
(24, '64001');

--
-- Index pour les tables exportées
--

--
-- Index pour la table `bsl_professionnel_villes`
--
ALTER TABLE `bsl_professionnel_villes`
  ADD UNIQUE KEY `id_professionnel` (`id_professionnel`,`code_insee`);
