# Project Guidelines

## Project Context
Laravel 12.x helpdesk application with multi-tenant architecture. Key business domains:
- **Multi-tenant**: Companies with associated users using Spatie Permissions
- **Ticket system**: Full lifecycle from creation to billing (see `DEV.md` for complete data model)
- **Time tracking & billing**: Charges with decimal units, credit system
- **File attachments**: Per-ticket and per-message attachments
- **Approval workflows**: Message approval via email/SMS tokens
- **Conversation sharing**: UUID-based public ticket access

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
        ->with('error', __('crud.messages.not_found'));
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
- Flash messages: `__('crud.messages.created')`, `__('crud.messages.updated')`

### View Architecture
- Bootstrap 5 CDN (no local assets pipeline yet)
- Base layout: `resources/views/layouts/app.blade.php`
- All views extend `@extends('layouts.app')`
- Flash message handling built into layout

### Permission System
Three roles defined in `PermissionSeeder`:
- `super-admin`: All permissions
- `admin`: Full CRUD + admin/reports access
- `manager`: No delete permissions, limited to CRUD operations

## Key Files Reference
- Data model specification: `DEV.md`
- Permission setup: `database/seeders/PermissionSeeder.php`
- Form validation patterns: `app/Http/Requests/`
- Route authorization: `routes/web.php`