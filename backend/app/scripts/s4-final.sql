CREATE TABLE `etablissements` (
    `id` integer PRIMARY KEY,
    `nom` varchar(255)
);

CREATE TABLE `budgets` (
    `id` integer PRIMARY KEY,
    `montant` float,
    `etablissement_id` int,
    `date` datetime
);

CREATE TABLE `type_users` (
    `id` integer PRIMARY KEY,
    `nom` varchar(255)
);

CREATE TABLE `users` (
    `id` integer PRIMARY KEY,
    `nom` varchar(255),
    `prenom` varchar(255),
    `email` varchar(255),
    `password` varchar(255),
    `type_user_id` integer
);

CREATE TABLE `type_prets` (
    `id` integer PRIMARY KEY,
    `nom` varchar(255),
    `taux` float
);

CREATE TABLE `prets` (
    `id` integer PRIMARY KEY,
    `type_pret_id` int,
    `user_id` int,
    `montant` float
);

ALTER TABLE `type_users`
ADD FOREIGN KEY (`id`) REFERENCES `users` (`type_user_id`);

ALTER TABLE `users`
ADD FOREIGN KEY (`id`) REFERENCES `prets` (`user_id`);

ALTER TABLE `type_prets`
ADD FOREIGN KEY (`id`) REFERENCES `prets` (`type_pret_id`);

ALTER TABLE `etablissements`
ADD FOREIGN KEY (`id`) REFERENCES `budgets` (`etablissement_id`);