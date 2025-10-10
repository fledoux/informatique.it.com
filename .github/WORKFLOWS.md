# Workflows et Bonnes Pratiques

Guide des workflows de développement et bonnes pratiques pour l'application Laravel helpdesk.

## 🚀 Environnement de Développement

### Setup Initial
```bash
# Installation
composer install
php artisan key:generate
php artisan migrate --seed
php artisan storage:link

# Configuration
cp .env.example .env
# Configurer DB, MAIL, etc.
```

### Développement Quotidien
```bash
# Démarrage complet (serveur, queue, logs, vite)
composer run dev

# Alternative manuelle
php artisan serve &
php artisan queue:work &
php artisan pail &
npm run dev
```

## 🏗️ Workflow Nouvelle Fonctionnalité

### 1. Planification
- [ ] Analyser les besoins métier
- [ ] Définir les permissions nécessaires
- [ ] Prévoir les traductions FR/EN
- [ ] Dessiner le modèle de données

### 2. Base de Données
```bash
# Migration
php artisan make:migration create_entities_table

# Structure recommandée
Schema::create('entities', function (Blueprint $table) {
    $table->id();
    $table->string('name');
    $table->string('status')->default('active'); // Enum string
    $table->decimal('amount', 12, 3)->nullable(); // Billing précis
    
    // Foreign keys avec contraintes
    $table->foreignId('company_id')->constrained()->cascadeOnDelete();
    $table->foreignId('user_id')->constrained()->cascadeOnDelete();
    
    // UUID pour partage public
    $table->uuid('public_id')->unique()->nullable();
    
    // Timestamps obligatoires
    $table->timestamps();
    
    // Index de performance
    $table->index(['company_id', 'status']);
});
```

### 3. Modèle
```php
// app/Models/Entity.php
class Entity extends Model
{
    protected $fillable = [
        'name', 'status', 'amount', 'company_id', 'user_id'
    ];
    
    protected $casts = [
        'amount' => 'decimal:3',
    ];
    
    // Relations
    public function company()
    {
        return $this->belongsTo(Company::class);
    }
    
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    
    // Scopes pour multi-tenant
    public function scopeForCompany($query, Company $company)
    {
        return $query->where('company_id', $company->id);
    }
}
```

### 4. Factory (pour tests)
```php
// database/factories/EntityFactory.php
class EntityFactory extends Factory
{
    public function definition()
    {
        return [
            'name' => $this->faker->company(),
            'status' => $this->faker->randomElement(['active', 'inactive']),
            'amount' => $this->faker->randomFloat(3, 0, 999.999),
            'company_id' => Company::factory(),
            'user_id' => User::factory(),
        ];
    }
}
```

### 5. Form Requests
```php
// app/Http/Requests/EntityStoreRequest.php
class EntityStoreRequest extends FormRequest
{
    public function authorize()
    {
        return $this->user()->can('entity.create');
    }
    
    public function rules()
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'status' => ['required', 'in:active,inactive'],
            'amount' => ['nullable', 'numeric', 'min:0', 'max:999999.999'],
            'company_id' => ['required', 'exists:companies,id'],
        ];
    }
}

// EntityUpdateRequest.php - même structure
```

### 6. Controller
```php
// app/Http/Controllers/EntityController.php
class EntityController extends Controller
{
    public function index()
    {
        $this->authorize('entity.index');
        
        $entities = Entity::with(['company', 'user'])
            ->forCompany(auth()->user()->company)
            ->paginate(15);
            
        return view('entity.index', compact('entities'));
    }
    
    public function store(EntityStoreRequest $request)
    {
        $entity = Entity::create(array_merge(
            $request->validated(),
            ['user_id' => auth()->id()]
        ));
        
        return redirect()->route('entity.index')
            ->with('success', __('global.messages.created'));
    }
    
    public function show($id)
    {
        $this->authorize('entity.show');
        
        try {
            $entity = Entity::with(['company', 'user'])->findOrFail($id);
        } catch (ModelNotFoundException $e) {
            return redirect()->route('entity.index')
                ->with('error', __('global.messages.not_found'));
        }
        
        return view('entity.show', compact('entity'));
    }
    
    public function update(EntityUpdateRequest $request, $id)
    {
        try {
            $entity = Entity::findOrFail($id);
            $entity->update($request->validated());
        } catch (ModelNotFoundException $e) {
            return redirect()->route('entity.index')
                ->with('error', __('global.messages.update_not_found'));
        }
        
        return redirect()->route('entity.show', $entity)
            ->with('success', __('global.messages.updated'));
    }
}
```

### 7. Routes
```php
// routes/web.php
Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('entity', EntityController::class)
        ->middleware('permission:entity.index,entity.create,entity.show,entity.edit,entity.delete');
});
```

### 8. Permissions
```php
// database/seeders/PermissionSeeder.php
$entities = ['entity']; // Ajouter à la liste

foreach ($entities as $entity) {
    Permission::create(['name' => "{$entity}.index"]);
    Permission::create(['name' => "{$entity}.create"]);
    Permission::create(['name' => "{$entity}.show"]);
    Permission::create(['name' => "{$entity}.edit"]);
    Permission::create(['name' => "{$entity}.delete"]);
}
```

### 9. Vues
```blade
{{-- resources/views/entity/_form.blade.php --}}
<form method="POST" action="{{ $action }}">
    @csrf
    @if($method ?? null) @method($method) @endif
    
    <x-forms.input 
        name="name" 
        label="{{ __('entity.fields.name') }}" 
        :value="old('name', $entity->name ?? '')"
        required 
    />
    
    <x-forms.select 
        name="status" 
        label="{{ __('entity.fields.status') }}"
        :options="[
            'active' => __('entity.enums.status.active'),
            'inactive' => __('entity.enums.status.inactive')
        ]"
        :value="old('status', $entity->status ?? 'active')"
        required
    />
    
    <x-forms.input 
        name="amount" 
        label="{{ __('entity.fields.amount') }}" 
        type="number"
        step="0.001"
        :value="old('amount', $entity->amount ?? '')"
    />
    
    <div class="d-flex gap-1 gap-sm-2 mt-4">
        <button type="submit" class="btn btn-primary">
            {!! __('global.btn.Save') !!}
        </button>
        <a href="{{ route('entity.index') }}" class="btn btn-secondary">
            {!! __('global.btn.Back') !!}
        </a>
    </div>
</form>
```

### 10. Traductions
```bash
# Lancer après création des vues
php dev/translations.php fix

# Vérifier
php dev/translations.php check
```

### 11. Tests
```php
// tests/Feature/EntityTest.php
class EntityTest extends TestCase
{
    use RefreshDatabase;
    
    public function test_user_can_create_entity()
    {
        $user = User::factory()->create();
        $company = $user->company;
        
        $this->actingAs($user)
            ->post(route('entity.store'), [
                'name' => 'Test Entity',
                'status' => 'active',
                'company_id' => $company->id,
            ])
            ->assertRedirect(route('entity.index'));
            
        $this->assertDatabaseHas('entities', [
            'name' => 'Test Entity',
            'company_id' => $company->id,
        ]);
    }
}
```

## 🔐 Sécurité et Permissions

### Multi-Tenant Pattern
```php
// Dans tous les controllers
public function index()
{
    $entities = Entity::forCompany(auth()->user()->company)->get();
}

// Scope dans le modèle
public function scopeForCompany($query, Company $company)
{
    return $query->where('company_id', $company->id);
}
```

### Validation d'Autorisation
```php
// Toujours try-catch sur findOrFail
try {
    $entity = Entity::findOrFail($id);
    $this->authorize('update', $entity); // Policy si nécessaire
} catch (ModelNotFoundException $e) {
    return redirect()->back()->with('error', __('global.messages.not_found'));
}
```

## 🎨 UI/UX Standards

### Layout Standard
```blade
@extends('layouts.app')
@section('title', __('entity.List'))

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">{{ __('entity.List') }}</h5>
                    @can('entity.create')
                        <a href="{{ route('entity.create') }}" class="btn btn-primary">
                            {!! __('global.btn.New') !!}
                        </a>
                    @endcan
                </div>
                <div class="card-body p-2 p-sm-3">
                    {{-- Contenu --}}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
```

### Messages Flash
```blade
{{-- Inclus automatiquement dans layouts/app.blade.php --}}
@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif
```

## 📊 Performance et Optimisation

### Eager Loading
```php
// Controller
$entities = Entity::with(['company:id,name', 'user:id,name'])
    ->forCompany(auth()->user()->company)
    ->paginate(15);
```

### Cache pour Options
```php
// Cache les options lourdes
$companies = Cache::remember('companies_options', 3600, function () {
    return Company::pluck('name', 'id');
});
```

### Index de Base de Données
```php
// Dans les migrations
$table->index(['company_id', 'status']);
$table->index(['created_at']);
$table->index(['user_id', 'created_at']);
```

## 🧪 Testing Strategy

### Tests Feature (Prioritaires)
- Création d'entité
- Lecture/affichage
- Mise à jour
- Suppression
- Permissions et accès

### Tests Unit (Complémentaires)
- Scopes de modèle
- Relations
- Validation des données

### Commandes de Test
```bash
# Tests complets
composer run test

# Tests spécifiques
php artisan test --filter=EntityTest

# Coverage
php artisan test --coverage
```

## 🚀 Déploiement

### Pre-Déploiement Checklist
- [ ] Tests passent : `composer run test`
- [ ] Traductions complètes : `php dev/translations.php check`
- [ ] Pas d'erreurs de lint : `composer run lint` (si configuré)
- [ ] Migrations testées
- [ ] Seeds de permissions à jour

### Déploiement Production
```bash
# Exclure le dossier dev
rsync --exclude=dev/ source/ production/

# Ou dans .gitignore pour automatique
echo "dev/" >> .gitignore
```

---

*Guide créé le 22 septembre 2025*  
*Workflows validés sur le terrain*