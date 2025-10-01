<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Schema;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class MakeCrudBootstrap extends Command
{
    protected $signature = 'make:crud-bootstrap {model : Eloquent model name, e.g. Company} {--table=} {--force : Force overwrite existing files}';
    protected $description = 'Génère des vues Blade Bootstrap 5 (index/create/edit/show + _form) pour un modèle';
    
    protected string $table;
    protected array $booleanFields;
    protected array $jsonFields;
    protected array $jsonBooleanFields;
    protected array $foreignKeys;
    protected array $booleanEnumFields; // Nouveau tableau pour les booléens avec enum
    protected array $enumFields; // Nouveau tableau pour les champs enum
    protected array $columnTypes; // NOUVEAU : Types de colonnes depuis les migrations
    protected array $allEntitiesProcessed = []; // NOUVEAU : Liste des entités traitées

    public function handle(): int
    {
        $model      = ltrim($this->argument('model'), '\\/');
        $modelClass = "\\App\\Models\\{$model}";
        
        // Message de confirmation TOUJOURS obligatoire (même avec --force)
        if (!$this->confirm("Voulez-vous continuer pour créer \"{$model}\" ?", false)) {
            $this->info('Opération annulée.');
            return self::FAILURE;
        }
        
        // Déduire le nom de table d'abord (avant de créer le modèle)
        $this->table = $this->option('table') ?: Str::snake(Str::pluralStudly($model));
        
        if (! class_exists($modelClass)) {
            // Génère le modèle manquant et continue (boilerplate inclus si supporté)
            $this->call('make:model', [
                'name'  => $model,
                '--all' => true,
            ]);
        }
        
        // NOUVEAU : Analyser tous les types de colonnes depuis les migrations
        $this->columnTypes = $this->getColumnTypesFromMigration($model);
        
        // AJOUTER : Récupérer les champs enum AVANT les autres
        $this->enumFields = $this->getEnumFieldsFromMigration($model);
    
        // Récupérer les champs booléens une seule fois
        $this->booleanFields = $this->getBooleanFieldsFromMigration($model);
        // $this->booleanEnumFields est maintenant rempli par la méthode ci-dessus
        $jsonData = $this->getJsonFieldsFromMigration($model);
        $this->jsonFields = $jsonData['json'];
        $this->jsonBooleanFields = $jsonData['json_boolean'];

        // Récupérer les relations (clés étrangères)
        $this->foreignKeys = $this->getForeignKeysFromMigrations($this->table);

        // DEBUG : Afficher les détections
        $this->info("Types de colonnes détectés pour {$this->table}: " . json_encode($this->columnTypes, JSON_PRETTY_PRINT));
        $this->info("Enum détectés pour {$this->table}: " . json_encode($this->enumFields, JSON_PRETTY_PRINT));
        $this->info("FK détectées pour {$this->table}: " . json_encode($this->foreignKeys, JSON_PRETTY_PRINT));

        if (! Schema::hasTable($this->table)) {
            $this->error("La table '{$this->table}' n’existe pas (migre d’abord).");
            return self::FAILURE;
        }

        // Récupérer colonnes (filtrer SEULEMENT les champs techniques, pas password)
        $columns = array_values(array_filter(
            Schema::getColumnListing($this->table),
            fn($c) => !in_array($c, ['id', 'remember_token', 'created_at', 'updated_at', 'email_verified_at'])
        ));

        $entity     = class_basename($modelClass); // Company
        $entitySlug = Str::kebab($entity); // company (singular slug as requested)
        $varSing    = Str::camel($entity);         // company
        $varPlur    = Str::camel(Str::pluralStudly($entity)); // companies
        $viewDir    = resource_path("views/{$entitySlug}");

        $fs = new Filesystem();
        $fs->ensureDirectoryExists($viewDir);

        // Cleanup only this entity's views when --force (scoped, safe)
        if ($this->option('force')) {
            $viewsBase = resource_path('views');
            $currentDir = resource_path("views/{$entitySlug}");
            $legacyPluralDir = resource_path('views/' . Str::plural($entitySlug));

            $safeDelete = function (Filesystem $fs, string $dir, string $base) {
                if (!$fs->exists($dir) || !is_dir($dir)) {
                    return;
                }
                // ensure the directory is inside the views base (avoid accidental deletions)
                $realDir  = realpath($dir);
                $realBase = realpath($base);
                if ($realDir && $realBase && str_starts_with($realDir, $realBase)) {
                    $fs->deleteDirectory($dir);
                }
            };

            // delete only the legacy plural dir of THIS entity (e.g. views/companies/)
            $safeDelete($fs, $legacyPluralDir, $viewsBase);

            // recreate and clean only THIS entity dir (e.g. views/company/)
            if ($fs->exists($currentDir)) {
                $fs->cleanDirectory($currentDir);
            } else {
                $fs->ensureDirectoryExists($currentDir);
            }
        }

        $files = [
            "{$viewDir}/index.blade.php"  => $this->stubIndex($entity, $entitySlug, $varSing, $varPlur, $columns),
            "{$viewDir}/_form.blade.php"  => $this->stubForm($entity, $entitySlug, $varSing, $columns, $this->table),
            "{$viewDir}/create.blade.php" => $this->stubCreate($entity, $entitySlug),
            "{$viewDir}/edit.blade.php"   => $this->stubEdit($entity, $entitySlug, $varSing),
            "{$viewDir}/show.blade.php"   => $this->stubShow($entity, $entitySlug, $varSing, $columns),
            "{$viewDir}/_delete_form.blade.php" => $this->stubDeleteForm($entity, $entitySlug, $varSing),
        ];

        foreach ($files as $path => $content) {
            $fs->put($path, $content);
            $this->info("Écrit : " . str_replace(base_path() . '/', '', $path));
        }

        $this->line("Routes attendues (resource) : Route::resource('{$entitySlug}', {$entity}Controller::class);");

        // Générer le Model (et tout le boilerplate) si inexistant
        if (! class_exists($modelClass)) {
            // --all => migration, seeder, factory, policy, resource controller & form requests (selon version de Laravel)
            $this->call('make:model', [
                'name' => $entity,
                '--all' => true,
            ]);
        }

        // Générer un contrôleur resource si inexistant
        $controllerPath = app_path("Http/Controllers/{$entity}Controller.php");
        if (!$fs->exists($controllerPath)) {
            $this->call('make:controller', [
                'name' => "{$entity}Controller",
                '--resource' => true,
                '--model' => $modelClass,
            ]);
        }

        // Générer une Policy si inexistante
        $policyPath = app_path("Policies/{$entity}Policy.php");
        if (!$fs->exists($policyPath)) {
            $this->call('make:policy', [
                'name' => "{$entity}Policy",
                '--model' => $modelClass,
            ]);
        }

        // Générer des Form Requests (Store / Update) si inexistantes
        $storeReq = app_path("Http/Requests/{$entity}StoreRequest.php");
        $updateReq = app_path("Http/Requests/{$entity}UpdateRequest.php");
        if (!$fs->exists($storeReq)) {
            $this->call('make:request', ['name' => "{$entity}StoreRequest"]);
        }
        if (!$fs->exists($updateReq)) {
            $this->call('make:request', ['name' => "{$entity}UpdateRequest"]);
        }

        // (Re)écrire le contenu des Form Requests avec authorize()=true + règles basées sur le schéma
        // Récupération des métadonnées colonnes via INFORMATION_SCHEMA (MySQL/MariaDB)
        $meta = DB::select(
            "SELECT COLUMN_NAME as name, DATA_TYPE as type, IS_NULLABLE as nullable, CHARACTER_MAXIMUM_LENGTH as len
             FROM INFORMATION_SCHEMA.COLUMNS
             WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ?",
            [$this->table]
        );
        $columnsMeta = collect($meta)->keyBy('name');

        $makeRule = function (string $col) use ($columnsMeta) {
            $info = $columnsMeta[$col] ?? null;
            $nullable = $info && strtoupper($info->nullable) === 'YES';
            $type = $info->type ?? 'varchar';
            $len  = $info->len ?? null;
            $lower = strtolower($col);

            // champs ignorés (retirer password de cette liste)
            if (in_array($col, ['id', 'created_at', 'updated_at', 'email_verified_at', 'remember_token'])) {
                return null;
            }

            $rules = [];

            // Cas spécial pour password
            if ($lower === 'password') {
                $rules = ['nullable', 'string', 'min:8'];
            } else {
                $rules[] = $nullable ? 'nullable' : 'required';

                // NOUVEAU : Vérifier si c'est un champ enum Laravel (PRIORITÉ)
                if (isset($this->enumFields[$col])) {
                    $enumValues = $this->enumFields[$col];
                    $rules[] = 'in:' . implode(',', $enumValues);
                }
                // Vérifier si c'est un champ JSON avec structure booléenne
                elseif (isset($this->jsonBooleanFields[$col])) {
                    $rules[] = 'array';
                    $rules[] = 'nullable';
                }
                // Vérifier si c'est un champ JSON général
                elseif (in_array($col, $this->jsonFields)) {
                    $rules[] = 'nullable';
                    $rules[] = 'json';
                }
                // Vérifier si c'est un champ booléen depuis la migration
                elseif (in_array($col, $this->booleanFields)) {
                    $rules[] = 'boolean';
                }
                // Mappings types / heuristiques
                elseif (in_array($type, ['text', 'mediumtext', 'longtext'])) {
                    $rules[] = 'string';
                } elseif (in_array($type, ['json'])) {
                    $rules[] = 'array';
                } elseif (in_array($type, ['tinyint', 'bool', 'boolean'])) {
                    $rules[] = 'boolean';
                } elseif (in_array($type, ['int', 'bigint', 'smallint', 'mediumint'])) {
                    $rules[] = 'integer';
                } elseif (in_array($type, ['decimal', 'numeric', 'float', 'double'])) {
                    $rules[] = 'numeric';
                } elseif (in_array($type, ['datetime', 'timestamp', 'date'])) {
                    $rules[] = 'date';
                } else {
                    $rules[] = 'string';
                    if ($len) {
                        $rules[] = "max:{$len}";
                    }
                }
            }

            // SUPPRIMER cette section car elle interfère avec les enum :
            // // règles spécifiques par nom (seulement si pas booléen)
            // if (!in_array('boolean', $rules)) {
            //     if ($lower === 'email') {
            //         $rules = [$nullable ? 'nullable' : 'required', 'email', $len ? "max:{$len}" : ''];
            //     }
            //     if ($lower === 'website' || str_ends_with($lower, 'url')) {
            //         $rules = [$nullable ? 'nullable' : 'required', 'url', $len ? "max:{$len}" : ''];
            //     }
            //     if ($lower === 'country') {
            //         $rules = [$nullable ? 'nullable' : 'required', 'string', 'size:2'];
            //     }
            //     if (in_array($lower, ['status', 'company_status'])) {  // ← CETTE LIGNE CAUSE LE PROBLÈME
            //         $rules = [$nullable ? 'nullable' : 'required', 'in:active,inactive'];
            //     }
            // }

            // REMPLACER par des règles spécifiques qui ne touchent PAS aux enum/boolean :
            if (!in_array('boolean', $rules) && !isset($this->enumFields[$col])) {
                if ($lower === 'email') {
                    $rules = [$nullable ? 'nullable' : 'required', 'email', $len ? "max:{$len}" : ''];
                }
                if ($lower === 'website' || str_ends_with($lower, 'url')) {
                    $rules = [$nullable ? 'nullable' : 'required', 'url', $len ? "max:{$len}" : ''];
                }
                if ($lower === 'country') {
                    $rules = [$nullable ? 'nullable' : 'required', 'string', 'size:2'];
                }
            }

            // Nettoyage des règles vides
            $rules = array_values(array_filter($rules, fn($r) => $r !== ''));

            return "'{$col}' => ['" . implode("','", $rules) . "']";
        };

        $rulesLines = array_values(array_filter(array_map($makeRule, $columns)));
        $rulesExport = "[\n            " . implode(",\n            ", $rulesLines) . "\n        ]";

        $requestTemplate = function (string $nsClass, string $rulesExport) {
            return <<<PHP
            <?php

            namespace App\\Http\\Requests;

            use Illuminate\\Foundation\\Http\\FormRequest;

            class {$nsClass} extends FormRequest
            {
                public function authorize(): bool
                {
                    return true;
                }

                public function rules(): array
                {
                    return {$rulesExport};
                }
            }
            PHP;
        };

        $fs->put($storeReq, $requestTemplate("{$entity}StoreRequest", $rulesExport));
        $fs->put($updateReq, $requestTemplate("{$entity}UpdateRequest", $rulesExport));

        // Ecrire le contenu complet du controller (CRUD) si vide (ou --force)
        if ($fs->exists($controllerPath)) {
            $controllerContent = $fs->get($controllerPath);
            $isEmptyController = trim(preg_replace('/<\?php|namespace\s+.*;|use\s+.*;|class\s+.*\{|\}/sU', '', $controllerContent)) === '';
            if ($isEmptyController || $this->option('force')) {
                
                // Traitement spécial pour le modèle User avec password
                $storeMethod = $entity === 'User' ? 
                    <<<PHP
                    public function store({$entity}StoreRequest \$request)
                    {
                        \$data = \$request->validated();
                        if (!empty(\$data['password'])) {
                            \$data['password'] = bcrypt(\$data['password']);
                        } else {
                            unset(\$data['password']);
                        }
                        \${$varSing} = {$entity}::create(\$data);
                        return redirect()->route('{$entitySlug}.index')->with('success', '{$entity} created successfully.');
                    }
                    PHP :
                    <<<PHP
                    public function store({$entity}StoreRequest \$request)
                    {
                        \${$varSing} = {$entity}::create(\$request->validated());
                        return redirect()->route('{$entitySlug}.index')->with('success', '{$entity} created successfully.');
                    }
                    PHP;

                $updateMethod = $entity === 'User' ?
                    <<<PHP
                    public function update({$entity}UpdateRequest \$request, {$entity} \${$varSing})
                    {
                        \$data = \$request->validated();
                        if (!empty(\$data['password'])) {
                            \$data['password'] = bcrypt(\$data['password']);
                        } else {
                            unset(\$data['password']);
                        }
                        \${$varSing}->update(\$data);
                        return redirect()->route('{$entitySlug}.index')->with('success', '{$entity} updated successfully.');
                    }
                    PHP :
                    <<<PHP
                    public function update({$entity}UpdateRequest \$request, {$entity} \${$varSing})
                    {
                        \${$varSing}->update(\$request->validated());
                        return redirect()->route('{$entitySlug}.index')->with('success', '{$entity} updated successfully.');
                    }
                    PHP;

                // Générer eager loading pour éviter le problème N+1
                $eagerRelations = $this->getEagerLoadingRelations($columns);
                $withClause = empty($eagerRelations) ? '' : "->with(['" . implode("', '", $eagerRelations) . "'])";

                $controllerBody = <<<PHP
<?php

namespace App\\Http\\Controllers;

use {$modelClass};
use App\\Http\\Requests\\{$entity}StoreRequest;
use App\\Http\\Requests\\{$entity}UpdateRequest;
use Illuminate\\Database\\Eloquent\\ModelNotFoundException;
use Illuminate\\Support\\Facades\\Auth;

/**
 * @method \\Illuminate\\Routing\\ControllerMiddlewareOptions middleware(string \$middleware)
 */
class {$entity}Controller extends Controller
{
    /**
     * Apply permission middleware to controller methods.
     */
    public function __construct()
    {
        \$this->middleware('permission:{$entitySlug}.index')->only('index');
        \$this->middleware('permission:{$entitySlug}.create')->only(['create', 'store']);
        \$this->middleware('permission:{$entitySlug}.show')->only('show');
        \$this->middleware('permission:{$entitySlug}.edit')->only(['edit', 'update']);
        \$this->middleware('permission:{$entitySlug}.delete')->only('destroy');
    }

    public function index()
    {
        \${$varPlur} = {$entity}::query(){$withClause}->latest('id')->paginate(15);
        return view('{$entitySlug}.index', compact('{$varPlur}'));
    }

    public function create()
    {
        return view('{$entitySlug}.create');
    }

    public function store({$entity}StoreRequest \$request)
    {
        \$data = \$request->validated();
        if (!empty(\$data['password'])) {
            \$data['password'] = bcrypt(\$data['password']);
        } else {
            unset(\$data['password']);
        }
        \${$varSing} = {$entity}::create(\$data);
        return redirect()->route('{$entitySlug}.index')->with('success', __('global.messages.created'));
    }

    public function show(\$id)
    {
        try {
            \${$varSing} = {$entity}::query(){$withClause}->findOrFail(\$id);
            return view('{$entitySlug}.show', compact('{$varSing}'));
        } catch (ModelNotFoundException \$e) {
            return redirect()->route('{$entitySlug}.index')
                ->with('error', __('global.messages.not_found'));
        }
    }

    public function edit(\$id)
    {
        try {
            \${$varSing} = {$entity}::query(){$withClause}->findOrFail(\$id);
            return view('{$entitySlug}.edit', compact('{$varSing}'));
        } catch (ModelNotFoundException \$e) {
            return redirect()->route('{$entitySlug}.index')
                ->with('error', __('global.messages.edit_not_found'));
        }
    }

    public function update({$entity}UpdateRequest \$request, \$id)
    {
        try {
            \${$varSing} = {$entity}::findOrFail(\$id);
            \$data = \$request->validated();
            if (!empty(\$data['password'])) {
                \$data['password'] = bcrypt(\$data['password']);
            } else {
                unset(\$data['password']);
            }
            \${$varSing}->update(\$data);
            return redirect()->route('{$entitySlug}.index')->with('success', __('global.messages.updated'));
        } catch (ModelNotFoundException \$e) {
            return redirect()->route('{$entitySlug}.index')
                ->with('error', __('global.messages.update_not_found'));
        }
    }

    public function destroy(\$id)
    {
        try {
            \${$varSing} = {$entity}::findOrFail(\$id);
            
            // Empêcher l'auto-suppression
            if (strtolower('{$entity}') === 'user' && Auth::check() && \${$varSing}->id === Auth::id()) {
                return redirect()->route('{$entitySlug}.index')
                    ->with('error', __('global.messages.cannot_delete_self'));
            }
            
            \${$varSing}->delete();
            return redirect()->route('{$entitySlug}.index')->with('success', __('global.messages.deleted'));
        } catch (ModelNotFoundException \$e) {
            return redirect()->route('{$entitySlug}.index')
                ->with('error', __('global.messages.delete_not_found'));
        }
    }
}
PHP;
                $fs->put($controllerPath, $controllerBody);
                $this->info("{$entity}Controller complété (CRUD).");
            }
        }

        // Ajouter la Route::resource si absente
        $routesFile = base_path('routes/web.php');
        $resourceLines = <<<PHP
// Routes {$entity} - Permissions gérées dans {$entity}Controller::__construct()
Route::prefix('{$entitySlug}')->group(function () {
    Route::get('/', [{$entity}Controller::class, 'index'])->name('{$entitySlug}.index');
    Route::get('/create', [{$entity}Controller::class, 'create'])->name('{$entitySlug}.create');
    Route::post('/', [{$entity}Controller::class, 'store'])->name('{$entitySlug}.store');
    Route::get('/{{$entitySlug}}', [{$entity}Controller::class, 'show'])->name('{$entitySlug}.show');
    Route::get('/{{$entitySlug}}/edit', [{$entity}Controller::class, 'edit'])->name('{$entitySlug}.edit');
    Route::put('/{{$entitySlug}}', [{$entity}Controller::class, 'update'])->name('{$entitySlug}.update');
    Route::delete('/{{$entitySlug}}', [{$entity}Controller::class, 'destroy'])->name('{$entitySlug}.destroy');
});
PHP;

        if ($fs->exists($routesFile)) {
            $routes = $fs->get($routesFile);
            
            // Vérifier si le groupe de routes existe déjà
            $routeGroupPattern = "Route::prefix('{$entitySlug}')->group(function ()";
            if (strpos($routes, $routeGroupPattern) !== false) {
                $this->info("Routes pour {$entitySlug} existent déjà, génération ignorée");
            } else {
                // Vérifier si le use statement existe déjà
                $useStatement = "use \\App\\Http\\Controllers\\{$entity}Controller;";
                if (strpos($routes, $useStatement) === false) {
                    // Ajouter le use statement après les autres use statements
                    $lines = explode("\n", $routes);
                    $insertIndex = 0;
                    
                    // Trouver la dernière ligne use ou après <?php
                    for ($i = 0; $i < count($lines); $i++) {
                        if (str_starts_with(trim($lines[$i]), 'use ') && str_contains($lines[$i], 'Controller')) {
                            $insertIndex = $i + 1;
                        }
                    }
                    
                    // Si pas de use Controller trouvé, chercher après les autres use
                    if ($insertIndex === 0) {
                        for ($i = 0; $i < count($lines); $i++) {
                            if (str_starts_with(trim($lines[$i]), 'use ')) {
                                $insertIndex = $i + 1;
                            }
                        }
                    }
                    
                    // Insérer le use statement
                    array_splice($lines, $insertIndex, 0, $useStatement);
                    $routes = implode("\n", $lines);
                    $fs->put($routesFile, $routes);
                    $this->info("Use statement ajouté pour {$entity}Controller");
                }
                
                // Ajouter les routes
                $fs->append($routesFile, PHP_EOL . $resourceLines . PHP_EOL);
                $this->info("Routes ajoutées dans routes/web.php (permissions gérées dans le contrôleur)");
            }
        }

        // Mettre à jour le Model: $fillable + $casts
        $modelPath = app_path("Models/{$entity}.php");
        if ($fs->exists($modelPath)) {
            $modelSrc = $fs->get($modelPath);

            // construire fillable à partir des colonnes (exclure id, timestamps)
            $fillableCols = array_values(array_filter($columns, fn($c) => !in_array($c, ['id'])));
            $fillableExport = "['" . implode("','", $fillableCols) . "']";

            // construire casts de base
            $casts = [
                'email_verified_at' => 'datetime',
                'password' => 'hashed',
                'created_at' => 'datetime',
                'updated_at' => 'datetime',
            ];
            
            // Ajouter les casts pour les champs spéciaux
            foreach ($columns as $c) {
                if (str_ends_with($c, '_at') && !isset($casts[$c])) {
                    $casts[$c] = 'datetime';
                }
                if (in_array($c, $this->jsonFields)) {
                    $casts[$c] = 'array';
                }
                if (in_array($c, $this->booleanFields)) {
                    $casts[$c] = 'boolean';
                }
                if (isset($this->enumFields[$c])) {
                    $casts[$c] = 'string';
                }
            }
            
            $castsExportPairs = [];
            foreach ($casts as $k => $v) {
                $castsExportPairs[] = "'{$k}' => '{$v}'";
            }
            $castsExport = '[' . implode(',', $castsExportPairs) . ']';

            // NOUVEAU : Configuration spéciale pour le modèle User
            if ($entity === 'User') {
                $this->info("🔧 Configuration spéciale du modèle User avec Spatie Laravel Permission");
                
                // 1. AJOUTER l'import Spatie HasRoles
                if (!str_contains($modelSrc, 'use Spatie\\Permission\\Traits\\HasRoles;')) {
                    $modelSrc = str_replace(
                        'use Illuminate\\Notifications\\Notifiable;',
                        "use Illuminate\\Notifications\\Notifiable;\nuse Spatie\\Permission\\Traits\\HasRoles;",
                        $modelSrc
                    );
                    $this->info("✅ Import HasRoles ajouté");
                }
                
                // 2. AJOUTER le trait HasRoles dans la déclaration use
                if (!str_contains($modelSrc, 'HasRoles')) {
                    // Plusieurs patterns possibles
                    if (str_contains($modelSrc, 'use HasFactory, Notifiable;')) {
                        $modelSrc = str_replace(
                            'use HasFactory, Notifiable;',
                            'use HasFactory, Notifiable, HasRoles;',
                            $modelSrc
                        );
                        $this->info("✅ Trait HasRoles ajouté à la déclaration use");
                    }
                }
                
                // 3. SUPPRIMER la méthode casts() en double si elle existe
                if (str_contains($modelSrc, 'protected function casts()')) {
                    $modelSrc = preg_replace('/protected function casts\(\): array\s*\{[^}]*\}/s', '', $modelSrc);
                    $this->info("✅ Méthode casts() dupliquée supprimée");
                }
                
                // 4. AJOUTER la relation company() si elle n'existe pas
                if (!str_contains($modelSrc, 'function company()')) {
                    $companyRelation = "\n\n    public function company()\n    {\n        return \$this->belongsTo(\\App\\Models\\Company::class);\n    }";
                    $modelSrc = preg_replace('/\}$/', $companyRelation . "\n}", $modelSrc);
                    $this->info("✅ Relation company() ajoutée");
                }
            }

            // injecter ou remplacer les propriétés
            if (strpos($modelSrc, 'protected $fillable') === false) {
                $modelSrc = preg_replace(
                    '/class\s+' . $entity . '\s+extends\s+[^\{]+\{/',
                    "class {$entity} extends " . ($entity === 'User' ? 'Authenticatable' : 'Model') . "\n{\n    protected \$fillable = {$fillableExport};\n\n    protected \$casts = {$castsExport};\n",
                    $modelSrc
                );
            } else {
                $patternFillable = '/protected\s+\$fillable\s*=\s*[^;]+;/';
                $patternCasts    = '/protected\s+\$casts\s*=\s*[^;]+;/';

                $replacementFillable = 'protected $fillable = ' . $fillableExport . ';';
                $replacementCasts    = 'protected $casts = ' . $castsExport . ';';

                if (preg_match($patternFillable, $modelSrc)) {
                    $modelSrc = preg_replace($patternFillable, $replacementFillable, $modelSrc);
                } else {
                    // inject after class opening if no $fillable property exists
                    $modelSrc = preg_replace(
                        '/class\s+' . preg_quote($entity, '/') . '\s+extends\s+[^\{]+\{/',
                        'class ' . $entity . " extends " . ($entity === 'User' ? 'Authenticatable' : 'Model') . "\n{\n    " . $replacementFillable,
                        $modelSrc,
                        1
                    );
                }

                if (strpos($modelSrc, 'protected $casts') === false) {
                    // append casts right after $fillable
                    $modelSrc = preg_replace(
                        $patternFillable,
                        $replacementFillable . "\n\n    " . $replacementCasts,
                        $modelSrc,
                        1
                    );
                } else {
                    $modelSrc = preg_replace($patternCasts, $replacementCasts, $modelSrc, 1);
                }
            }

            // NOUVEAU : Ajouter la relation company() pour le modèle User
            if ($entity === 'User' && !str_contains($modelSrc, 'function company()')) {
                $companyRelation = "\n\n    public function company()\n    {\n        return \$this->belongsTo(\\App\\Models\\Company::class);\n    }";
                
                // Ajouter avant la dernière accolade
                $modelSrc = preg_replace('/\}$/', $companyRelation . "\n}", $modelSrc);
            }

            $fs->put($modelPath, $modelSrc);
            $this->info("Model {$entity} enrichi (\$fillable + \$casts" . ($entity === 'User' ? ' + HasRoles trait' : '') . ").");
        }

        // --- Génération des fichiers de traduction (en & fr) pour le CRUD ---

        // CORRIGÉ : Ajouter les traductions enum SEULEMENT pour les champs de CETTE table
        $enumTranslations = [];
        foreach ($columns as $column) {
            if (isset($this->enumFields[$column])) {
                $enumValues = $this->enumFields[$column];
                foreach ($enumValues as $enumValue) {
                    $enumTranslations[$column][$enumValue] = ucfirst($enumValue);
                }
            }
        }

        $entityLabel = $entity;
        
        // Traductions anglaises
        $commonEN = [
            'entity'   => $entityLabel,
            'id'       => 'ID',
            'List'     => 'List',
            'Edit'     => 'Edit',
            'Details'  => 'Details',
            'Actions'  => 'Actions',
            'New'      => 'New',
            'Save'     => 'Save',
            'Back'     => 'Back',
            'Delete'   => 'Delete',
            'Delete?'  => 'Delete?',
            'No data'  => 'No data',
        ];
        
        // Traductions françaises  
        $commonFR = [
            'entity'   => $entityLabel,
            'id'       => 'ID',
            'List'     => 'Liste',
            'Edit'     => 'Modifier',
            'Details'  => 'Détails',
            'Actions'  => 'Actions',
            'New'      => 'Nouveau',
            'Save'     => 'Enregistrer',
            'Back'     => 'Retour',
            'Delete'   => 'Supprimer',
            'Delete?'  => 'Supprimer ?',
            'No data'  => 'Aucune donnée',
        ];

        // MODIFIÉ : Fonction d'export avec enum à la racine
        $exportArray = function (array $arr, array $fields, array $enums) {
            $fieldsLines = [];
            foreach ($fields as $k => $v) {
                $fieldsLines[] = "            '{$k}' => '" . addslashes($v) . "'";
            }
            $fieldsStr = implode(",\n", $fieldsLines);

            $lines = [];
            foreach ($arr as $k => $v) {
                $lines[] = "        '{$k}' => '" . addslashes($v) . "'";
            }
            $commonStr = implode(",\n", $lines);

            // CORRIGÉ : Enum directement à la racine
            $enumStr = '';
            if (!empty($enums)) {
                $enumSections = [];
                foreach ($enums as $enumField => $enumValues) {
                    $enumValueLines = [];
                    foreach ($enumValues as $key => $label) {
                        $enumValueLines[] = "            '{$key}' => '" . addslashes($label) . "'";
                    }
                    $enumSections[] = "    '{$enumField}' => [\n" . implode(",\n", $enumValueLines) . "\n    ]";
                }
                $enumStr = ",\n\n" . implode(",\n", $enumSections);
            }

            return "<?php\n\nreturn [\n{$commonStr},\n\n    'fields' => [\n{$fieldsStr}\n    ]{$enumStr}\n];\n";
        };

        // Ensure lang directories exist
        $fs->ensureDirectoryExists(resource_path('lang/en'));
        $fs->ensureDirectoryExists(resource_path('lang/fr'));

        $enFile = resource_path('lang/en/' . $entitySlug . '.php');
        $frFile = resource_path('lang/fr/' . $entitySlug . '.php');

        // EN : Valeurs par défaut (SEULEMENT pour cette entité)
        $enEnumTranslations = $enumTranslations;

        // FR : Traductions françaises pour les enum courants (SEULEMENT pour cette entité)
        $frEnumTranslations = [];
        foreach ($enumTranslations as $enumField => $enumValues) {
            foreach ($enumValues as $key => $defaultValue) {
                $frValue = match($defaultValue) {
                    'New' => 'Nouveau',
                    'In_progress' => 'En cours', 
                    'In Progress' => 'En cours',
                    'Waiting' => 'En attente',
                    'Resolved' => 'Résolu',
                    'Closed' => 'Fermé',
                    'Canceled' => 'Annulé',
                    'Active' => 'Actif',
                    'Inactive' => 'Inactif', 
                    'Pending' => 'En attente',
                    'Draft' => 'Brouillon',
                    'Published' => 'Publié',
                    'Archived' => 'Archivé',
                    'High' => 'Élevée',
                    'Medium' => 'Moyen',
                    'Low' => 'Faible',
                    'Urgent' => 'Urgent',
                    'Normal' => 'Normal',
                    'Public' => 'Public',
                    'Private' => 'Privé',
                    'Oui' => 'Oui',
                    'Non' => 'Non',
                    default => $defaultValue
                };
                $frEnumTranslations[$enumField][$key] = $frValue;
            }
        }

        // Générer les labels traduits pour les champs
        $labelsMapEN = [];
        $labelsMapFR = [];
        foreach ($columns as $c) {
            $labelsMapEN[$c] = $this->labelFrom($c); // Version anglaise (ex: Company Id)
            $labelsMapFR[$c] = $this->labelFromFR($c); // Version française (ex: Entreprise)
            
            // Ajouter les traductions pour les champs JSON booléens
            if (isset($this->jsonBooleanFields[$c])) {
                foreach ($this->jsonBooleanFields[$c] as $key) {
                    $labelsMapEN["{$c}_{$key}"] = $this->labelFrom("{$c}_{$key}");
                    $labelsMapFR["{$c}_{$key}"] = $this->labelFromFR("{$c}_{$key}");
                }
            }
        }

        $fs->put($enFile, $exportArray($commonEN, $labelsMapEN, $enEnumTranslations));
        $fs->put($frFile, $exportArray($commonFR, $labelsMapFR, $frEnumTranslations));

        // Ensure minimal crud.php exists in resources/lang/en and fr
        $crudEn = resource_path('lang/en/crud.php');
        $crudFr = resource_path('lang/fr/crud.php');
        if (!$fs->exists($crudEn)) {
            $fs->put(
                $crudEn,
                <<<PHP
<?php
return [
    'List' => 'List',
    'Create' => 'Create',
    'Edit' => 'Edit',
    'Details' => 'Details',
    'Actions' => 'Actions',
    'New' => 'New',
    'Save' => 'Save',
    'Back' => 'Back',
    'Delete' => 'Delete',
    'Delete?' => 'Delete?',
    'No data' => 'No data',
    
    'messages' => [
        'created' => 'Record created successfully.',
        'updated' => 'Record updated successfully.',
        'deleted' => 'Record deleted successfully.',
        'not_found' => 'This record does not exist or has been deleted.',
        'edit_not_found' => 'Unable to edit: this record does not exist or has been deleted.',
        'update_not_found' => 'Unable to update: this record does not exist or has been deleted.',
        'delete_not_found' => 'Unable to delete: this record does not exist or has already been deleted.',
    ]
};
PHP
            );
        }
        if (!$fs->exists($crudFr)) {
            $fs->put(
                $crudFr,
                <<<PHP
<?php
return [
    'List' => 'Liste',
    'Create' => 'Créer',
    'Edit' => 'Éditer',
    'Details' => 'Détails',
    'Actions' => 'Actions',
    'New' => 'Nouveau',
    'Save' => 'Enregistrer',
    'Back' => 'Retour',
    'Delete' => 'Supprimer',
    'Delete?' => 'Supprimer ?',
    'No data' => 'Aucune donnée',
    
    'boolean' => [
        'yes' => 'Oui',
        'no' => 'Non',
    ],
    
    'messages' => [
        'created' => 'Enregistrement créé avec succès.',
        'updated' => 'Enregistrement modifié avec succès.',
        'deleted' => 'Enregistrement supprimé avec succès.',
        'not_found' => 'Cet enregistrement n\'existe pas ou a été supprimé.',
        'edit_not_found' => 'Impossible de modifier : cet enregistrement n\'existe pas ou a été supprimé.',
        'update_not_found' => 'Impossible de mettre à jour : cet enregistrement n\'existe pas ou a été supprimé.',
        'delete_not_found' => 'Impossible de supprimer : cet enregistrement n\'existe pas ou a déjà été supprimé.',
        'access_denied' => 'Accès refusé : vous n\'avez pas les permissions nécessaires.',
        'cannot_delete_self' => 'Vous ne pouvez pas supprimer votre propre compte.',
    ]
};
PHP
            );
        }

        $this->info("Traductions générées : lang/en/{$entitySlug}.php et lang/fr/{$entitySlug}.php");

        // NOUVEAU : Ajouter à la liste des entités traitées (en kebab-case)
        $this->allEntitiesProcessed[] = Str::kebab($entity);

        // NOUVEAU : Générer/Mettre à jour automatiquement le PermissionSeeder
        $this->updatePermissionSeeder();

        // AJOUTER vers la ligne ~2000 - Méthode manquante pour configurer les middlewares
        $this->configurePermissionMiddleware();

        $this->info("✅ CRUD Bootstrap généré pour {$entity} avec permissions !");
        return 0;
    }

    private function labelFrom(string $column): string
    {
        return Str::headline($column); // ex: company_status -> Company Status
    }

    private function labelFromFR(string $column): string
    {
        // Traductions spécifiques pour les champs courants
        $translations = [
            'status' => 'Statut',
            'priority' => 'Priorité',
            'company_id' => 'Entreprise',
            'author_id' => 'Auteur',
            'assigned_to' => 'Assigné à',
            'assigned_at' => 'Date d\'assignation',
            'due' => 'Échéance',
            'folder_code' => 'Code dossier',
            'subject' => 'Sujet',
            'question' => 'Description',
            'billable' => 'Facturable',
            'name' => 'Nom',
            'email' => 'Email',
            'password' => 'Mot de passe',
            'phone' => 'Téléphone',
            'address' => 'Adresse',
            'city' => 'Ville',
            'zip' => 'Code postal',
            'country' => 'Pays',
            'website' => 'Site web',
            'description' => 'Description',
            'created_at' => 'Créé le',
            'updated_at' => 'Modifié le',
        ];
        
        return $translations[$column] ?? Str::headline($column);
    }

    /**
     * Convertit un nom de colonne de clé étrangère en nom de relation
     * Ex: company_id -> company, author_id -> author
     */
    private function getRelationNameFromColumn(string $column): string
    {
        // Pour les colonnes comme author_id, assigned_to, etc.
        if (Str::endsWith($column, '_id')) {
            return Str::camel(Str::replaceLast('_id', '', $column));
        }
        
        // Pour les colonnes comme assigned_to
        if ($column === 'assigned_to') {
            return 'assignedUser'; // Relation spéciale
        }
        
        return Str::camel($column);
    }

    /**
     * Génère la liste des relations à charger pour l'eager loading
     */
    private function getEagerLoadingRelations(array $columns): array
    {
        $relations = [];
        
        foreach ($columns as $column) {
            if (isset($this->foreignKeys[$column])) {
                $relationName = $this->getRelationNameFromColumn($column);
                $relations[] = $relationName;
            }
        }
        
        return $relations;
    }

    private function getColumnsMeta(string $table): array
    {
        $columns = DB::select("
            SELECT 
                COLUMN_NAME as name,
                IS_NULLABLE as nullable,
                DATA_TYPE as type,
                CHARACTER_MAXIMUM_LENGTH as len,
                COLUMN_DEFAULT as default_value,
                COLUMN_TYPE as full_type
            FROM INFORMATION_SCHEMA.COLUMNS 
            WHERE TABLE_SCHEMA = DATABASE() 
            AND TABLE_NAME = ?
            ORDER BY ORDINAL_POSITION
        ", [$table]);

        $meta = [];
        foreach ($columns as $col) {
            $meta[$col->name] = $col;
        }
        return $meta;
    }

    /**
     * Analyser TOUS les types de colonnes depuis les migrations
     */
    private function getColumnTypesFromMigration(string $modelName): array
    {
        $migrationPath = database_path('migrations');
        $columnTypes = [];
        $tableName = $this->table;
        
        // Chercher le fichier de migration pour cette table spécifique
        $migrationFiles = glob($migrationPath . '/*_create_' . $tableName . '_table.php');
        
        if (empty($migrationFiles)) {
            return $columnTypes;
        }
        
        $migrationFile = end($migrationFiles);
        $content = file_get_contents($migrationFile);
        
        // Extraire seulement la partie inside Schema::create
        $pattern = '/Schema::create\([\'"]' . preg_quote($tableName, '/') . '[\'"],\s*function\s*\([^)]*\)\s*\{(.*?)\}\);/s';
        if (preg_match($pattern, $content, $schemaMatch)) {
            $tableDefinition = $schemaMatch[1];
            
            // Pattern pour capturer les définitions de colonnes (pas les index)
            $columnPattern = '/\$table\s*->\s*(\w+)\s*\(\s*[\'"](\w+)[\'"].*?\);/';
            preg_match_all($columnPattern, $tableDefinition, $matches, PREG_SET_ORDER);
            
            foreach ($matches as $match) {
                $columnType = $match[1];
                $columnName = $match[2];
                
                // Types de colonnes valides de Laravel
                $validColumnTypes = [
                    'id', 'bigIncrements', 'increments', 'string', 'text', 'mediumText', 'longText',
                    'integer', 'bigInteger', 'tinyInteger', 'smallInteger', 'unsignedInteger',
                    'decimal', 'float', 'double', 'boolean', 'json', 'datetime', 'timestamp',
                    'date', 'time', 'enum', 'foreignId', 'uuid', 'binary'
                ];
                
                if (in_array($columnType, $validColumnTypes)) {
                    $columnTypes[$columnName] = $columnType;
                }
            }
        }
        
        return $columnTypes;
    }

    private function getBooleanFieldsFromMigration(string $modelName): array
    {
        $migrationPath = database_path('migrations');
        $booleanFields = [];
        $booleanEnumFields = []; // Nouveau tableau pour les booléens avec enum
        
        // Parcourir tous les fichiers de migration
        $migrationFiles = glob($migrationPath . '/*.php');
        
        foreach ($migrationFiles as $file) {
            $content = file_get_contents($file);
            
            // Détecter les champs boolean avec commentaire enum
            if (preg_match_all('/\$table->boolean\([\'"]([^\'"]+)[\'"]\).*?\/\/\s*([^;\n\r]+)/', $content, $matches, PREG_SET_ORDER)) {
                foreach ($matches as $match) {
                    $fieldName = $match[1];
                    $comment = trim($match[2]);
                    
                    // Vérifier si le commentaire contient des options séparées par |
                    if (str_contains($comment, '|')) {
                        $options = array_map('trim', explode('|', $comment));
                        $this->booleanEnumFields[$fieldName] = $options;
                    } else {
                        // Boolean classique
                        $booleanFields[] = $fieldName;
                    }
                }
            }
            
            // Regex pour détecter les champs boolean sans commentaire
            if (preg_match_all('/\$table->boolean\([\'"]([^\'"]+)[\'"]\)(?![^;]*\/\/)/', $content, $matches)) {
                $booleanFields = array_merge($booleanFields, $matches[1]);
            }
        }
        
        // Stocker les deux types
        $this->booleanEnumFields = $booleanEnumFields;
        
        return array_unique($booleanFields);
    }

    private function getJsonFieldsFromMigration(string $modelName): array
    {
        $migrationPath = database_path('migrations');
        $jsonFields = [];
        $jsonFieldsWithBooleanHint = [];
        
        $migrationFiles = glob($migrationPath . '/*.php');
        
        foreach ($migrationFiles as $file) {
            $content = file_get_contents($file);
            
            // Détecter TOUS les champs JSON
            if (preg_match_all('/\$table->json\([\'"]([^\'"]+)[\'"]\)/', $content, $matches)) {
                $jsonFields = array_merge($jsonFields, $matches[1]);
            }
            
            // Détecter les champs JSON avec commentaire booléen (bonus)
            if (preg_match_all('/\$table->json\([\'"]([^\'"]+)[\'"]\).*?\/\/\s*\{([^}]+)\}/', $content, $matches, PREG_SET_ORDER)) {
                foreach ($matches as $match) {
                    $fieldName = $match[1];
                    $jsonStructure = $match[2];
                    
                    // Vérifier si c'est une structure booléenne
                    if (preg_match_all('/"([^"]+)"\s*:\s*(true|false)/', $jsonStructure, $keyMatches)) {
                        $jsonFieldsWithBooleanHint[$fieldName] = $keyMatches[1];
                    }
                }
            }
        }
        
        return [
            'json' => array_unique($jsonFields),
            'json_boolean' => $jsonFieldsWithBooleanHint
        ];
    }

    /**
     * Détecte les clés étrangères depuis les migrations
     */
    private function getForeignKeysFromMigrations(string $tableName): array
    {
        $migrationPath = database_path('migrations');
        $foreignKeys = [];
        
        $migrationFiles = glob($migrationPath . '/*.php');
        
        foreach ($migrationFiles as $file) {
            $content = file_get_contents($file);
            
            // Rechercher les références à notre table
            if (!str_contains($content, $tableName)) {
                continue;
            }
            
            // Patterns pour détecter les FK
            $patterns = [
                // $table->foreign('company_id')->references('id')->on('companies');
                '/\$table->foreign\([\'"]([^\'"]*)[\'"]\)->references\([\'"]([^\'"]*)[\'"]\)->on\([\'"]([^\'"]*)[\'"]\)/',
                // $table->foreignId('company_id')->references('id')->on('companies');
                '/\$table->foreignId\([\'"]([^\'"]*)[\'"]\)->references\([\'"]([^\'"]*)[\'"]\)->on\([\'"]([^\'"]*)[\'"]\)/',
                // $table->foreignId('company_id')->constrained('companies');
                '/\$table->foreignId\([\'"]([^\'"]*)[\'"]\)->constrained\([\'"]([^\'"]*)[\'"]\)/',
                // $table->foreignId('company_id')->constrained(); (assume table name from field)
                '/\$table->foreignId\([\'"]([^\'"]*)[\'"]\)->constrained\(\)/',
            ];
            
            foreach ($patterns as $pattern) {
                if (preg_match_all($pattern, $content, $matches, PREG_SET_ORDER)) {
                    foreach ($matches as $match) {
                        $localKey = $match[1];
                        
                        // Déterminer la table référencée
                        if (count($match) >= 4) {
                            $referencedTable = $match[3];
                            $referencedKey = $match[2] ?? 'id';
                        } elseif (count($match) === 3) {
                            $referencedTable = $match[2];
                            $referencedKey = 'id';
                        } else {
                            // Pour ->constrained() sans paramètre, déduire le nom de table
                            $referencedTable = Str::plural(str_replace('_id', '', $localKey));
                            $referencedKey = 'id';
                        }
                        
                        $foreignKeys[$localKey] = [
                            'table' => $referencedTable,
                            'key' => $referencedKey,
                            'model' => Str::studly(Str::singular($referencedTable))
                        ];
                    }
                }
            }
        }
        
        // AJOUT : Détection par convention de nommage pour les champs non déclarés explicitement
        if (Schema::hasTable($tableName)) {
            $columns = Schema::getColumnListing($tableName);
            foreach ($columns as $column) {
                if (str_ends_with($column, '_id') && !isset($foreignKeys[$column])) {
                    $referencedTable = Str::plural(str_replace('_id', '', $column));
                    if (Schema::hasTable($referencedTable)) {
                        $foreignKeys[$column] = [
                            'table' => $referencedTable,
                            'key' => 'id',
                            'model' => Str::studly(Str::singular($referencedTable))
                        ];
                    }
                }
            }
        }
        
        return $foreignKeys;
    }

    private function inputFor(string $column, string $varSing, string $entitySlug, string $tableName = ''): string
    {
        $name  = $column;
        $labelExpr = "__('{$entitySlug}.fields.{$name}')";
        $varToken = '$' . $varSing;
        $lower = strtolower($column);

        // Cas spécial : champ password
        if ($lower === 'password') {
            return <<<HTML
        <div class="col-12 col-lg-6">
            <x-forms.input name="{$name}" 
                           :label="{$labelExpr}" 
                           type="password" 
                           placeholder="Laissez vide pour conserver le mot de passe actuel" />
        </div>
HTML;
        }

        // Cas spécial : clé étrangère (relation)
        if (isset($this->foreignKeys[$column])) {
            $fk = $this->foreignKeys[$column];
            $modelName = $fk['model'];
            $tableName = $fk['table'];
            $displayField = $this->getDisplayFieldForTable($tableName);
            
            return <<<HTML
        <div class="col-12 col-lg-6">
            <x-forms.relation name="{$name}" 
                              :label="{$labelExpr}" 
                              model="{$modelName}" 
                              display-field="{$displayField}"
                              :value="{$varToken}->{$name} ?? null" />
        </div>
HTML;
        }

        // NOUVEAU : Champs enum Laravel natifs (PRIORITÉ sur les autres)
        if (isset($this->enumFields[$column])) {
            $options = $this->enumFields[$column];
            $optionsArray = [];
            foreach ($options as $option) {
                $optionsArray[] = "'{$option}' => __('{$entitySlug}.{$column}.{$option}')";
            }
            $optionsStr = '[' . implode(', ', $optionsArray) . ']';
            
            return <<<HTML
        <div class="col-12 col-lg-4">
            <x-forms.select name="{$name}" 
                            :label="{$labelExpr}" 
                            :options="{$optionsStr}"
                            :value="{$varToken}->{$name} ?? '{$options[0]}'" />
        </div>
HTML;
        }

        // Cas spécial : champs timestamp/datetime/date (basé sur le type de colonne)
        $columnType = $this->columnTypes[$column] ?? null;
        if (in_array($columnType, ['datetime', 'timestamp', 'date']) || Str::endsWith($lower, '_at') || in_array($lower, ['last_login', 'email_verified_at'])) {
            $inputType = ($columnType === 'date') ? 'date' : 'datetime-local';
            $format = ($columnType === 'date') ? 'Y-m-d' : 'Y-m-d\\\\TH:i';
            
            return <<<HTML
        <div class="col-12 col-lg-6">
            <x-forms.input name="{$name}" 
                           :label="{$labelExpr}" 
                           type="{$inputType}"
                           :value="old('{$name}', {$varToken}->{$name} ? ({$varToken}->{$name} instanceof \\Carbon\\Carbon ? {$varToken}->{$name}->format('{$format}') : {$varToken}->{$name}) : '')" />
        </div>
HTML;
        }

        // Cas spécial : JSON avec structure booléenne (commentaire détecté)
        if (isset($this->jsonBooleanFields[$column])) {
            $optionsArray = [];
            foreach ($this->jsonBooleanFields[$column] as $key) {
                $optionsArray[] = "'{$key}' => __('{$entitySlug}.fields.{$name}_{$key}')";
            }
            $optionsStr = '[' . implode(', ', $optionsArray) . ']';
            $valuesStr = "old('{$name}', is_array({$varToken}->{$name}) ? {$varToken}->{$name} : (is_string({$varToken}->{$name}) ? json_decode({$varToken}->{$name}, true) : []))";
            
            return <<<HTML
        <div class="col-12 col-lg-6">
            <x-forms.checkbox-group name="{$name}" 
                                    :label="{$labelExpr}" 
                                    :options="{$optionsStr}"
                                    :values="{$valuesStr}" />
        </div>
HTML;
        }

        // Cas général : champ JSON (textarea)
        if (in_array($column, $this->jsonFields)) {
            $jsonValue = "is_array({$varToken}->{$name}) ? json_encode({$varToken}->{$name}, JSON_PRETTY_PRINT) : {$varToken}->{$name}";
            return <<<HTML
        <div class="col-12">
            <x-forms.input name="{$name}" 
                           :label="{$labelExpr}" 
                           type="textarea" 
                           :rows="4" 
                           placeholder='{"key": "value"}'
                           :value="old('{$name}', {$jsonValue})" />
            <small class="form-text text-muted">Format JSON attendu</small>
        </div>
HTML;
        }

        // NOUVEAU : Champs booléens avec enum (ex: active|inactive)
        if (isset($this->booleanEnumFields[$column])) {
            $options = $this->booleanEnumFields[$column];
            $optionsArray = [];
            foreach ($options as $index => $option) {
                $value = $index === 0 ? 'true' : 'false'; // Premier = true, second = false
                $optionsArray[] = "'{$value}' => '" . ucfirst($option) . "'";
            }
            $optionsStr = '[' . implode(', ', $optionsArray) . ']';
            $defaultValue = 'true';
            
            return <<<HTML
        <div class="col-12 col-lg-4">
            <x-forms.select name="{$name}" 
                            :label="{$labelExpr}" 
                            :options="{$optionsStr}"
                            :value="old('{$name}', {$varToken}->{$name} ?? '{$defaultValue}')" />
        </div>
HTML;
        }

        // Champs booléans (basé sur le type de colonne ou détection existante)
        if ($columnType === 'boolean' || in_array($column, $this->booleanFields)) {
            return <<<HTML
        <div class="col-12 col-lg-4">
            <x-forms.select name="{$name}" 
                            :label="{$labelExpr}" 
                            :options="['0' => __('global.boolean.no'), '1' => __('global.boolean.yes')]"
                            :value="old('{$name}', {$varToken}->{$name} ?? false) ? '1' : '0'" />
        </div>
HTML;
        }

        // textarea pour les champs text/longText (basé sur le type de colonne)
        if (in_array($columnType, ['text', 'longText', 'mediumText']) || $this->isLongTextField($tableName, $column) || $this->isTextField($tableName, $column)) {
            return <<<HTML
        <div class="col-12">
            <x-forms.input name="{$name}" 
                           :label="{$labelExpr}" 
                           type="textarea" 
                           :rows="4"
                           :value="old('{$name}', {$varToken}->{$name} ?? null)" />
        </div>
HTML;
        }

        // types spécifiques
        $type = 'text';
        if ($lower === 'email') {
            $type = 'email';
        }
        if ($lower === 'website' || Str::endsWith($lower, 'url')) {
            $type = 'url';
        }
        if (Str::contains($lower, 'phone') || $lower === 'tel') {
            $type = 'tel';
        }

        $placeholder = '';
        if ($lower === 'country') {
            $placeholder = 'FR';
        }

        // taille de colonne par défaut
        $colClass = 'col-12 col-lg-6';
        if (in_array($lower, ['city', 'zip', 'country'])) {
            $colClass = 'col-12 col-lg-4';
        }
        if ($lower === 'name') {
            $colClass = 'col-12 col-lg-8';
        }

        $additionalAttrs = '';
        if ($lower === 'country') {
            $additionalAttrs = 'maxlength="2"';
        }

        return <<<HTML
        <div class="{$colClass}">
            <x-forms.input name="{$name}" 
                           :label="{$labelExpr}" 
                           type="{$type}"
                           :value="old('{$name}', {$varToken}->{$name} ?? null)"
                           placeholder="{$placeholder}" {$additionalAttrs} />
        </div>
HTML;
    }

    /**
     * Détermine le champ d'affichage pour une table donnée
     */
    private function getDisplayFieldForTable(string $tableName): string
    {
        // Champs d'affichage courants par ordre de préférence
        $displayFields = ['name', 'title', 'label', 'email', 'username', 'code'];
        
        // Vérifier quels champs existent dans la table
        if (Schema::hasTable($tableName)) {
            $columns = Schema::getColumnListing($tableName);
            foreach ($displayFields as $field) {
                if (in_array($field, $columns)) {
                    return $field;
                }
            }
            
            // Fallback : premier champ string après id
            foreach ($columns as $column) {
                if ($column !== 'id' && !str_ends_with($column, '_at') && !str_ends_with($column, '_id')) {
                    return $column;
                }
            }
        }
        
        // Ultime fallback
        return 'id';
    }

    private function isLongTextField(string $tableName, string $columnName): bool
    {
        $migrationPath = database_path('migrations');
        $files = glob($migrationPath . '/*_create_' . $tableName . '_table.php');
        
        if (empty($files)) {
            return false;
        }
        
        $migrationFile = end($files);
        $content = file_get_contents($migrationFile);
        
        // Cherche les déclarations longText pour ce champ
        $pattern = '/\$table\s*->\s*longText\s*\(\s*[\'"]' . preg_quote($columnName, '/') . '[\'"].*?\)/';
        
        return preg_match($pattern, $content) === 1;
    }

    private function isTextField(string $tableName, string $columnName): bool
    {
        $migrationPath = database_path('migrations');
        $files = glob($migrationPath . '/*_create_' . $tableName . '_table.php');
        
        if (empty($files)) {
            return false;
        }
        
        $migrationFile = end($files);
        $content = file_get_contents($migrationFile);
        
        // Cherche les déclarations text pour ce champ
        $pattern = '/\$table\s*->\s*text\s*\(\s*[\'"]' . preg_quote($columnName, '/') . '[\'"].*?\)/';
        
        return preg_match($pattern, $content) === 1;
    }

    private function isDateTimeField(string $tableName, string $columnName): bool
    {
        // Obtenir les informations du schéma pour vérifier le type de colonne
        try {
            $columnType = DB::select("
                SELECT DATA_TYPE 
                FROM INFORMATION_SCHEMA.COLUMNS 
                WHERE TABLE_SCHEMA = DATABASE() 
                AND TABLE_NAME = ? 
                AND COLUMN_NAME = ?
            ", [$tableName, $columnName]);
            
            if (!empty($columnType)) {
                $type = strtolower($columnType[0]->DATA_TYPE ?? '');
                return in_array($type, ['datetime', 'timestamp', 'date']);
            }
        } catch (\Exception $e) {
            // Fallback : chercher dans les migrations
            $modelName = Str::studly(Str::singular($tableName));
            $migrationFiles = glob(database_path('migrations/*_create_' . $tableName . '_table.php'));
            
            if (empty($migrationFiles)) {
                return false;
            }
            
            $migrationFile = end($migrationFiles);
            $content = file_get_contents($migrationFile);
            
            // Chercher les déclarations datetime/timestamp/date
            $patterns = [
                '/\$table\s*->\s*datetime\s*\(\s*[\'"]' . preg_quote($columnName, '/') . '[\'"].*?\)/',
                '/\$table\s*->\s*timestamp\s*\(\s*[\'"]' . preg_quote($columnName, '/') . '[\'"].*?\)/',
                '/\$table\s*->\s*date\s*\(\s*[\'"]' . preg_quote($columnName, '/') . '[\'"].*?\)/',
            ];
            
            foreach ($patterns as $pattern) {
                if (preg_match($pattern, $content)) {
                    return true;
                }
            }
        }
        
        return false;
    }

    private function stubIndex(string $entity, string $entitySlug, string $varSing, string $varPlur, array $columns): string
    {
        $ths = '';
        $tds = '';
        
        foreach ($columns as $column) {
            if (in_array($column, ['id', 'created_at', 'updated_at'])) {
                continue;
            }
            
            $ths .= "\n<th class=\"text-left\">{{ __('{$entitySlug}.fields.{$column}') }}</th>";
            
            $lower = strtolower($column);
            
            // Gestion spéciale pour les clés étrangères
            if (isset($this->foreignKeys[$column])) {
                $fk = $this->foreignKeys[$column];
                $modelName = $fk['model'];
                $displayField = $this->getDisplayFieldForTable($fk['table']);
                // Utiliser la relation au lieu de find() pour éviter le problème N+1
                $relationName = $this->getRelationNameFromColumn($column);
                $tds .= "\n<td>{{ \${$varSing}->{$relationName}?->{$displayField} ?? '' }}</td>";
            }
            // CORRIGÉ : Gestion des champs enum Laravel avec traductions et couleurs dynamiques
            elseif (isset($this->enumFields[$column])) {
                $enumValues = $this->enumFields[$column];
                
                // Générer dynamiquement les case statements
                $switchCases = '';
                foreach ($enumValues as $enumValue) {
                    $badgeColor = $this->getBadgeColorForEnumValue($enumValue);
                    $switchCases .= "\n    @case('{$enumValue}') @php(\$badgeColor = '{$badgeColor}') @break";
                }
                
                $tds .= "\n<td>";
                $tds .= "\n@php(\$badgeColor = 'secondary')"; // Couleur par défaut
                $tds .= "\n@switch(\${$varSing}->{$column})";
                $tds .= $switchCases;
                $tds .= "\n@endswitch";
                $tds .= "\n<span class=\"badge bg-{{ \$badgeColor }}\">{{ __('{$entitySlug}.{$column}.' . \${$varSing}->{$column}) }}</span>";
                $tds .= "\n</td>";
            }
            // Gestion spéciale pour les champs timestamp/datetime
            elseif (Str::endsWith($lower, '_at') || in_array($lower, ['last_login', 'email_verified_at'])) {
                $tds .= "\n<td>{{ \${$varSing}->{$column} ? (\${$varSing}->{$column} instanceof \\Carbon\\Carbon ? \${$varSing}->{$column}->format('d/m/Y H:i') : \${$varSing}->{$column}) : '' }}</td>";
            }
            // Gestion spéciale pour les champs JSON avec structure booléenne
            elseif (isset($this->jsonBooleanFields[$column])) {
                $keys = json_encode($this->jsonBooleanFields[$column]);
                $tds .= "\n<td>@php(\$selected = collect({$keys})->filter(fn(\$key) => \${$varSing}->{$column}[\$key] ?? false)->map(fn(\$key) => __('{$entitySlug}.fields.{$column}_' . \$key))->join(', ')){{ \$selected ?: '' }}</td>";
            }
            // NOUVEAU : Gestion des booléens avec enum
            elseif (isset($this->booleanEnumFields[$column])) {
                $options = $this->booleanEnumFields[$column];
                $activeOption = $options[0]; // Premier = true
                $inactiveOption = $options[1] ?? 'Inactive'; // Second = false
                $tds .= "\n<td>{{ \${$varSing}->{$column} ? '{$activeOption}' : '{$inactiveOption}' }}</td>";
            }
            // Gestion spéciale pour les autres champs JSON
            elseif (in_array($column, $this->jsonFields)) {
                // Pour les autres champs JSON, afficher une version lisible
                $tds .= "\n<td>{{ is_array(\${$varSing}->{$column}) ? json_encode(\${$varSing}->{$column}) : \${$varSing}->{$column} }}</td>";
            } elseif (in_array($column, $this->booleanFields)) {
                // Pour les champs booléens classiques
                $tds .= "\n<td>{{ \${$varSing}->{$column} ? __('global.boolean.yes') : __('global.boolean.no') }}</td>";
            } elseif ($column === 'password') {
                // Pour le password, afficher des étoiles
                $tds .= "\n<td>••••••••</td>";
            } else {
                // Pour les autres champs, affichage normal
                $tds .= "\n<td>{{ \${$varSing}->{$column} }}</td>";
            }
        }

        // Calculer le nombre de colonnes pour le colspan
        // +1 pour la colonne ID, +1 pour la colonne Actions
        $totalColumns = count($columns) + 2;

        return <<<BLADE
@extends('layouts.app')

@section('title')
@if(auth()->check() && auth()->user()->hasRole('manager'))
{{ __('{$entitySlug}.List') }}
@else  
{{ __('{$entitySlug}.YourList') }}
@endif
@endsection

@section('content')
<h1 class="mb-4">{{ __('{$entitySlug}.List') }}</h1>

<div class="table-responsive-lg">
<table class="table align-middle table-xs table-bordered table-hover">
<thead>
<tr>
<th class="text-center">{{ __('{$entitySlug}.id') }}</th>{$ths}
<th>{{ __('global.Actions') }}</th>
</tr>
</thead>
<tbody>
@forelse(\${$varPlur} as \${$varSing})
<tr>
<td class="text-center">{{ \${$varSing}->id }}</td>{$tds}
<td class="text-nowrap">
<a href="{{ route('{$entitySlug}.show', \${$varSing}) }}" class="btn btn-link text-decoration-none p-0 me-2">
{!! __('global.Details') !!}
</a>
<a href="{{ route('{$entitySlug}.edit', \${$varSing}) }}" class="btn btn-link text-decoration-none p-0 me-2">
{!! __('global.Edit') !!}
</a>
@include('{$entitySlug}._delete_form', ['{$varSing}' => \${$varSing}])
</td>
</tr>
@empty
<tr>
<td colspan="{$totalColumns}" class="text-center">
{{ __('global.No data') }}
</td>
</tr>
@endforelse
</tbody>
</table>
</div>

<a href="{{ route('{$entitySlug}.create') }}" class="btn btn-orange mt-3">
    <i class="fa-regular fa-square-plus"></i>
{{ __('global.New') }}
</a>
@endsection
BLADE;
    }

    private function stubDeleteForm(string $entity, string $entitySlug, string $varSing): string
    {
        return <<<BLADE
<form method="POST" action="{{ route('{$entitySlug}.destroy', \${$varSing}) }}" 
onsubmit="return confirm('{{ __('global.Delete?') }}');" 
style="display:inline">
@csrf
@method('DELETE')
<button type="submit" class="btn btn-link text-decoration-none text-orange p-0">
{!! __('global.Delete') !!}
</button>
</form>
BLADE;
    }

    private function stubForm(string $entity, string $entitySlug, string $varSing, array $columns, string $tableName = ''): string
    {
        $fields = '';
        foreach ($columns as $column) {
            if (in_array($column, ['id', 'created_at', 'updated_at'])) {
                continue;
            }
            $fields .= $this->inputFor($column, $varSing, $entitySlug, $tableName) . "\n";
        }

        return <<<BLADE
<div class="row g-3">
{$fields}
</div>
<div class="btn-group mt-3" role="group" aria-label="Basic example">
<button type="submit" class="btn btn-primary">{{ __('global.Save') }}</button>
<a href="{{ route('{$entitySlug}.index') }}" class="btn btn-outline-primary">{!! __('global.Back') !!}</a>
</div>
BLADE;
    }

    private function stubCreate(string $entity, string $entitySlug): string
    {
        $varSing = Str::camel($entity);
        return <<<BLADE
@extends('layouts.app')

@section('title', __('global.Create') . '  ' . __('{$entitySlug}.entity'))

@section('content')
    <h1 class="h3 mb-3">{{ __('global.Create') }}  {{ __('{$entitySlug}.entity') }}</h1>

    @php(\${$varSing} = new \\App\\Models\\{$entity}())

    <form method="POST" action="{{ route('{$entitySlug}.store') }}" novalidate>
        @csrf
        @include('{$entitySlug}._form')
    </form>
@endsection
BLADE;
    }

    private function stubEdit(string $entity, string $entitySlug, string $varSing): string
    {
        $singToken = '$' . $varSing;
        return <<<BLADE
@extends('layouts.app')

@section('title', __('global.Edit') . '  ' . __('{$entitySlug}.entity'))

@section('content')
    <h1 class="h3 mb-3">{!! __('global.Edit') !!}  {{ __('{$entitySlug}.entity') }}</h1>

    <form method="POST" action="{{ route('{$entitySlug}.update', {$singToken}) }}" novalidate>
        @csrf
        @method('PUT')
        @include('{$entitySlug}._form')
    </form>
@endsection
BLADE;
    }

    private function stubShow(string $entity, string $entitySlug, string $varSing, array $columns): string
    {
        $singToken = '$' . $varSing;
        
        // Ajouter l'ID en premier
        $idRow = '        <dt class="col-sm-3">{{ __(\'' . $entitySlug . '.id\') }}</dt>' . "\n" .
                 '        <dd class="col-sm-9">{{ ' . $singToken . '->id }}</dd>';
        
        $rows = collect($columns)->map(function ($c) use ($singToken, $entitySlug) {
            // Ignorer l'ID car déjà traité
            if ($c === 'id') {
                return null;
            }
            
            $label = '        <dt class="col-sm-3">{{ __(\'' . $entitySlug . '.fields.' . $c . '\') }}</dt>';
            $lower = strtolower($c);
            
            // Gestion spéciale pour les clés étrangères
            if (isset($this->foreignKeys[$c])) {
                $fk = $this->foreignKeys[$c];
                $modelName = $fk['model'];
                $displayField = $this->getDisplayFieldForTable($fk['table']);
                $value = '        <dd class="col-sm-9">{{ ' . $singToken . '->' . $c . ' ? \\App\\Models\\' . $modelName . '::find(' . $singToken . '->' . $c . ')?->' . $displayField . " : '' }}</dd>";
            }
            // Gestion spéciale pour les champs timestamp/datetime
            elseif (Str::endsWith($lower, '_at') || in_array($lower, ['last_login', 'email_verified_at'])) {
                $value = '        <dd class="col-sm-9">{{ ' . $singToken . '->' . $c . ' ? (' . $singToken . '->' . $c . ' instanceof \\Carbon\\Carbon ? ' . $singToken . '->' . $c . "->format('d/m/Y à H:i') : " . $singToken . '->' . $c . ") : '' }}</dd>";
            }
            // CORRIGÉ : Gestion des champs enum Laravel avec traductions et couleurs dynamiques
            elseif (isset($this->enumFields[$c])) {
                $enumValues = $this->enumFields[$c];
                
                // Générer dynamiquement les case statements
                $switchCases = '';
                foreach ($enumValues as $enumValue) {
                    $badgeColor = $this->getBadgeColorForEnumValue($enumValue);
                    $switchCases .= "\n                @case('{$enumValue}') @php(\$badgeColor = '{$badgeColor}') @break";
                }
                
                $value = <<<HTML
        <dd class="col-sm-9">
            @php(\$badgeColor = 'secondary')
            @switch({$singToken}->{$c}){$switchCases}
            @endswitch
            <span class="badge bg-{{ \$badgeColor }}">{{ __('{$entitySlug}.{$c}.' . {$singToken}->{$c}) }}</span>
        </dd>
HTML;
            }
            // Autres champs JSON
            elseif (in_array($c, $this->jsonFields)) {
                // Pour les champs JSON, affichage formaté
                $value = '        <dd class="col-sm-9"><pre>{{ is_array(' . $singToken . '->' . $c . ') ? json_encode(' . $singToken . '->' . $c . ', JSON_PRETTY_PRINT) : (' . $singToken . '->' . $c . " ?? '') }}</pre></dd>";
            } elseif (in_array($c, $this->booleanFields)) {
                // Pour les champs booléens classiques
                $value = '        <dd class="col-sm-9">{{ ' . $singToken . '->' . $c . " ? __('global.boolean.yes') : __('global.boolean.no') }}</dd>";
            } elseif ($c === 'password') {
                // Pour le password
                $value = '        <dd class="col-sm-9">••••••••</dd>';
            } else {
                // Pour les autres champs
                $value = '        <dd class="col-sm-9">{{ ' . $singToken . '->' . $c . " ?? '' }}</dd>";
            }
            
            return $label . "\n" . $value;
        })->filter()->implode("\n");
        
        // Combiner l'ID avec les autres champs
        $allRows = $idRow . "\n" . $rows;

        return <<<BLADE
@extends('layouts.app')

@section('title', __('global.Details') . '  ' . __('{$entitySlug}.entity'))

@section('content')
    <h1 class="h3 mb-3">{!! __('global.Details') !!}  {{ __('{$entitySlug}.entity') }}</h1>

    <dl class="row">
{$allRows}
    </dl>

    <div class="btn-group mt-3" role="group" aria-label="Actions">
        <a href="{{ route('{$entitySlug}.edit', {$singToken}) }}" class="btn btn-primary">{!! __('global.Edit') !!}</a>
        <a href="{{ url()->previous() }}" class="btn btn-outline-primary">{!! __('global.Back') !!}</a>
    </div>
@endsection
BLADE;
    }

    /**
     * Détecte les champs enum depuis les migrations pour une table spécifique
     */
    private function getEnumFieldsFromMigration(string $modelName): array
    {
        $migrationPath = database_path('migrations');
        $enumFields = [];

        // Déduire le nom de la table à partir du modèle
        $tableName = strtolower(Str::plural($modelName)); // User => users
        if ($modelName === 'User') {
            $tableName = 'users'; // Cas spécial pour le modèle User
        }

        $migrationFiles = glob($migrationPath . '/*.php');

        foreach ($migrationFiles as $file) {
            $content = file_get_contents($file);
            
            $this->info("Analyse du fichier: " . basename($file));
            
            // NOUVEAU : Extraire seulement le contenu du Schema::create pour notre table
            $pattern = "/Schema::create\('{$tableName}',\s*function\s*\([^)]*\)\s*\{(.*?)\}\);/s";
            if (preg_match($pattern, $content, $schemaMatch)) {
                $tableContent = $schemaMatch[1];
                $this->info("Schema trouvé pour la table {$tableName}");
                
                // Chercher les enum dans ce contenu spécifique
                if (preg_match_all('/\$table->enum\([\'"]([^\'"]+)[\'"]\s*,\s*\[(.*?)\]/', $tableContent, $matches, PREG_SET_ORDER)) {
                    foreach ($matches as $match) {
                        $fieldName = $match[1];
                        $enumValues = $match[2];
                        
                        // Extraire les valeurs enum
                        if (preg_match_all('/[\'"]([^\'"]+)[\'"]/', $enumValues, $valueMatches)) {
                            $options = $valueMatches[1];
                            $enumFields[$fieldName] = $options;
                            $this->info("Enum natif détecté dans {$tableName}: {$fieldName} => " . json_encode($options));
                        }
                    }
                }
            }
            
            // FALLBACK : Si pas trouvé avec la méthode précise, essayer l'ancienne méthode
            if (empty($enumFields) && str_contains($content, $tableName)) {
                $this->info("Fallback: recherche globale dans le fichier pour {$tableName}");
                
                if (preg_match_all('/\$table->enum\([\'"]([^\'"]+)[\'"]\s*,\s*\[(.*?)\]/', $content, $matches, PREG_SET_ORDER)) {
                    foreach ($matches as $match) {
                        $fieldName = $match[1];
                        $enumValues = $match[2];
                        
                        if (preg_match_all('/[\'"]([^\'"]+)[\'"]/', $enumValues, $valueMatches)) {
                            $options = $valueMatches[1];
                            $enumFields[$fieldName] = $options;
                            $this->info("Enum natif détecté (fallback) dans {$tableName}: {$fieldName} => " . json_encode($options));
                        }
                    }
                }
            }
        }
        
        return $enumFields;
    }

    /**
     * Détermine la couleur du badge pour une valeur enum donnée
     */
   
    private function getBadgeColorForEnumValue(string $enumValue): string
    {
        // Mapping des valeurs courantes vers des couleurs Bootstrap
        $colorMap = [
            // États positifs
            'active' => 'success',
            'enabled' => 'success',
            'published' => 'success',
            'approved' => 'success',
            'completed' => 'success',
            'verified' => 'success',
            'confirmed' => 'success',
            'online' => 'success',
            
            // États négatifs
            'inactive' => 'secondary',
            'disabled' => 'secondary',
            'draft' => 'secondary',
            'unpublished' => 'secondary',
            'rejected' => 'danger',
            'cancelled' => 'danger',
            'failed' => 'danger',
            'offline' => 'secondary',
            'deleted' => 'danger',
            
            // États d'attente
            'pending' => 'warning',
            'waiting' => 'warning',
            'processing' => 'warning',
            'review' => 'warning',
            'scheduled' => 'warning',
            
            // États neutres/informatifs
            'new' => 'info',
            'open' => 'info',
            'closed' => 'secondary',
            'archived' => 'secondary',
            
            // Priorités
            'high' => 'danger',
            'medium' => 'warning',
            'low' => 'info',
            'urgent' => 'danger',
            'normal' => 'primary',
            
            // Types
            'public' => 'success',
            'private' => 'warning',
            'internal' => 'info',
        ];
        
        $lowerValue = strtolower($enumValue);
        
        return $colorMap[$lowerValue] ?? 'primary'; // Couleur par défaut
    }

    /**
     * Génère ou met à jour automatiquement le PermissionSeeder
     */
    private function updatePermissionSeeder(): void
    {
        $fs = new Filesystem();
        $seederPath = database_path('seeders/PermissionSeeder.php');
        
        
        // Récupérer toutes les entités existantes en scannant les contrôleurs
        $allEntities = $this->getAllExistingEntities();
        
        // Générer le contenu du seeder
        $permissionsCode = $this->generatePermissionsCode($allEntities);
        $rolesCode = $this->generateRolesCode($allEntities);
        
        $seederContent = <<<PHP
<?php

namespace Database\\Seeders;

use Illuminate\\Database\\Seeder;
use Spatie\\Permission\\Models\\Role;
use Spatie\\Permission\\Models\\Permission;
use App\\Models\\User;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\\Spatie\\Permission\\PermissionRegistrar::class]->forgetCachedPermissions();

        // === PERMISSIONS AUTOMATIQUES ===
        {$permissionsCode}

        // === RÔLES ===
        {$rolesCode}

        // === UTILISATEUR SUPER ADMIN PAR DÉFAUT ===
        \$superAdminUser = User::firstOrCreate([
            'email' => 'a@a.com'
        ], [
            'name' => 'Super Admin',           // AJOUTER
            'password' => bcrypt('azerty'),  // AJOUTER
            'status' => 'active',
            'agree_terms' => 'oui'
        ]);
        \$superAdminUser->assignRole('super-admin');

        \$this->command->info('Permissions et rôles créés automatiquement !');
        \$this->command->info('Super Admin : a@a.com / azerty');
    }
}
PHP;

        // Créer le répertoire seeders s'il n'existe pas
        $fs->ensureDirectoryExists(database_path('seeders'));
        
        // Écrire le seeder
        $fs->put($seederPath, $seederContent);
        
        // Mettre à jour DatabaseSeeder.php
        $this->updateDatabaseSeeder();
        
        $this->info("✅ PermissionSeeder généré automatiquement avec les permissions pour toutes les entités");
    }

    /**
     * Récupère toutes les entités existantes en scannant les contrôleurs
     */
    private function getAllExistingEntities(): array
    {
        $fs = new Filesystem();
        $controllerPath = app_path('Http/Controllers');
        $entities = [];
        
        if ($fs->exists($controllerPath)) {
            $files = $fs->files($controllerPath);
            foreach ($files as $file) {
                $filename = $file->getFilename();
                // Pattern : EntityController.php
                if (preg_match('/^([A-Z][a-zA-Z]+)Controller\.php$/', $filename, $matches)) {
                    $entityName = $matches[1];
                    // Exclure les contrôleurs système
                    if (!in_array($entityName, ['Auth', 'Home', 'Welcome', 'Controller'])) {
                        $entities[] = strtolower($entityName);
                    }
                }
            }
        }
        
        return array_unique($entities);
    }

    /**
     * Génère le code des permissions pour toutes les entités
     */
    private function generatePermissionsCode(array $entities): string
    {
        $code = [];
        
        foreach ($entities as $entity) {
            // Convertir en kebab-case pour les permissions
            $entitySlug = Str::kebab($entity);
            $code[] = "        // Permissions {$entitySlug}";
            $code[] = "        Permission::firstOrCreate(['name' => '{$entitySlug}.index']);";
            $code[] = "        Permission::firstOrCreate(['name' => '{$entitySlug}.show']);";
            $code[] = "        Permission::firstOrCreate(['name' => '{$entitySlug}.create']);";
            $code[] = "        Permission::firstOrCreate(['name' => '{$entitySlug}.edit']);";
            $code[] = "        Permission::firstOrCreate(['name' => '{$entitySlug}.delete']);";
            $code[] = "";
        }
        
        // Permissions génériques
        $code[] = "        // Permissions génériques";
        $code[] = "        Permission::firstOrCreate(['name' => 'admin.access']);";
        $code[] = "        Permission::firstOrCreate(['name' => 'reports.access']);";
        
        return implode("\n", $code);
    } // ← CETTE ACCOLADE FERMANTE MANQUAIT !

    /**
     * Génère le code des rôles avec attribution automatique des permissions
     */
    private function generateRolesCode(array $entities): string
    {
        // Construire la liste des permissions pour chaque rôle
        $adminPermissions = ['admin.access', 'reports.access'];
        $managerPermissions = [];
        $userPermissions = [];
        
        foreach ($entities as $entity) {
            // Convertir en kebab-case pour les permissions
            $entitySlug = Str::kebab($entity);
            
            // Admin : toutes les permissions
            $adminPermissions[] = "{$entitySlug}.index";
            $adminPermissions[] = "{$entitySlug}.show";
            $adminPermissions[] = "{$entitySlug}.create";
            $adminPermissions[] = "{$entitySlug}.edit";
            $adminPermissions[] = "{$entitySlug}.delete";
            
            // Manager : pas de suppression
            $managerPermissions[] = "{$entitySlug}.index";
            $managerPermissions[] = "{$entitySlug}.show";
            if (!in_array($entitySlug, ['user'])) { // Manager ne peut pas créer d'users
                $managerPermissions[] = "{$entitySlug}.create";
            }
            $managerPermissions[] = "{$entitySlug}.edit";
            
            // User : seulement lecture sur user et company
            if (in_array($entitySlug, ['user', 'company'])) {
                $userPermissions[] = "{$entitySlug}.show";
            }
        }
        
        $adminPermsStr = "'" . implode("', '", $adminPermissions) . "'";
        $managerPermsStr = "'" . implode("', '", $managerPermissions) . "'";
        $userPermsStr = "'" . implode("', '", $userPermissions) . "'";
        
        return <<<CODE
        // Super Admin : Tous les droits
        \$superAdmin = Role::firstOrCreate(['name' => 'super-admin']);
        \$superAdmin->syncPermissions(Permission::all());
        
        // Admin : Gestion complète
        \$admin = Role::firstOrCreate(['name' => 'admin']);
        \$admin->syncPermissions([
            {$adminPermsStr}
        ]);
        
        // Manager : Gestion limitée (pas de suppression)
        \$manager = Role::firstOrCreate(['name' => 'manager']);
        \$manager->syncPermissions([
            {$managerPermsStr}
        ]);
        
        // User : Lecture seule sur profil et company
        \$user = Role::firstOrCreate(['name' => 'user']);
        \$user->syncPermissions([
            {$userPermsStr}
        ]);
CODE;
    }

    /**
     * Met à jour DatabaseSeeder.php pour inclure PermissionSeeder
     */
    private function updateDatabaseSeeder(): void
    {
        $fs = new Filesystem();
        $databaseSeederPath = database_path('seeders/DatabaseSeeder.php');
        
        if ($fs->exists($databaseSeederPath)) {
            $content = $fs->get($databaseSeederPath);
            
            // Vérifier si PermissionSeeder n'est pas déjà présent
            if (!str_contains($content, 'PermissionSeeder::class')) {
                // Chercher la méthode run() et ajouter le seeder
                $pattern = '/(public function run\(\): void\s*\{)/';
                $replacement = '$1' . "\n" . '        $this->call([' . "\n" . '            PermissionSeeder::class,' . "\n" . '        ]);' . "\n";
                
                $newContent = preg_replace($pattern, $replacement, $content);
                
                if ($newContent && $newContent !== $content) {
                    $fs->put($databaseSeederPath, $newContent);
                    $this->info("✅ DatabaseSeeder mis à jour avec PermissionSeeder");
                }
            }
        }
    }

    /**
     * Dans app/Console/Commands/MakeCrudBootstrap.php
     * Ajouter cette méthode de configuration du modèle User
     */
    private function configureUserModel()
    {
        $modelPath = app_path('Models/User.php');
        
        if (file_exists($modelPath)) {
            $modelContent = file_get_contents($modelPath);
            
            // Vérifier si le trait HasRoles est déjà présent
            if (!str_contains($modelContent, 'use Spatie\\Permission\\Traits\\HasRoles;')) {
                // Ajouter l'import du trait
                $modelContent = str_replace(
                    'use Illuminate\\Notifications\\Notifiable;',
                    "use Illuminate\\Notifications\\Notifiable;\nuse Spatie\\Permission\\Traits\\HasRoles;",
                    $modelContent
                );
                
                // Ajouter l'utilisation du trait
                $modelContent = str_replace(
                    'use HasFactory, Notifiable;',
                    'use HasFactory, Notifiable, HasRoles;',
                    $modelContent
                );
                
                file_put_contents($modelPath, $modelContent);
                $this->info('✅ Trait HasRoles ajouté au modèle User');
            }
        }
    }

    private function configurePermissionMiddleware(): void
    {
        $this->info("🔧 Configuration des middlewares Spatie pour Laravel 12");
        
        $appPath = base_path('bootstrap/app.php');
        if (!file_exists($appPath)) {
            $this->error("❌ Fichier bootstrap/app.php introuvable !");
            return;
        }
        
        $appContent = file_get_contents($appPath);
        
        // Vérifier si les middlewares Spatie ne sont pas déjà configurés
        if (!str_contains($appContent, 'permission') && !str_contains($appContent, 'PermissionMiddleware')) {
            
            // Ajouter les imports nécessaires
            if (!str_contains($appContent, 'use Illuminate\Foundation\Configuration\Middleware;')) {
                $this->error("❌ Structure de bootstrap/app.php non standard pour Laravel 12");
                return;
            }
            
            $middlewareConfig = <<<'PHP'

    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'permission' => \Spatie\Permission\Middleware\PermissionMiddleware::class,
            'role' => \Spatie\Permission\Middleware\RoleMiddleware::class,
            'role_or_permission' => \Spatie\Permission\Middleware\RoleOrPermissionMiddleware::class,
        ]);
    })
PHP;
            
            // Rechercher le bon point d'insertion pour Laravel 12
            if (str_contains($appContent, '->withExceptions(')) {
                $appContent = str_replace('->withExceptions(', $middlewareConfig . "\n    ->withExceptions(", $appContent);
                file_put_contents($appPath, $appContent);
                $this->info("✅ Middlewares permission configurés dans bootstrap/app.php");
            } else if (str_contains($appContent, '->create()')) {
                $appContent = str_replace('->create()', $middlewareConfig . "\n    ->create()", $appContent);
                file_put_contents($appPath, $appContent);
                $this->info("✅ Middlewares permission configurés dans bootstrap/app.php");
            } else {
                $this->error("❌ Impossible de trouver le point d'insertion dans bootstrap/app.php");
            }
        } else {
            $this->info("✅ Middlewares Spatie déjà configurés dans bootstrap/app.php");
        }
    }
}
