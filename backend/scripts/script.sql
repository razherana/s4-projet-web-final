-- Active: 1739989451833@@127.0.0.1@3306@s4_exam_web
CREATE TABLE `s4_fonds` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `montant_initial` FLOAT(53) NOT NULL,
    `description` TEXT NOT NULL,
    `source_fond_id` INT NOT NULL,
    `date_ajout` DATETIME NOT NULL DEFAULT NOW()
);

CREATE TABLE `s4_source_fonds` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `nom` VARCHAR(255) NOT NULL
);

ALTER TABLE `s4_source_fonds`
ADD UNIQUE `s4_source_fonds_nom_unique` (`nom`);

CREATE TABLE `s4_type_prets` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `nom` VARCHAR(255) NOT NULL,
    `taux_interet` FLOAT(53) NOT NULL,
    `duree_min` FLOAT(53) NOT NULL,
    `duree_max` FLOAT(53) NOT NULL
);

CREATE TABLE `s4_clients` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `email` VARCHAR(255) NOT NULL,
    `password` VARCHAR(255) NOT NULL,
    `nom` VARCHAR(255) NOT NULL,
    `prenom` VARCHAR(255) NOT NULL,
    `user_id` BIGINT NOT NULL
);

CREATE TABLE `s4_prets` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `client_id` BIGINT NOT NULL,
    `type_pret_id` BIGINT NOT NULL,
    `montant` BIGINT NOT NULL,
    `duree` BIGINT NOT NULL,
    `date_acceptation` DATETIME NULL,
    `date_refus` DATETIME NULL,
    `date_creation` DATETIME NOT NULL
);

CREATE TABLE `s4_pret_retour_historiques` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `pret_id` BIGINT NOT NULL,
    `montant` FLOAT(53) NOT NULL,
    `date_retour` DATE NOT NULL
);

CREATE TABLE `s4_users` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `nom` VARCHAR(255) NOT NULL
);

CREATE TABLE `s4_fond_historiques` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `fond_id` BIGINT NOT NULL,
    `description` VARCHAR(255) NOT NULL,
    `montant` FLOAT(53) NOT NULL,
    `est_sortie` BOOLEAN NOT NULL DEFAULT 1,
    `date` DATETIME NOT NULL DEFAULT NOW()
);