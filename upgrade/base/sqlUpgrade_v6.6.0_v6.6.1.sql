-- Mise à jour n° de version
UPDATE `system` SET `value`='v6.6.1' WHERE `name`='base' and `groupe`='module';

-- Ajout mutuelle 

INSERT IGNORE INTO `data_types` (`groupe`, `name`, `placeholder`, `label`, `description`, `validationRules`, `validationErrorMsg`, `formType`, `formValues`, `module`, `cat`, `fromID`, `creationDate`, `durationLife`, `displayOrder`) VALUES
('admin', 'nmu', '', 'Numéro de mutuelle', 'numéro de mutelle', '', '', 'text', '', 'base', @catID, '1', '2019-01-01 00:00:00', '3600', '1');

-- Ajout d'un option pour n'evoyer des rappel sms que pour certains types de consultation
-- Ajout pour activer ou non la génération des codes bare rpps et adeli
INSERT IGNORE INTO `configuration` (`name`, `level`, `toID`, `module`, `cat`, `type`, `description`, `value`) VALUES
('smsTypeRdvPourRappel', 'default', '0', '', 'Rappels SMS', 'vide/text', 'N\'envoyer les rappels SMS que pour les types de rendez-vous listés (placer les types de RDV entre "[]" et séparés par des virgules, ex : "[C1],[C2]"), laisser vide pour envoyer des rappels pour tous les types de rendez-vous.', ''),
('activGenBarreCode', 'default', '0', '', 'Options', 'true/false', 'Activer ou non la fonctionnalité permettant de générer les codes barres RPPS et ADELI.', 'true');
