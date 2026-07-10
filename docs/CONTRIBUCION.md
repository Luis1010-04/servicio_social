# Guía de Contribución — IoT_Project

## Tabla de Contenidos

1. [Flujo de Trabajo](#1-flujo-de-trabajo)
2. [Convenciones de Código](#2-convenciones-de-código)
3. [Estructura de Commits](#3-estructura-de-commits)
4. [Pull Requests](#4-pull-requests)
5. [Testing](#5-testing)

---

## 1. Flujo de Trabajo

### 1.1 Branches

| Branch | Propósito |
|--------|-----------|
| `main` | Código en producción (estable) |
| `develop` | Código en desarrollo (integración) |
| `feature/*` | Nuevas funcionalidades |
| `fix/*` | Corrección de bugs |
| `hotfix/*` | Corrección urgente en producción |

### 1.2 Flujo

1. Crear una rama desde `develop`:
   ```bash
   git checkout develop
   git pull origin develop
   git checkout -b feature/nueva-funcionalidad
   ```

2. Desarrollar los cambios.
3. Commitear con mensajes descriptivos.
4. Push y crear Pull Request hacia `develop`.
5. Code review y merge.

---

## 2. Convenciones de Código

### 2.1 PHP (PSR-12)

```php
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Componente;

class ComponenteController extends Controller
{
    /**
     * Listar todos los componentes.
     */
    public function index()
    {
        $datos = Componente::all();
        return view('componentes.index', compact('datos'));
    }
}
```

**Reglas:**
- Espacios de nombres en PascalCase.
- Clases en PascalCase.
- Métodos en camelCase.
- Propiedades en camelCase.
- Una llave de apertura por línea.
- Comments con `/** */` para PHPDoc.

### 2.2 Blade

```blade
@extends('layouts.main')

@section('content')
    <div class="container">
        <h1>{{ $titulo }}</h1>

        @if($datos->isEmpty())
            <p>No hay registros.</p>
        @else
            @foreach($datos as $item)
                <p>{{ $item->nombre }}</p>
            @endforeach
        @endif
    </div>
@endsection
```

### 2.3 JavaScript

```javascript
// Usar const/let, nunca var
const nombre = 'IoT_Project';

// Funciones en camelCase
function obtenerDatos() {
    return fetch('/api/datos')
        .then(response => response.json());
}
```

### 2.4 Nomenclatura de Archivos

| Tipo | Convención | Ejemplo |
|------|-----------|---------|
| Controladores | PascalCase | `ComponenteController.php` |
| Modelos | PascalCase | `Componente.php` |
| Vistas | snake_case | `index.blade.php` |
| Migraciones | timestamp | `2026_07_04_...` |
| Seeders | PascalCase | `CatalogosSeeder.php` |

---

## 3. Estructura de Commits

### Formato

```
<tipo>(<alcance>): <descripción corta>

<descripción opcional más detallada>

<footer>
```

### Tipos

| Tipo | Descripción |
|------|------------|
| `feat` | Nueva funcionalidad |
| `fix` | Corrección de bug |
| `docs` | Cambios en documentación |
| `style` | Cambios de formato (no afectan lógica) |
| `refactor` | Refactorización de código |
| `test` | Agregar o modificar tests |
| `chore` | Tareas de mantenimiento |

### Ejemplos

```bash
git commit -m "feat(reportes): agregar exportación a PDF"
git commit -m "fix(auth): corregir throttle en login"
git commit -m "docs: actualizar README con instrucciones de instalación"
git commit -m "refactor(componentes): extraer lógica a servicio"
```

---

## 4. Pull Requests

### Checklist

- [ ] El código sigue las convenciones PSR-12.
- [ ] No hay errores de PHP (`php artisan lint`).
- [ ] Las migraciones son reversibles.
- [ ] Los permisos de rutas son correctos.
- [ ] La documentación está actualizada.
- [ ] No se exponen credenciales o secretos.

### Plantilla

```markdown
## Descripción
Breve descripción de los cambios.

## Tipo de Cambio
- [ ] Nueva funcionalidad
- [ ] Corrección de bug
- [ ] Refactorización
- [ ] Documentación

## Pruebas
- [ ] Tests existentes pasan
- [ ] Nuevos tests agregados (si aplica)

## Capturas de Pantalla (si aplica)
```

---

## 5. Testing

### Ejecutar Tests

```bash
# Todos los tests
php artisan test

# Tests específicos
php artisan test --filter=ComponenteTest

# Con cobertura
php artisan test --coverage
```

### Estrutura de un Test

```php
<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_con_credenciales_validas(): void
    {
        User::factory()->create([
            'email' => 'test@test.com',
            'password' => bcrypt('password'),
        ]);

        $response = $this->post('/logear', [
            'email' => 'test@test.com',
            'password' => 'password',
        ]);

        $response->assertRedirect('/home');
    }
}
```
