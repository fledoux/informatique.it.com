{{-- Formulaire de suppression pour une permission --}}
<form method="POST" action="{{ route('permissions.destroy', $permission) }}" class="d-inline">
    @csrf
    @method('DELETE')
    <button 
        type="submit" 
        class="btn btn-link text-decoration-none text-danger p-0"
        onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette permission ? Cette action est irréversible.')"
        title="Supprimer"
    >
        <i class="fa-regular fa-trash-can"></i>
    </button>
</form>
