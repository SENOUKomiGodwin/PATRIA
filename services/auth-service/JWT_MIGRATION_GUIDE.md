# JWT/Roles Middleware - Guide de Migration

Ce middleware doit être copié dans tous les autres services pour valider les tokens JWT et vérifier les rôles.

## Fichiers à copier

### 1. Middleware de Rôle
Copier `app/Http/Middleware/RoleMiddleware.php` dans chaque service.

```php
<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        $user = Auth::guard('api')->user();

        if (!$user || !$user->role) {
            return response()->json(['message' => 'Non authentifié'], 401);
        }

        if (!in_array($user->role->name, $roles)) {
            return response()->json(['message' => 'Accès non autorisé'], 403);
        }

        return $next($request);
    }
}
```

### 2. Configuration Auth
Ajouter le guard API dans `config/auth.php`:

```php
'guards' => [
    'web' => [
        'driver' => 'session',
        'provider' => 'users',
    ],
    'api' => [
        'driver' => 'jwt',
        'provider' => 'users',
    ],
],
```

### 3. Configuration JWT
Copier `config/jwt.php` dans chaque service (même configuration, le JWT_SECRET est partagé).

### 4. Bootstrap Configuration
Ajouter l'alias du middleware dans `bootstrap/app.php`:

```php
->withMiddleware(function (Middleware $middleware): void {
    $middleware->alias(['role' => \App\Http\Middleware\RoleMiddleware::class]);
})
```

### 5. Package Composer
Ajouter dans `composer.json` de chaque service:

```json
"require": {
    "tymon/jwt-auth": "^2.3"
}
```

### 6. Configuration .env
Ajouter dans le `.env` de chaque service:

```env
JWT_SECRET=shared_jwt_secret_from_env_example
JWT_TTL=480
```

## Utilisation dans les Routes

```php
Route::middleware('auth:api')->group(function () {
    // Routes requiring authentication
    
    Route::middleware('role:ADMIN,GESTIONNAIRE')->group(function () {
        // Routes requiring specific roles
    });
});
```

## Claims du Token

Le token JWT contient les claims suivants (automatiquement ajoutés par User::getJWTCustomClaims()):

- `sub`: ID de l'utilisateur
- `role`: Nom du rôle (ADMIN, GESTIONNAIRE, AGENT, AUDITEUR)
- `service_id`: ID du service de l'utilisateur

Pour accéder à ces informations dans un contrôleur:

```php
$user = Auth::guard('api')->user();
$role = $user->role->name;
$serviceId = $user->service_id;
```

## Note Importante

Le service auth-service est le seul à créer et gérer les utilisateurs. Les autres services utilisent uniquement le token pour identifier l'utilisateur et ses droits via les claims du JWT.
