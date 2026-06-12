## 1. Install Debugbar

- [x] 1.1 Run `composer require --dev barryvdh/laravel-debugbar` to install Debugbar

## 2. Fix N+1

- [x] 2.1 Add `->with('depenses')` to `RecuController::show()` before passing `$recu` to the view

## 3. Verify

- [x] 3.1 Run `php artisan test` to confirm all 36 tests still pass
- [x] 3.2 Run `php artisan route:list` to confirm all resource routes are registered
