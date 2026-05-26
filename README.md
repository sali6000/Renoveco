--------------------------------------------------------------------------------
🏗️ RenoveConstruct.be (Renoveco)
  
Conception et mise en production d'une application métier complète développée "From Scratch" en PHP 8.1.
Ce projet a été réalisé en autonomie totale pour démontrer ma capacité à concevoir des systèmes complexes, sécurisés et performants sans dépendre d'un framework tiers
.

--------------------------------------------------------------------------------
🎯 Objectifs du Projet
L'objectif principal était de monter en compétences sur les architectures modernes de haut niveau. Plutôt que d'utiliser des outils pré-construits, j'ai choisi de réimplémenter les composants critiques d'un framework pour en comprendre la logique interne et garantir une maîtrise totale de la dette technique
.
🏗️ Architecture Technique
Le projet suit les principes de la Clean Architecture et du MVC modulaire
 :
Core / Kernel : Développement d'un noyau d'application gérant le cycle de vie des requêtes, un conteneur d'injection de dépendances et une gestion centralisée des exceptions
.
Routing par Attributs : Système de routage moderne utilisant les attributs PHP 8.1 pour mapper les contrôleurs
.
Couches métier (Clean Architecture) :
Controller ➡️ UseCase ➡️ Service ➡️ Repository
.
Utilisation de ViewModels dédiés pour découpler totalement la logique métier de la présentation
.
Moteur de templates : Intégration de Twig pour une séparation stricte entre logique et UI
.
🛡️ Sécurité & Performance
Le projet n'est pas qu'une démonstration de code, il est conçu pour la production
 :
Sécurité renforcée : Protection contre les failles CSRF (CsrfGuard), Rate Limiting, hashage Bcrypt et validation stricte des entrées
.
SEO Technique : Génération dynamique de sitemap.xml, implémentation de micro-données JSON-LD (Schema.org) et gestion propre des redirections 301/404
.
Vitesse : Utilisation de FrankenPHP pour les performances, cache applicatif et optimisation du rendu via Vite et SCSS
.
🛠️ Stack Technique
Backend : PHP 8.1 (POO avancée, PSR-4)
.
Database : MySQL (Architecture SGBD propre)
.
Frontend : SCSS, JavaScript (Vite), Twig
.
DevOps & Infra :
Environnement conteneurisé avec Docker
.
Automatisation des sauvegardes et déploiement via Git/SSH
.
Monitoring et logs d'accès personnalisés
.
📂 Structure du projet (Aperçu)
├── config/             # Configurations (Services, Routes, DB)
├── core/               # Le "Framework" maison (Router, Kernel, DI)
├── src/
│   ├── Modules/        # Découpage modulaire du métier (Product, Auth, User...)
│   │   └── [Module]/
│   │       ├── Application/    # UseCases & ViewModels
│   │       ├── Domain/         # Entities & Interfaces
│   │       ├── Infrastructure/ # Persistence (Repositiories MySQL)
│   │       └── UI/             # Controllers & Twig Views
└── public/             # Point d'entrée et assets compilés

--------------------------------------------------------------------------------
👨‍💻 À propos de moi
Titulaire d'un Bachelier en informatique de gestion
, je combine une expérience de plus de deux ans dans le support d'environnements critiques (ERP Pharmaceutique) avec une passion profonde pour le développement logiciel moderne
.
Je suis rigoureux, méthodologique et j'accorde une importance capitale à la qualité du code (Clean Code, principes SOLID)
.
Portfolio : renoveconstruct.be
LinkedIn : [Lien vers votre profil]
Contact : s.ferlazzo@protonmail.com | 0488/71.60.96

--------------------------------------------------------------------------------
