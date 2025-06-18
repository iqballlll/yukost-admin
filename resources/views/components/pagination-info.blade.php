<div class="mt-3 d-flex justify-content-between align-items-center">
    <div>
        <span id="firstItem">{{ $paginator->firstItem() ?? 0 }}</span> sampai <select id="perPageSelect"
            onchange="changePerPage()" style="width: auto; display: inline-block;">
            @foreach([5, 10, 25, 50, 100, 500] as $option)
            <option {{ request('per_page')==$option ? 'selected' : '' }} value="{{ $option }}" {{
                request('page')==$option ? 'selected' : '' }}>{{ $option }}</option>
            @endforeach
        </select> dari <span id="totalItem">{{
            $paginator->total() ?? 0 }}</span>
    </div>
    <div>
        {{ $paginator->links('pagination::bootstrap-4') }}
    </div>

</div>

<script>
    function changePerPage() {
            const perPage = document.getElementById('perPageSelect').value;
            const params = new URLSearchParams(window.location.search);
            params.set('per_page', perPage);
            window.location.href = `${window.location.pathname}?${params.toString()}`;
    }
</script>