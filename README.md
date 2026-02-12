# code-migrator

> **Archive** — Outil PHP de migration automatisée de code legacy (PHP 4.x sans namespaces) vers une base de code moderne avec namespaces PSR-4. Conçu comme un pipeline de transformations extensible. **Utilisé en production** dans le cadre d'une migration d'un projet legacy PHP 4.x vers PHP 5.X.

[![Coverage Status](https://coveralls.io/repos/github/starker-xp/code-migrator/badge.svg?branch=master)](https://coveralls.io/github/starker-xp/code-migrator?branch=master) [![Build Status](https://travis-ci.org/starker-xp/code-migrator.svg?branch=master)](https://travis-ci.org/starker-xp/code-migrator) [![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/starker-xp/code-migrator/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/starker-xp/code-migrator/?branch=master)

## Contexte

Sur un projet de migration d'une application legacy PHP 4.x vers PHP 5.X, je me suis retrouvé confronté à une base de code sans namespaces, avec des fichiers `.class.php`, des `require_once` partout et plusieurs classes par fichier. La migration manuelle de centaines de fichiers étant irréaliste dans le délai imparti (1 mois), j'ai développé cet outil pour automatiser les transformations récurrentes.

Cet outil a été utilisé conjointement avec [database-checker](https://github.com/starker-xp/database-checker) qui gérait la partie synchronisation de schéma MySQL.

> *Analyse rétrospective réalisée en 2026 dans le cadre d'un nettoyage et d'une mise en archive de mes dépôts GitHub/GitLab.*

## Fonctionnalités

Le code-migrator fonctionne comme un **pipeline de transformations** : chaque adapter applique une transformation sur chaque fichier du projet, dans l'ordre défini.

```
  Projet legacy
       │
       ▼
   ┌──────────┐
   │  Runner  │──► Parcourt tous les fichiers PHP du projet
   └────┬─────┘
        │
        ▼
   ┌───────────────────┐
   │  ReplaceContent   │──► Normalise les fins de ligne (\r\n → \n)
   └────────┬──────────┘
            ▼
   ┌───────────────────┐
   │  ObjectExtractor  │──► Extrait chaque classe/interface/trait dans son propre fichier
   └────────┬──────────┘
            ▼
   ┌───────────────────────┐
   │  ChangeFileExtension  │──► Renomme .class.php → .php
   └────────┬──────────────┘
            ▼
   ┌───────────────────┐
   │  ValidNamespace   │──► Ajoute le namespace PSR-4 basé sur le chemin du fichier
   └────────┬──────────┘
            ▼
   ┌───────────────────┐
   │  RemoveComment    │──► Supprime les commentaires obsolètes
   └────────┬──────────┘
            ▼
     Projet modernisé
```

### Adapters disponibles

| Adapter | Description |
|---|---|
| `ReplaceContent` | Recherche/remplacement de chaînes dans tous les fichiers |
| `ObjectExtractor` | Parse le code PHP via le tokenizer natif et extrait chaque classe/interface/trait dans un fichier dédié |
| `ChangeFileExtension` | Renomme les extensions de fichiers (ex: `.class.php` → `.php`) et met à jour les références |
| `ValidNamespace` | Déduit et injecte le namespace PSR-4 correct basé sur le chemin du fichier |
| `RemoveComment` | Supprime tous les commentaires PHP (inline, bloc, PHPDoc) via le tokenizer |

## Architecture

```
src/
├── Runner.php              # Orchestre le pipeline : parcourt les fichiers, exécute les adapters
├── AbstractAdapter.php     # Classe de base : tokenisation, lecture/écriture, résolution de namespace
├── AdapterInterface.php    # Contrat pour créer un nouvel adapter
├── ListContents.php        # Trait : parcours récursif du filesystem
└── Adapter/
    ├── ChangeFileExtension.php
    ├── ObjectExtractor.php
    ├── RemoveComment.php
    ├── ReplaceContent.php
    └── ValidNamespace.php

tests/
└── Adapter/
    ├── RemoveCommentTest.php     # Test avec vfsStream (filesystem virtuel)
    └── ReplaceContentTest.php

work/
└── Adapter/
    └── ConvertRequireToUseForHektor.php   # Adapter spécifique au projet client
```

## Points techniques notables

- **Tokenizer PHP natif** : `ObjectExtractor` utilise `token_get_all()` pour parser le code source et extraire les classes/interfaces/traits — bien plus fiable que des regex
- **Architecture pipeline extensible** : ajouter une transformation = créer une classe implémentant `AdapterInterface`. Le `Runner` les exécute séquentiellement
- **Filesystem virtuel en tests** : utilisation de `vfsStream` pour tester les transformations de fichiers sans toucher au disque réel
- **Résolution de namespace automatique** : `ValidNamespace` déduit le namespace PSR-4 à partir du chemin du fichier et du mapping de base défini
- **Détection de conflits** : `ObjectExtractor` vérifie qu'un fichier cible n'existe pas déjà avant d'écrire, évitant les écrasements silencieux

## Utilisation

```php
$runner = new \Starkerxp\CodeMigrator\Runner('./project/');

// Normaliser les fins de ligne
$runner->addAdapter(new ReplaceContent(["\r\n"], "\n"));

// Extraire chaque classe dans son propre fichier
$runner->addAdapter(new ObjectExtractor($baseNamespaces));

// Renommer .class.php → .php
$runner->addAdapter(new ChangeFileExtension('.class.php', '.php'));

// Ajouter les namespaces PSR-4
$runner->addAdapter(new ValidNamespace($baseNamespaces));

// Supprimer les commentaires obsolètes
$runner->addAdapter(new RemoveComment());

$runner->run();
```

## Compétences démontrées

- **Résolution de problème concret** : automatisation d'une migration de code legacy sous contrainte de temps
- **Parsing de code PHP** : utilisation du tokenizer natif pour l'analyse et la transformation de code source
- **Architecture extensible** : pattern pipeline/adapter permettant d'ajouter des transformations sans modifier le code existant
- **Tests** : PHPUnit + vfsStream pour tester les I/O fichiers
- **CI/CD** : Travis CI + Coveralls + Scrutinizer

## Limitations connues

Ce projet étant un outil développé dans un délai contraint (1 mois), certaines limitations ont été identifiées avec le recul :

| Limitation | Détail |
|---|---|
| **Tests partiels** | Seuls `RemoveComment` et `ReplaceContent` sont testés — les adapters les plus complexes (`ObjectExtractor`, `ValidNamespace`) ne le sont pas |
| **Adapter spécifique client** | Le dossier `work/` contient un adapter spécifique au projet client (non générique) |

## Commandes de développement

```bash
# Lancer les tests
gulp phpunit

# Watcher (relance les tests à chaque modification)
gulp start

# Couverture de code (nécessite xdebug)
gulp coverage
```

## Prérequis

- PHP >= 5.4
- Symfony OptionsResolver ^3.4
- PSR Log ^1.0
- PHPUnit ^5.7 (dev)

## Licence

MIT
