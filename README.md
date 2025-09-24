# Projet "forum-app" avec Symfony

Un projet de forum dédié au personnes fan de Disney développé avec **Symfony** pour pouvoir échanger.

## Description

Ce projet est une application de commerce électronique créée avec Symfony 6. Elle permet aux utilisateurs de :
- Consulter un sujet du forum
- Ajouter un commentaire

---

## Technologies utilisées

Voici les technologies et services utilisés pour ce projet :

- **Symfony 6** : Framework PHP pour les applications web
- **Doctrine ORM** : Gestion de la base de données
- **Twig** : Moteur de templates pour l'affichage HTML

---

## Prérequis

- PHP 8.2 ou supérieur
- Composer
- Symfony CLI
- Serveur SQL (MySQL, SQLite, PostgreSQL, …)
- Node.js et npm (ou yarn) pour les assets front-end

---

## Installation

Suivez ces étapes pour installer et lancer le projet localement :

### 1. Clonez le dépôt

Cloner ce projet depuis GitHub :
```bash
git clone https://github.com/marine1512/forum-app.git
cd forum-app
```

### 2. Installez les dépendances
Installez les dépendances PHP avec Composer :
```bash
composer install
```

Installez les dépendances front-end :
```bash
npm install
npm run dev
```

### 3. Configurez le fichier `.env` :
```bash
DATABASE_URL="mysql://admin:admin@127.0.0.1:3307/dev_app"
```

### 4. Configurez la base de données

Exécutez les migrations pour créer les tables nécessaires dans votre base de données :
```bash
php bin/console doctrine:database:create
php bin/console doctrine:migrations:migrate
```

Importer les données :
```bash
php bin/console doctrine:fixtures:load
```

### 5. Lancez l'application

Lancez le serveur Symfony avec :
```bash
symfony server:start
```

Votre application sera disponible à l’adresse [http://127.0.0.1:8000](http://127.0.0.1:8000).

---
