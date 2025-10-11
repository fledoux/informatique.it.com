# Project Guidelines

## 🎯 PRINCIPE FONDAMENTAL : SIMPLICITÉ TOUJOURS !

**TOUJOURS privilégier la solution la plus SIMPLE :**
- ✅ CSS global > Classes répétées partout
- ✅ Convention > Configuration
- ✅ DRY (Don't Repeat Yourself)
- ✅ Une seule source de vérité
- ✅ Moins de code = Moins de bugs

**Exemples concrets :**
```css
/* ✅ BIEN : 1 règle CSS globale */
.table th { white-space: nowrap; }

/* ❌ MAL : text-nowrap sur 100+ éléments <th> dans les vues */
<th class="text-nowrap">...</th>
```

```php
/* ✅ BIEN : Logique métier dans le modèle */
public function getManagers() {
    return User::where('company_id', $this->company_id)->role('manager')->get();
}

/* ❌ MAL : Requête SQL directement dans la vue */
@foreach(User::where('company_id', $ticket->company_id)->role('manager')->get() as $manager)
```

**Avant de coder, demandez-vous :**
1. Est-ce que je répète du code ?
2. Puis-je créer une règle CSS globale ?
3. Cette logique appartient-elle au modèle ?
4. Y a-t-il une solution plus simple ?

## Project Context
Laravel 12.x helpdesk application with multi-tenant architecture. Key business domains:
- **Multi-tenant**: Companies with associated users using Spatie Permissions
- **Ticket system**: Full lifecycle from creation to billing (see `DEV.md` for complete data model)
- **Time tracking & billing**: Charges with decimal units, credit system
- **File attachments**: Per-ticket and per-message attachments
- **Approval workflows**: Message approval via email/SMS tokens
- **Conversation sharing**: UUID-based public ticket access
- **SMS notifications**: AWS SNS integration for ticket updates
- **Email helpers**: FuelPHP-style helpers in `Helper::mailTo()`

## Architecture Patterns

### Multi-Tenant Structure
- `Company` model is the tenant root - all data relates to a company
- `User` belongs to `Company` via `company_id` foreign key
- Spatie Laravel-Permission for role-based access control
- Permission naming: `{resource}.{action}` (e.g., `user.create`, `company.edit`)

### Controller Patterns
Controllers follow this consistent structure:
```php
// Use Form Requests for validation
public function store(CompanyStoreRequest $request)
// Always wrap findOrFail in try-catch
try {
    $company = Company::findOrFail($id);
} catch (ModelNotFoundException $e) {
    return redirect()->route('company.index')
        ->with('error', __('global.messages.not_found'));
}
```

### Form Request Validation
- Separate Store/Update requests (e.g., `CompanyStoreRequest`, `CompanyUpdateRequest`)
- Country validation: `'country' => ['nullable','string','size:2']` (ISO-2 codes)
- Status enums: `'status' => ['required','in:active,inactive']`

### Route Authorization
User routes have granular permission middleware:
```php
Route::get('/', [UserController::class, 'index'])
    ->middleware('permission:user.index')
    ->name('user.index');
```
Company routes use standard resource controller without explicit permissions.

### Helper Classes
Generic helper for common utilities in `app/Helpers/Helper.php`:
```php
// Generate initials from firstname + lastname
Helper::generateInitials('Jean', 'Dupont'); // → 'JD'

// Format full name with fallback
Helper::getFullName($firstname, $lastname, $name); // → 'Jean Dupont' or fallback to $name

// Format French phone numbers
Helper::formatPhone('0123456789'); // → '01 23 45 67 89'

// Get badge color for status
Helper::getStatusBadgeColor('active'); // → 'success'

// FuelPHP-style email helper
Helper::mailTo('email@example.com', 'Name', ['class' => 'link']) // → '<a href="mailto:..." class="link">Name</a>'

// Convert numbers to letters (for UI display)
Helper::asLetters(2) // → 'deux'
```
- Use static methods for reusability across controllers and views
- Prefer helper over duplicate logic in controllers
- User initials are auto-generated from firstname/lastname fields

### Model Patterns
```php
// Always encapsulate business logic in models, never in views
public function getManagers() {
    return User::where('company_id', $this->company_id)->role('manager')->get();
}

// Use relations consistently
public function assignedTo(): BelongsTo {
    return $this->belongsTo(User::class, 'assigned_to');
}
```
- **CRITICAL**: Never put database queries directly in views - always use model methods
- Service classes for external integrations (e.g., `SmsService` for AWS SNS)

## Development Workflow

### Local Development
Use composer script for full stack:
```bash
composer run dev
```
This runs: server, queue worker, pail logs, and Vite in parallel with concurrently.

### Testing
- SQLite in-memory database for tests
- Run tests: `composer run test`
- Use factories for test data generation

### Database Standards
- All status fields use string enums (not integers)
- Decimal fields for billing: `decimal(12,3)` for precise unit calculations
- UUID fields for public sharing: `string(36)`
- Foreign keys: Always include `->index()` and proper cascading

### Translation Keys
- French is primary language (`resources/lang/fr/`)
- CRUD operations use `crud.php` translation file
- Flash messages: `__('global.messages.created')`, `__('global.messages.updated')`

### View Architecture
- Bootstrap 5 CDN (no local assets pipeline yet)
- Base layout: `resources/views/layouts/app.blade.php` (authenticated), `layouts/public.blade.php` (public)
- All views extend `@extends('layouts.app')`
- Flash message handling built into layout
- Custom CSS: `public/assets/css/app.css` with responsive width utilities
- Extended Bootstrap width classes: `w-sm-25`, `w-md-50`, `w-lg-75`, etc. (25, 50, 75, 100, auto for all breakpoints sm, md, lg, xl, xxl)
- Auto dark theme: `data-bs-theme="auto"` with system preference detection
- Responsive design: Use `d-none d-sm-flex` for desktop-only elements, `d-block d-sm-none` for mobile-only

### Component Patterns
- Partial views for reusable UI: `@include('ticket._addTicket')` for action menus
- Dropdown menus with `d-grid` for full-width buttons
- FontAwesome icons consistently: `<i class="fa-solid fa-chevron-up"></i>`
- Date formatting: Always use `d/m/y à H\hi` for French locale (2-digit year, escaped 'h')

### Permission System
Three roles defined in `PermissionSeeder`:
- `super-admin`: All permissions
- `admin`: Full CRUD + admin/reports access
- `manager`: No delete permissions, limited to CRUD operations

### External Integrations
- **AWS SDK**: Same credentials for SES (email) and SNS (SMS) - `config/services.php`
- **SMS Service**: `app/Services/SmsService.php` with static `send()` method for AWS SNS
- **Public ticket access**: UUID-based sharing with expiration (`public_uuid`, `public_uuid_expires`)
- **QR codes**: Chillerlan library for generating ticket QR codes

### UI/UX Patterns
- **Scroll to top**: Automatic button in `layouts/public.blade.php` (appears at 300px scroll)
- **Language switcher**: Only in local environment via `@if (app()->isLocal())`
- **Dropdown actions**: Replace button groups with `dropdown` + `d-grid` for mobile-friendly menus
- **Text truncation**: Use `\Illuminate\Support\Str::limit($text, 20)` for long text
- **Pluralization**: Dynamic plurals `{{ $count > 1 ? 's' : '' }}` with `Helper::asLetters()` for numbers

## Key Files Reference
- Data model specification: `DEV.md`
- Permission setup: `database/seeders/PermissionSeeder.php`
- Form validation patterns: `app/Http/Requests/`
- Route authorization: `routes/web.php`
- Generic utilities: `app/Helpers/Helper.php`
- SMS integration: `app/Services/SmsService.php`
- Ticket model methods: `app/Models/Ticket.php` (getManagers, getPublicLink, sendSmsWithLink)
- Troubleshooting guide: `.github/TROUBLESHOOTING.md`