-- phpMyAdmin SQL Dump
-- version 5.0.1
-- https://www.phpmyadmin.net/
--
-- Hôte : campusbook_mysql
-- Généré le : lun. 04 jan. 2021 à 10:49
-- Version du serveur :  8.0.19
-- Version de PHP : 7.4.1

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;


DROP DATABASE sf4;

CREATE DATABASE sf4;
USE sf4;

--
-- Base de données : `sf4`
--

-- --------------------------------------------------------

--
-- Structure de la table `historique`
--

CREATE TABLE `historique` (
  `id` int NOT NULL,
  `livre_id` int NOT NULL,
  `user_id` int NOT NULL,
  `type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `date_historique` datetime NOT NULL,
  `date_retour_estimer` date DEFAULT NULL,
  `date_retour_reelle` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `livre`
--

CREATE TABLE `livre` (
  `id` int NOT NULL,
  `reserver_par_id` int DEFAULT NULL,
  `theme_id` int NOT NULL,
  `type_id` int NOT NULL,
  `nom` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `auteur` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `date_sortie` date NOT NULL,
  `description` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `etat` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `hash` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nb_jours_pret` int NOT NULL,
  `bloquer_prochaine_reservation` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `livre`
--

INSERT INTO `livre` (`id`, `reserver_par_id`, `theme_id`, `type_id`, `nom`, `auteur`, `date_sortie`, `description`, `etat`, `hash`, `nb_jours_pret`, `bloquer_prochaine_reservation`) VALUES
(1, NULL, 20, 1, 'Php Et Mysql Pour Les Nuls', 'Janet Valade', '2017-08-31', 'Php pour les nuls', 'disponible', '23113d123zed1az463z', 10, 0);

-- --------------------------------------------------------

--
-- Structure de la table `theme`
--

CREATE TABLE `theme` (
  `id` int NOT NULL,
  `type_id` int NOT NULL,
  `nom` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `theme`
--

INSERT INTO `theme` (`id`, `type_id`, `nom`) VALUES
(12, 2, 'Policier'),
(13, 2, 'Littérature'),
(14, 2, 'Romantique'),
(15, 3, 'Supers Héros'),
(16, 3, 'Horreur'),
(17, 4, 'Kodomo'),
(18, 4, 'Hentaï'),
(19, 4, 'Shojo'),
(20, 1, 'DEV'),
(21, 1, 'OPS'),
(22, 1, 'DEV/OPS');

-- --------------------------------------------------------

--
-- Structure de la table `type`
--

CREATE TABLE `type` (
  `id` int NOT NULL,
  `nom` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `type`
--

INSERT INTO `type` (`id`, `nom`) VALUES
(1, 'Revue technique'),
(2, 'Roman'),
(3, 'BD'),
(4, 'Manga');

-- --------------------------------------------------------

--
-- Structure de la table `user`
--

CREATE TABLE `user` (
  `id` int NOT NULL,
  `username` varchar(180) COLLATE utf8mb4_unicode_ci NOT NULL,
  `roles` json NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `enabled` tinyint(1) DEFAULT NULL,
  `nom` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `prenom` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `user`
--

INSERT INTO `user` (`id`, `username`, `roles`, `password`, `enabled`, `nom`, `prenom`) VALUES
(1, 'adm@adm.adm', '[\"ROLE_SUPER_ADMIN\"]', '$argon2i$v=19$m=65536,t=4,p=1$WDdWeUJ5anAzQ1k2bGlvVg$zGDUwROmufHmNNEhqNhStDGgF85ajTDX+SYeYPFwMkc', 1, NULL, NULL),
(2, 'test@test.test', '[\"ROLE_USER\"]', '$argon2i$v=19$m=65536,t=4,p=1$UkFOU3cxNjROZEdPR0dNVQ$Obu21Dcesn3DS3l1rtwumPssmX7B/NRfb3matVwH43A', 1, NULL, NULL);

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `historique`
--
ALTER TABLE `historique`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_EDBFD5EC37D925CB` (`livre_id`),
  ADD KEY `IDX_EDBFD5ECA76ED395` (`user_id`);

--
-- Index pour la table `livre`
--
ALTER TABLE `livre`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_AC634F9941FB7070` (`reserver_par_id`),
  ADD KEY `IDX_AC634F9959027487` (`theme_id`),
  ADD KEY `IDX_AC634F99C54C8C93` (`type_id`);

--
-- Index pour la table `theme`
--
ALTER TABLE `theme`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_9775E708C54C8C93` (`type_id`);

--
-- Index pour la table `type`
--
ALTER TABLE `type`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `UNIQ_8D93D649F85E0677` (`username`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `historique`
--
ALTER TABLE `historique`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `livre`
--
ALTER TABLE `livre`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT pour la table `theme`
--
ALTER TABLE `theme`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT pour la table `type`
--
ALTER TABLE `type`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT pour la table `user`
--
ALTER TABLE `user`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `historique`
--
ALTER TABLE `historique`
  ADD CONSTRAINT `FK_EDBFD5EC37D925CB` FOREIGN KEY (`livre_id`) REFERENCES `livre` (`id`),
  ADD CONSTRAINT `FK_EDBFD5ECA76ED395` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`);

--
-- Contraintes pour la table `livre`
--
ALTER TABLE `livre`
  ADD CONSTRAINT `FK_AC634F9941FB7070` FOREIGN KEY (`reserver_par_id`) REFERENCES `user` (`id`),
  ADD CONSTRAINT `FK_AC634F9959027487` FOREIGN KEY (`theme_id`) REFERENCES `theme` (`id`),
  ADD CONSTRAINT `FK_AC634F99C54C8C93` FOREIGN KEY (`type_id`) REFERENCES `type` (`id`);

--
-- Contraintes pour la table `theme`
--
ALTER TABLE `theme`
  ADD CONSTRAINT `FK_9775E708C54C8C93` FOREIGN KEY (`type_id`) REFERENCES `type` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
