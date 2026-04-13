@if($patients->hasPages())
    {{ $patients->links() }}
@endif