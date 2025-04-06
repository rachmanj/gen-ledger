<div class="btn-group">
    <a href="{{ route('accounts.edit', $account->id) }}" class="btn btn-sm btn-primary">
        <i class="fas fa-edit"></i>
    </a>
    <form action="{{ route('accounts.destroy', $account->id) }}" method="POST" class="d-inline">
        @csrf
        @method('DELETE')
        <button type="submit" class="btn btn-sm btn-danger"
            onclick="return confirm('Are you sure you want to delete this account?')">
            <i class="fas fa-trash"></i>
        </button>
    </form>
</div>
