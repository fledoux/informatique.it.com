# Guide des Composants x-forms

Documentation complète du système de composants de formulaires Bootstrap 5 avec floating labels.

## 🎨 Philosophie

Les composants x-forms fournissent une interface uniforme pour tous les formulaires avec :
- **Bootstrap 5** floating labels automatiques
- **Validation** intégrée avec affichage d'erreurs
- **Accessibilité** complète (ARIA, labels, etc.)
- **Traductions** automatiques
- **Cohérence** visuelle sur toute l'application

## 📦 Composants Disponibles

### Input Text/Email/Password
```blade
<x-forms.input 
    name="email" 
    label="E-mail" 
    type="email"
    :value="old('email', $user->email ?? '')"
    placeholder="exemple@domaine.com"
    required 
    readonly
    disabled
/>
```

**Attributs :**
- `name` (requis) - Nom du champ
- `label` (requis) - Label affiché
- `type` - text, email, password, number, tel, url
- `value` - Valeur par défaut
- `placeholder` - Placeholder optionnel
- `required` - Champ obligatoire
- `readonly` - Lecture seule
- `disabled` - Désactivé

### Select Dropdown
```blade
<x-forms.select 
    name="status" 
    label="Statut"
    :options="[
        'active' => __('user.status.active'),
        'inactive' => __('user.status.inactive')
    ]"
    :value="old('status', $user->status ?? '')"
    required
    empty-option="Sélectionner un statut"
/>
```

**Attributs :**
- `name` (requis) - Nom du champ
- `label` (requis) - Label affiché  
- `options` (requis) - Array associatif [value => label]
- `value` - Valeur sélectionnée
- `required` - Champ obligatoire
- `empty-option` - Option vide en premier
- `multiple` - Sélection multiple

### Textarea
```blade
<x-forms.textarea 
    name="description" 
    label="Description"
    :value="old('description', $ticket->description ?? '')"
    rows="4"
    maxlength="1000"
    required
/>
```

**Attributs :**
- `name` (requis) - Nom du champ
- `label` (requis) - Label affiché
- `value` - Contenu par défaut
- `rows` - Nombre de lignes (défaut: 3)
- `maxlength` - Longueur maximale
- `required` - Champ obligatoire

### Checkbox
```blade
<x-forms.checkbox 
    name="agree_terms" 
    label="J'accepte les conditions d'utilisation"
    :checked="old('agree_terms', $user->agree_terms ?? false)"
    value="1"
    required
/>
```

**Attributs :**
- `name` (requis) - Nom du champ
- `label` (requis) - Label affiché
- `checked` - État coché (boolean)
- `value` - Valeur envoyée (défaut: "1")
- `required` - Champ obligatoire

### Group de Checkboxes
```blade
<x-forms.checkbox-group 
    name="channels" 
    label="Canaux de notification"
    :options="[
        'email' => __('user.fields.channels_email'),
        'sms' => __('user.fields.channels_sms')
    ]"
    :checked="old('channels', explode(',', $user->channels ?? ''))"
/>
```

### File Upload
```blade
<x-forms.file 
    name="avatar" 
    label="Photo de profil"
    accept="image/*"
    max-size="2048"
    required
/>
```

## 🎯 Patterns d'Utilisation

### Formulaire de Création
```blade
<form method="POST" action="{{ route('user.store') }}">
    @csrf
    
    <x-forms.input 
        name="firstname" 
        label="{{ __('user.fields.firstname') }}" 
        :value="old('firstname')"
        required 
    />
    
    <x-forms.input 
        name="lastname" 
        label="{{ __('user.fields.lastname') }}" 
        :value="old('lastname')"
        required 
    />
    
    <x-forms.input 
        name="email" 
        label="{{ __('user.fields.email') }}" 
        type="email"
        :value="old('email')"
        required 
    />
    
    <x-forms.select 
        name="company_id" 
        label="{{ __('user.fields.company_id') }}"
        :options="$companies"
        :value="old('company_id')"
        empty-option="Sélectionner une entreprise"
        required
    />
    
    <div class="d-flex gap-1 gap-sm-2 mt-4">
        <button type="submit" class="btn btn-primary">
            {!! __('global.btn.Save') !!}
        </button>
        <a href="{{ route('user.index') }}" class="btn btn-secondary">
            {{ __('global.Cancel') }}
        </a>
    </div>
</form>
```

### Formulaire d'Édition
```blade
<form method="POST" action="{{ route('user.update', $user) }}">
    @csrf
    @method('PUT')
    
    <x-forms.input 
        name="firstname" 
        label="{{ __('user.fields.firstname') }}" 
        :value="old('firstname', $user->firstname)"
        required 
    />
    
    <x-forms.select 
        name="status" 
        label="{{ __('user.fields.status') }}"
        :options="[
            'active' => __('user.status.active'),
            'inactive' => __('user.status.inactive')
        ]"
        :value="old('status', $user->status)"
        required
    />
    
    <!-- ... autres champs -->
</form>
```

### Formulaire avec Validation
```blade
{{-- La validation est automatique, les erreurs s'affichent sous chaque champ --}}

<x-forms.input 
    name="email" 
    label="E-mail" 
    type="email"
    :value="old('email')"
    required 
/>
{{-- 
Si $errors->has('email'), l'erreur s'affiche automatiquement :
- Border rouge sur le champ
- Texte d'erreur en dessous
- Icon d'erreur
--}}
```

## 🎨 Styling et Apparence

### Classes Bootstrap Appliquées
```css
/* Input floating label */
.form-floating .form-control {
    /* Styling automatique Bootstrap 5 */
}

/* États d'erreur */
.is-invalid {
    border-color: #dc3545;
}

/* Messages d'erreur */
.invalid-feedback {
    color: #dc3545;
    font-size: 0.875em;
}
```

### Customisation CSS
```css
/* Dans votre CSS global si nécessaire */
.form-floating > .form-control:focus ~ label {
    color: #0d6efd; /* Couleur du label en focus */
}

.form-floating > .form-select ~ label {
    /* Styling spécifique pour les selects */
}
```

## 🔧 Implémentation Technique

### Structure des Composants
```
resources/views/components/forms/
├── input.blade.php       # Champ input standard
├── select.blade.php      # Select dropdown  
├── textarea.blade.php    # Zone de texte
├── checkbox.blade.php    # Case à cocher unique
├── checkbox-group.blade.php # Groupe de checkboxes
└── file.blade.php        # Upload de fichier
```

### Exemple de Composant Input
```blade
{{-- resources/views/components/forms/input.blade.php --}}
@props([
    'name',
    'label',
    'type' => 'text',
    'value' => '',
    'required' => false,
    'readonly' => false,
    'disabled' => false,
])

<div class="form-floating mb-3">
    <input 
        type="{{ $type }}"
        class="form-control @error($name) is-invalid @enderror"
        id="{{ $name }}"
        name="{{ $name }}"
        value="{{ $value }}"
        placeholder="{{ $label }}"
        @if($required) required @endif
        @if($readonly) readonly @endif
        @if($disabled) disabled @endif
        {{ $attributes }}
    />
    <label for="{{ $name }}">
        {{ $label }}
        @if($required) <span class="text-danger">*</span> @endif
    </label>
    
    @error($name)
        <div class="invalid-feedback">
            {{ $message }}
        </div>
    @enderror
</div>
```

## 📱 Responsive et Accessibilité

### Grid Bootstrap
```blade
<div class="row">
    <div class="col-md-6">
        <x-forms.input name="firstname" label="Prénom" />
    </div>
    <div class="col-md-6">
        <x-forms.input name="lastname" label="Nom" />
    </div>
</div>
```

### Accessibilité Intégrée
- **Labels associés :** `for` et `id` automatiques
- **ARIA attributes :** Pour les états d'erreur
- **Focus management :** Navigation clavier
- **Screen readers :** Support complet

## 🚨 Bonnes Pratiques

### Validation
```php
// Form Request
public function rules()
{
    return [
        'email' => ['required', 'email', 'unique:users'],
        'status' => ['required', 'in:active,inactive'],
    ];
}

// Messages personnalisés
public function messages()
{
    return [
        'email.unique' => __('validation.email_already_taken'),
    ];
}
```

### Traductions
```blade
{{-- Utiliser les clés de traduction --}}
<x-forms.input 
    name="email" 
    label="{{ __('user.fields.email') }}" 
    type="email"
/>

{{-- Pas de hard-coding --}}
<x-forms.input 
    name="email" 
    label="E-mail" {{-- ❌ Éviter --}}
    type="email"
/>
```

### Options Dynamiques
```php
// Controller
$companies = Company::pluck('name', 'id');
return view('user.create', compact('companies'));
```

```blade
{{-- Vue --}}
<x-forms.select 
    name="company_id" 
    label="{{ __('user.fields.company_id') }}"
    :options="$companies"
/>
```

## 🔄 Migration depuis Forms Standards

### Avant (HTML standard)
```blade
<div class="mb-3">
    <label for="email" class="form-label">E-mail *</label>
    <input type="email" class="form-control @error('email') is-invalid @enderror" 
           id="email" name="email" value="{{ old('email') }}" required>
    @error('email')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>
```

### Après (x-forms)
```blade
<x-forms.input 
    name="email" 
    label="E-mail" 
    type="email"
    :value="old('email')"
    required 
/>
```

**Avantages :**
- ✅ 80% moins de code
- ✅ Cohérence automatique
- ✅ Maintenance centralisée
- ✅ Pas d'oubli de validation display

---

*Guide créé le 22 septembre 2025*  
*Composants x-forms pleinement opérationnels*