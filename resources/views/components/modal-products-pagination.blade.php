<div class="form-check form-switch mb-3 d-flex align-items-center">

    <input onchange="customerPriceInitState(`{{ $outletId }}`, this)" class="form-check-input" type="checkbox" value=""
        id="switchCustomPrice" switch {{ $isCustomPrice ? 'checked' : '' }}>
    <label class="form-check-label ms-2" for="switchCustomPrice">Kustom Harga</label>
    <div class="col-md-4 d-flex flex-column ms-5">
        <button onclick="syncProduct(`{{ $outletId }}`)" style="width:200px" class="btn btn-primary">Sinkronisasi
            Produk</button>
        <span class="text-danger mt-2">Silakan klik untuk sinkornisasi produk</span>
    </div>
</div>
<div class="table-responsive">
    <table class="table">
        <thead>
            <tr>
                <td>SKU</td>
                <td>Nama Produk</td>
                <td>Harga Dasar</td>
                <td>Harga Jual</td>
                <td align="center">Aksi</td>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td><input type="text" class="form-control" name="product_id" id="sku"
                        value="{{ $filters['product_id'] ?? '' }}"></td>
                <td><input type="text" class="form-control" name="product_name" id="productName"
                        value="{{ $filters['product_name'] ?? '' }}"></td>
                <td><input type="text" class="form-control" name="base_price" id="basePrice"
                        value="{{ $filters['base_price'] ?? '' }}"></td>
                <td><input style="width:250px" type="text" class="form-control" name="selling_price" id="sellingPrice"
                        value="{{ $filters['selling_price'] ?? '' }}">
                </td>
                <td class="text-center"><span>#</span></td>
            </tr>
            @forelse ($customerPrices as $p)
            <tr>
                <td>{{$p->product?->product_id}}</td>
                <td>{{$p->product?->product_name}}</td>
                <td><span>{{\App\Helpers\AppHelpers::formatToRupiah($p->base_price)}}</span></td>
                <td class="sellingPrice">
                    @if($isCustomPrice)
                    <div class="input-group custom-price-wrapper" style="width:250px">
                        <input min="1000" inputmode="numeric" autocomplete="off" class="form-control custom-price-input"
                            id="customPrice" data-id="{{ $p->id }}" type="text"
                            value="{{ \App\Helpers\AppHelpers::formatToRupiah($p->selling_price) }}" disabled>
                        <button title="Mengunci harga untuk disimpan"
                            onclick="lockCustomPrice(this, `{{ json_encode($p) }}`)"
                            class="btn btn-outline-secondary btn-lock-price" type="button"><i
                                class="bi bi-lock-fill"></i></button>
                    </div>

                    @else
                    <span>{{\App\Helpers\AppHelpers::formatToRupiah($p->selling_price)}}</span>
                    @endif

                </td>
                <td>

                    <a class="btn btn-sm btn-danger" onclick="restoreSellingPrice(`{{ $p->id }}`)"><i
                            class=" bi bi-arrow-clockwise"></i></a>
                </td>
            </tr>
            @empty
            <tr class="tr-empty">
                <td colspan="5" class="text-center">Tidak ada data ditemukan</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <x-pagination-info :paginator="$customerPrices" />
</div>
<script>
    const customPrices = container.querySelectorAll('#customPrice');

    customPrices.forEach(link => {
        handleRupiahFormat(link)
    });
   
</script>