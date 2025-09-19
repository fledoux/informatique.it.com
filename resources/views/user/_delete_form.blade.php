<form method="POST" action="{{ route('user.destroy', $user) }}" 
onsubmit="return confirm('{{ __('crud.Delete?') }}');" 
style="display:inline">
@csrf
@method('DELETE')
<button type="submit" class="btn btn-link text-decoration-none text-orange p-0">
{{ __('crud.Delete') }}
</button>
</form>