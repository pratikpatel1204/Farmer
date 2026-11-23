@if ($inquiries->hasPages())
    {{ $inquiries->onEachSide(1)->links('pagination::bootstrap-5') }}
@elseif ($inquiries->total() > 0)
    <p class="small text-muted mb-0">
        Showing {{ $inquiries->firstItem() }} to {{ $inquiries->lastItem() }} of {{ $inquiries->total() }} results
    </p>
@endif
