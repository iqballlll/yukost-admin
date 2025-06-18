<table class="table">
    <thead>
        <tr>
            <th><input type="checkbox" onclick="toggleAll(this)"></th>
            <th>Nama</th>
            <th>Harga</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($products as $product)
        <tr>
            <td>
                <input type="checkbox" class="produk-checkbox" data-product="{{ json_encode($product) }}"
                    data-id="{{ $product->id }}" data-name="{{ $product->product_name }}"
                    data-price="{{ $product->selling_price }}">
            </td>
            <td>{{ $product->product_name }}</td>
            <td>Rp. {{ number_format($product->selling_price) }}</td>
        </tr>
        @endforeach
    </tbody>
</table>

<div class="mt-3 d-flex justify-content-between align-items-center">
    <div>
        <span id="firstItem">{{ $products->firstItem() ?? 0 }}</span> sampai <select id="perPageSelect"
            onchange="changePerPageModalCustomerPrice()" style="width: auto; display: inline-block;">
            @foreach([5, 10, 25, 50, 100, 500] as $option)
            <option {{ request('per_page')==$option ? 'selected' : '' }} value="{{ $option }}" {{
                request('page')==$option ? 'selected' : '' }}>{{ $option }}</option>
            @endforeach
        </select> dari <span id="totalItem">{{
            $products->total() ?? 0 }}</span>
    </div>
    <div>
        {{ $products->links('pagination::bootstrap-4') }}
    </div>

</div>
<div class="d-grid mt-3">
    <button id="btnPilihProduk" class="btn btn-primary">Pilih Produk</button>
</div>