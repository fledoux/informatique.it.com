<form method="POST" action="{{ route('company.destroy', $company) }}" 
onsubmit="return confirm('{{ __('global.Delete?') }}');" 
style="display:inline">
@csrf
@method('DELETE')
<button type="submit" class="btn btn-link text-decoration-none text-orange p-0">
{{ __('global.Delete') }}
</button>
</form>