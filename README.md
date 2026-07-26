# SoutenancePro
Gestion des Soutenances de Fin d'Études (SoutenancePro) 
# SoutenancePro

Application web de gestion des soutenances de fin d'études, développée avec **Symfony 6.4**.

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

- Authentification par rôles (**Administrateur** / **Enseignant**)
- Gestion des étudiants (CRUD + recherche par nom)
- Gestion des enseignants (CRUD, avec création automatique d'un compte utilisateur lié)
- Gestion des salles (CRUD)
- Programmation des soutenances avec constitution du jury (président, rapporteur, examinateur)
- Détection automatique des conflits : salle déjà occupée, enseignant déjà mobilisé sur un autre jury au même horaire
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
   ```

2. **Installer les dépendances PHP**

   ```bash
   composer install
   ```

3. **Configurer la base de données**

   Copier `.env` en `.env.local` et adapter la ligne `DATABASE_URL` :

   ```
   DATABASE_URL="mysql://root:@127.0.0.1:3306/soutenance_db?serverVersion=8.0&charset=utf8mb4"
   ```

4. **Créer la base de données et les tables**

   ```bash
   php bin/console doctrine:database:create
   php bin/console make:migration
   php bin/console doctrine:migrations:migrate
   ```

5. **Créer un compte administrateur**

   ```bash
   php bin/console security:hash-password VotreMotDePasse "App\Entity\User" --no-interaction
   ```

   Copier le hash affiché puis l'insérer en base (via phpMyAdmin ou la console MySQL) :

   ```sql
   INSERT INTO user (email, roles, password, nom, prenom)
   VALUES ('admin@soutenancepro.local', '["ROLE_ADMIN"]', 'LE_HASH_GENERE', 'Admin', 'Principal');
   ```

## Lancement du projet

**Avec le serveur PHP intégré :**

```bash
php -S localhost:8000 -t public
```

Puis ouvrir : http://localhost:8000/

**Avec la Symfony CLI :**

```bash
symfony server:start
```

**Avec XAMPP/WAMP** : placer le projet dans `htdocs`/`www` et accéder à `http://localhost/SoutenancePro/public/`.

## Comptes de test

| Rôle | Email | Mot de passe |
|---|---|---|
| Administrateur | admin@soutenancepro.local | *(celui défini à l'installation)* |
| Enseignant | *(créé depuis le menu "Enseignants" une fois connecté en admin)* | *(défini à la création)* |

## Structure du projet

```
SoutenancePro/
├── config/              Configuration Symfony (sécurité, routes, doctrine...)
├── migrations/          Migrations Doctrine
├── public/               Point d'entrée web + assets (css/app.css)
├── src/
│   ├── Controller/       Contrôleurs (Dashboard, Etudiant, Enseignant, Salle, Soutenance, User, Security)
│   ├── Entity/           Entités Doctrine (User, Etudiant, Enseignant, Salle, Soutenance)
│   ├── Form/             Types de formulaire
│   └── Repository/       Repositories Doctrine
├── templates/            Vues Twig
└── README.md
```

## Stack technique

- **Framework** : Symfony 6.4 (LTS)
- **ORM** : Doctrine
- **Base de données** : MySQL 8.0
- **Frontend** : Twig, Bootstrap 5.3, Bootstrap Icons
- **Sécurité** : Symfony Security (authentification par formulaire, hachage bcrypt/argon2 automatique)
