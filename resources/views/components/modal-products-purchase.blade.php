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
                    data-id="{{ $product->product_id }}" data-name="{{ $product->product_name }}"
                    data-price="{{ $product->selling_price }}">
            </td>
            <td>{{ $product->product_name }}</td>
            <td>Rp. {{ number_format($product->selling_price) }}</td>
        </tr>
        @endforeach
    </tbody>
</table>

<x-pagination-info :paginator="$products" />

<div class="d-grid mt-3">
    <button id="btnPilihProduk" class="btn btn-primary">Pilih Produk</button>
</div>