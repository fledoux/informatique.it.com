<?php

namespace App\Http\Controllers;

use \App\Models\User;
use App\Http\Requests\UserStoreRequest;
use App\Http\Requests\UserUpdateRequest;
use App\Helpers\Helper;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    /**
     * Apply permission middleware to controller methods.
     */
    public function __construct()
    {
        // Permissions standard pour les opérations CRUD
        $this->middleware('permission:user.index')->only('index');
        $this->middleware('permission:user.create')->only(['create', 'store']);
        $this->middleware('permission:user.show')->only('show');
        $this->middleware('permission:user.edit')->only(['edit', 'update']);
        $this->middleware('permission:user.delete')->only('destroy');

        // Permissions spéciales pour l'impersonation
        $this->middleware('role:super-admin')->only('impersonate');
    }

    public function index()
    {
        $currentUser = Auth::user();

        $query = User::query()->with(['company', 'roles']);

        // Si l'utilisateur est manager ou admin, filtrer par sa société
        if ($currentUser->hasAnyRole(['manager', 'admin'])) {
            $query->where('company_id', $currentUser->company_id);
        }

        $users = $query->latest('id')->paginate(15);

        // Récupérer les domaines autorisés pour la société de l'utilisateur
        $allowedDomains = [];
        $allowedDomains = \App\Models\AllowDomainRegistration::where('company_id', $currentUser->company_id)
            ->pluck('domain')
            ->toArray();

        return view('user.index', compact('users', 'allowedDomains'));
    }

    public function create()
    {
        // Vérifier l'autorisation avec la policy
        $this->authorize('create', User::class);

        $user = new \App\Models\User();
        return view('user.create', compact('user'));
    }

    public function store(UserStoreRequest $request)
    {
        $data = $request->validated();
        if (!empty($data['password'])) {
            $data['password'] = bcrypt($data['password']);
        } else {
            unset($data['password']);
        }

        // Générer les initiales automatiquement
        $data['initial'] = Helper::generateInitials($data['firstname'] ?? '', $data['lastname'] ?? '');

        // Si l'utilisateur n'est pas super-admin (ce qui ne devrait pas arriver grâce à la policy)
        // on force les valeurs par défaut par sécurité
        $currentUser = Auth::user();
        if (!$currentUser->hasRole('super-admin')) {
            $data['company_id'] = $currentUser->company_id;
            $data['agree_terms'] = 'oui';
        }

        // Extraire les rôles des données
        $roles = $data['roles'] ?? [];
        unset($data['roles']);

        $user = User::create($data);

        // Assigner les rôles si présents
        if (!empty($roles)) {
            $user->syncRoles($roles);
        }

        return redirect()->route('user.index')->with('success', __('global.messages.created'));
    }

    public function show($id)
    {
        try {
            $user = User::query()->with(['company'])->findOrFail($id);

            // Vérifier l'autorisation avec la policy
            $this->authorize('view', $user);

            return view('user.show', compact('user'));
        } catch (ModelNotFoundException $e) {
            return redirect()->route('user.index')
                ->with('error', __('global.messages.not_found'));
        }
    }

    public function edit($id)
    {
        try {
            $user = User::query()->with(['company'])->findOrFail($id);

            // Vérifier l'autorisation avec la policy
            $this->authorize('update', $user);

            return view('user.edit', compact('user'));
        } catch (ModelNotFoundException $e) {
            return redirect()->route('user.index')
                ->with('error', __('global.messages.edit_not_found'));
        }
    }

    public function update(UserUpdateRequest $request, $id)
    {
        try {
            $user = User::findOrFail($id);
            $data = $request->validated();
            if (!empty($data['password'])) {
                $data['password'] = bcrypt($data['password']);
            } else {
                unset($data['password']);
            }

            // Générer les initiales automatiquement
            $data['initial'] = Helper::generateInitials($data['firstname'] ?? '', $data['lastname'] ?? '');

            // Si l'utilisateur n'est pas super-admin, on préserve les valeurs existantes
            $currentUser = Auth::user();
            if (!$currentUser->hasRole('super-admin')) {
                // Ne pas permettre de changer la société, agree_terms, email ou note
                $data['company_id'] = $user->company_id;
                $data['agree_terms'] = $user->agree_terms;
                $data['email'] = $user->email;
                $data['note'] = $user->note;
            }

            // Extraire les rôles des données
            $roles = $data['roles'] ?? null;
            unset($data['roles']);

            $user->update($data);

            // Synchroniser les rôles uniquement si fournis (super-admin)
            if ($roles !== null) {
                $user->syncRoles($roles);
            }

            return redirect()->route('user.index')->with('success', __('global.messages.updated'));
        } catch (ModelNotFoundException $e) {
            return redirect()->route('user.index')
                ->with('error', __('global.messages.update_not_found'));
        }
    }

    public function destroy($id)
    {
        try {
            $user = User::findOrFail($id);

            // Empêcher l'auto-suppression
            if (strtolower('User') === 'user' && Auth::check() && $user->id === Auth::id()) {
                return redirect()->route('user.index')
                    ->with('error', __('global.messages.cannot_delete_self'));
            }

            $user->delete();
            return redirect()->route('user.index')->with('success', __('global.messages.deleted'));
        } catch (ModelNotFoundException $e) {
            return redirect()->route('user.index')
                ->with('error', __('global.messages.delete_not_found'));
        }
    }

    /**
     * Se connecter en tant qu'autre utilisateur (impersonation)
     * Réservé aux super-admin
     */
    public function impersonate($id)
    {
        // Vérifier que l'utilisateur actuel est super-admin
        if (!Auth::user()->hasRole('super-admin')) {
            abort(403, 'Action non autorisée');
        }

        try {
            $userToImpersonate = User::findOrFail($id);

            // Sauvegarder l'ID de l'admin actuel en session
            session(['impersonating_from' => Auth::id()]);

            // Se connecter en tant que l'autre utilisateur
            Auth::loginUsingId($userToImpersonate->id);

            return redirect()->route('dashboard')
                ->with('success', "Connexion en tant que {$userToImpersonate->name}");
        } catch (ModelNotFoundException $e) {
            return redirect()->route('user.index')
                ->with('error', __('global.messages.not_found'));
        }
    }

    /**
     * Revenir à son compte d'origine (stop impersonation)
     */
    public function stopImpersonation()
    {
        $originalUserId = session('impersonating_from');

        if (!$originalUserId) {
            return redirect()->route('dashboard')
                ->with('error', 'Aucune impersonation en cours');
        }

        try {
            // Vérifier que l'utilisateur d'origine existe et est super-admin
            $originalUser = User::findOrFail($originalUserId);

            if (!$originalUser->hasRole('super-admin')) {
                // Sécurité : l'utilisateur d'origine n'est plus super-admin
                session()->forget('impersonating_from');
                Auth::logout();
                return redirect()->route('login')
                    ->with('error', 'Session d\'impersonation invalide');
            }

            // Supprimer la session d'impersonation
            session()->forget('impersonating_from');

            // Se reconnecter avec le compte d'origine
            Auth::loginUsingId($originalUserId);

            return redirect()->route('user.index')
                ->with('success', 'Retour au compte administrateur');
        } catch (ModelNotFoundException $e) {
            // L'utilisateur d'origine n'existe plus
            session()->forget('impersonating_from');
            Auth::logout();
            return redirect()->route('login')
                ->with('error', 'Session d\'impersonation invalide');
        }
    }
}
