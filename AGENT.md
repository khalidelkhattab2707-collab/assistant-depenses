# AGENTS.md

## Stack
- Laravel 11, PHP 8.2, MySQL
- Blade + Bootstrap 5
- Laravel Breeze (auth)
- laravel/ai SDK → Groq API
- Queue: database driver

## Conventions
- Noms de classes en PascalCase, méthodes en camelCase
- Toujours utiliser $request->validated(), jamais $request->all()
- Tous les appels IA passent par le SDK laravel/ai, jamais Http::
- Chaque Job implémente failed() pour gérer les erreurs

## Branches
- feature/recus-crud
- feature/extraction-ia
- feature/queue-traitement