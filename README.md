# SoutenancePro

Application web de gestion des soutenances de fin d’études, développée avec **Symfony 6.4**.

Projet réalisé dans le cadre de l'examen final — *Développement Web II* — IT Net Institute of Technology (Licence GL/WIM, année académique 2025-2026).

## Sommaire

- [Fonctionnalités](#fonctionnalités)
- [Prérequis](#prérequis)
- [Installation](#installation)
- [Lancement du projet](#lancement-du-projet)
- [Comptes de test](#comptes-de-test)
- [Structure du projet](#structure-du-projet)
- [Stack technique](#stack-technique)

## Fonctionnalités

- Authentification par rôles (**Administrateur** / **Enseignant** / **Étudiant**)
- Gestion des étudiants (CRUD + recherche par nom)
- Gestion des enseignants (CRUD, avec création automatique d'un compte utilisateur lié)
- Gestion des salles (CRUD)
- Programmation des soutenances avec constitution du jury (président, rapporteur, examinateur)
- Détection automatique des conflits : salle déjà occupée, enseignant mobilisé sur un autre jury au même horaire
- Recherche de soutenances par date
- Tableaux de bord différenciés (statistiques admin / vue personnelle enseignant)
- Gestion des comptes utilisateurs (rôles)

## Prérequis

| Outil | Version |
|---|---|
| PHP | 8.2 ou supérieur |
| Composer | 2.x |
| MySQL / MariaDB | 8.0 ou supérieur |
| Symfony CLI *(optionnel)* | dernière version |

Extensions PHP requises : `pdo_mysql`, `intl`, `mbstring`, `xml`, `ctype`, `iconv`.

## Installation

1. **Cloner le dépôt**
   ```bash
   git clone https://github.com/hub123HU/SoutenancePro.git
   cd SoutenancePro
