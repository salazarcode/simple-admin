# Arquitectura de Componentes - Gestión de Usuarios

## Resumen General

La página de gestión de usuarios implementa una arquitectura de componentes modular usando Laravel Livewire con un diseño de 2 columnas inspirado en Microsoft Teams. Esta arquitectura separa claramente la lógica de negocio, la presentación y la interacción del usuario.

## Estructura de Archivos

```
app/Livewire/
└── UsersComponent.php                    # Controlador principal Livewire

resources/views/
├── layouts/
│   └── app.blade.php                     # Layout principal de la aplicación
├── components/
│   ├── simple-crud-manager.blade.php     # Componente CRUD Manager genérico
│   └── user-detail-view.blade.php        # Vista de detalle del usuario
└── livewire/
    └── users-component.blade.php         # Vista principal del componente
```

## Componentes y su Función

### 1. Layout Principal (`app.blade.php`)
**Ubicación:** `/resources/views/layouts/app.blade.php`

**Función:** 
- Define la estructura base de la aplicación con sidebar y área de contenido
- Implementa flexbox para layout responsive
- Configura el contenedor principal con altura completa

**Elementos clave:**
```blade
<div class="h-screen flex flex-shrink-0">
    <!-- Sidebar (24 unidades de ancho) -->
    <div class="w-24" style="background-color: #133215;">
        @livewire('navigation-menu')
    </div>
    
    <!-- Área de contenido principal -->
    <div class="flex flex-1 flex-col" style="background-color: #F3E8D3;">
        <header class="shadow flex-shrink-0">...</header>
        <main class="flex-1 overflow-auto">
            {{ $slot }}
        </main>
    </div>
</div>
```

### 2. Controlador Livewire (`UsersComponent.php`)
**Ubicación:** `/app/Livewire/UsersComponent.php`

**Función:**
- Maneja toda la lógica de negocio para usuarios
- Gestiona el estado de la aplicación (usuarios seleccionados, modales, formularios)
- Implementa CRUD completo (Create, Read, Update, Delete)
- Controla la visibilidad de contraseñas

**Propiedades principales:**
```php
// Control de modales
public $showUserModal = false;
public $showDeleteUserModal = false;

// Estado del usuario seleccionado
public $selectedUser = null;

// Datos del formulario
public $userName = '';
public $userEmail = '';
public $userPassword = '';
public $userPasswordConfirmation = '';
public $userRoles = [];

// Visibilidad de contraseñas
public $showPassword = false;
public $showPasswordConfirmation = false;

// Búsqueda
public $searchUsers = '';
```

**Métodos principales:**
- `selectUser($userId)` - Selecciona usuario para mostrar detalles
- `createUser()` - Abre modal para crear usuario
- `editUser($userId)` - Abre modal para editar usuario
- `saveUser()` - Valida y guarda usuario (con trim de contraseñas)
- `deleteUser()` - Elimina usuario
- `togglePasswordVisibility()` - Controla visibilidad de contraseña

### 3. Vista Principal (`users-component.blade.php`)
**Ubicación:** `/resources/views/livewire/users-component.blade.php`

**Función:**
- Define la estructura de la página de usuarios
- Integra el componente CRUD manager
- Contiene los modales de creación/edición y confirmación de eliminación

**Estructura:**
```blade
<div>
    <!-- Header con título -->
    <x-slot name="header">...</x-slot>
    
    <!-- Mensajes de éxito/error -->
    @if (session('success'))...@endif
    
    <!-- CRUD Manager -->
    <x-simple-crud-manager>
        @if($selectedUser)
            <x-user-detail-view :item="$selectedUser" />
        @endif
    </x-simple-crud-manager>
    
    <!-- Modal de usuario -->
    @if($showUserModal)...@endif
    
    <!-- Modal de confirmación de eliminación -->
    @if($showDeleteUserModal)...@endif
</div>
```

### 4. CRUD Manager Genérico (`simple-crud-manager.blade.php`)
**Ubicación:** `/resources/views/components/simple-crud-manager.blade.php`

**Función:**
- Componente reutilizable para cualquier entidad CRUD
- Implementa layout de 2 columnas (lista + detalle)
- Maneja búsqueda, paginación y estado de selección

**Estructura de 2 columnas:**
```blade
<div class="flex h-full min-h-[calc(100vh-80px)] bg-white shadow-xl rounded-lg overflow-hidden">
    <!-- Columna 1: Lista de Items (320px de ancho) -->
    <div class="w-80 border-r border-gray-200 flex flex-col">
        <!-- Header con búsqueda y botón crear -->
        <div class="p-4 border-b border-gray-200 bg-gray-50">
            <input wire:model.live.debounce.300ms="searchUsers" ...>
            <button wire:click="createUser" ...>Crear Usuario</button>
        </div>
        
        <!-- Lista scrolleable de usuarios -->
        <div class="flex-1 overflow-y-auto">
            @foreach($items as $item)
                <div wire:click="selectUser({{ $item->id }})" 
                     class="p-4 cursor-pointer hover:bg-gray-50 {{ selección activa }}">
                    <!-- Avatar, nombre, email, roles -->
                </div>
            @endforeach
        </div>
        
        <!-- Paginación -->
        <div class="border-t border-gray-200 p-4">
            {{ $items->links() }}
        </div>
    </div>
    
    <!-- Columna 2: Vista de Detalle (flexible) -->
    <div class="flex-1">
        {{ $slot }} <!-- Aquí se inyecta user-detail-view -->
    </div>
</div>
```

**Parámetros configurables:**
- `items` - Colección de elementos a mostrar
- `selectedItem` - Elemento actualmente seleccionado
- `entityName` - Nombre singular de la entidad
- `entityNamePlural` - Nombre plural para búsqueda
- `searchPlaceholder` - Texto del placeholder de búsqueda
- `createButtonText` - Texto del botón de crear
- `createButtonColor` - Color del botón de crear
- `emptyMessage` - Mensaje cuando no hay elementos

### 5. Vista de Detalle (`user-detail-view.blade.php`)
**Ubicación:** `/resources/views/components/user-detail-view.blade.php`

**Función:**
- Muestra información completa del usuario seleccionado
- Contiene botones de acción (editar, eliminar)
- Presenta datos organizados en secciones

**Secciones:**
```blade
<div class="flex-1 bg-white p-6 overflow-y-auto">
    <!-- Header con avatar y botones de acción -->
    <div class="border-b border-gray-200 pb-6 mb-6">
        <div class="flex items-center space-x-4">
            <!-- Avatar circular -->
            <!-- Información básica (nombre, email, fecha) -->
            <!-- Botones editar/eliminar -->
        </div>
    </div>
    
    <!-- Información Personal -->
    <div class="mb-8">
        <h2>Información Personal</h2>
        <div class="bg-gray-50 rounded-lg p-4">
            <!-- Grid con datos del usuario -->
        </div>
    </div>
    
    <!-- Roles Asignados -->
    <div class="mb-8">
        <h2>Roles Asignados</h2>
        <!-- Grid de tarjetas de roles -->
    </div>
    
    <!-- Permisos Heredados -->
    <div>
        <h2>Permisos Heredados</h2>
        <!-- Lista de permisos de todos los roles -->
    </div>
</div>
```

## Flujo de Interacción

### 1. Carga Inicial
1. `UsersComponent` se monta y carga usuarios paginados
2. `simple-crud-manager` renderiza la lista en la columna izquierda
3. La columna derecha muestra mensaje "Selecciona usuario"

### 2. Selección de Usuario
1. Usuario hace clic en un item de la lista
2. Se ejecuta `wire:click="selectUser({{ $item->id }})"`
3. `UsersComponent::selectUser()` actualiza `$selectedUser`
4. `user-detail-view` se renderiza en la columna derecha

### 3. Búsqueda
1. Usuario escribe en el campo de búsqueda
2. `wire:model.live.debounce.300ms="searchUsers"` actualiza la propiedad
3. `updatingSearchUsers()` resetea la paginación
4. `render()` filtra usuarios según el término de búsqueda

### 4. Crear/Editar Usuario
1. Usuario hace clic en "Crear Usuario" o "Editar"
2. Se ejecuta `createUser()` o `editUser($userId)`
3. Se abre modal con formulario
4. `saveUser()` valida y guarda (aplicando `trim()` a contraseñas)
5. Modal se cierra y lista se actualiza

### 5. Visibilidad de Contraseñas
1. Usuario hace clic en icono de ojo
2. Se ejecuta `togglePasswordVisibility()` o `togglePasswordConfirmationVisibility()`
3. Propiedad booleana se alterna
4. Tipo de input cambia entre 'password' y 'text'
5. Icono cambia entre `fa-eye` y `fa-eye-slash`

## Características Técnicas

### Responsividad
- Layout flexible que se adapta a diferentes tamaños de pantalla
- Columna izquierda fija (320px), columna derecha flexible
- Grid responsive en formularios y vistas de detalle

### Estado Compartido
- `$selectedUser` se comparte entre lista y detalle
- Componentes reaccionan automáticamente a cambios de estado
- Sincronización en tiempo real via Livewire

### Validación
- Validación en tiempo real en formularios
- Limpieza de espacios en blanco en contraseñas
- Mensajes de error personalizados en español

### Reutilización
- `simple-crud-manager` es genérico y reutilizable
- `user-detail-view` específico pero extensible
- Patrón aplicable a otras entidades (roles, permisos)

## Beneficios de la Arquitectura

1. **Modularidad**: Cada componente tiene una responsabilidad específica
2. **Reutilización**: CRUD manager puede usarse para otras entidades
3. **Mantenibilidad**: Separación clara entre lógica y presentación
4. **Escalabilidad**: Fácil agregar nuevas funcionalidades
5. **UX Moderna**: Interfaz similar a aplicaciones conocidas (Teams)
6. **Performance**: Carga bajo demanda y paginación eficiente