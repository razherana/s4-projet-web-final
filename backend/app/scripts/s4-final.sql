-- Active: 1738011862925@@127.0.0.1@3306@s4_exam_web
CREATE TABLE `s4_final_etablissements` (
    `id` integer PRIMARY KEY AUTO_INCREMENT,
    `nom` varchar(255)
);

CREATE TABLE `s4_final_budgets` (
    `id` integer PRIMARY KEY AUTO_INCREMENT,
    `montant` float,
    `etablissement_id` int,
    `date` datetime
);

CREATE TABLE `s4_final_type_users` (
    `id` integer PRIMARY KEY AUTO_INCREMENT,
    `nom` varchar(255)
);

CREATE TABLE `s4_final_users` (
    `id` integer PRIMARY KEY AUTO_INCREMENT,
    `nom` varchar(255),
    `prenom` varchar(255),
    `email` varchar(255),
    `password` varchar(255),
    `type_user_id` integer
);

CREATE TABLE `s4_final_type_prets` (
    `id` integer PRIMARY KEY AUTO_INCREMENT,
    `nom` varchar(255),
    `taux` float
);

CREATE TABLE `s4_final_prets` (
    `id` integer PRIMARY KEY AUTO_INCREMENT,
    `type_pret_id` int,
    `user_id` int,
    `montant` float
);
