@extends('layout.index')
@section('content')
<div class="row">
    <div class="col-md-6 mb-3">
        <button onclick="location.href=`{{ route('sales.index') }}`" class="btn btn-sm btn-primary">Kembali</button>
    </div>
</div>
<div class="card">
    <div class="card-body pt-4 pb-4">
        <form action="{{ route('sales.update', $sale->id) }}" method="POST" id="formPenjualan">
            <input type="hidden" name="transaction_id" id="transaction_id" class="form-control" value="{{ $sale->id }}">
            @csrf
            @method('PUT')
            {{-- Form Utama --}}
            <div class="row mb-3">
                <div class="col-md-4">
                    <label for="customerId" class="form-label">Customer (Outlet)</label>
                    <select onchange="onChangeCustomer(this)" name="outlet_id" id="customerId" class="form-select"
                        required>
                        <option value="">-Pilih Customer-</option>
                        @foreach ($customers as $c)
                        <option {{ $sale->outlet_id == $c->id ? 'selected' : '' }} data-outlet="{{ json_encode($c) }}"
                            value="{{ $c->id }}">{{ $c->outlet_name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label for="orderDate" class="form-label">Tanggal Pemesanan</label>
                    <input type="date" name="order_date" id="orderDate" class="form-control"
                        value="{{ $sale->order_date }}" required>
                </div>
                <div class="col-md-4">
                    <label for="totalPrice" class="form-label">Total Harga</label>
                    <input type="text" id="totalPrice" class="form-control"
                        value="{{ \App\Helpers\AppHelpers::formatToRupiah($sale->total_price) }}" disabled>
                    <input type="hidden" name="total_price" id="totalPriceHidden" value="{{ $sale->total_price }}">
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-4">
                    <label for="dueDate" class="form-label">Tanggal Jatuh Tempo</label>
                    <input type="date" name="due_date" id="dueDate" class="form-control" value="{{ $sale->due_date }}">
                </div>
                <div class="col-md-4">
                    <label for="paymentDate" class="form-label">Tanggal Bayar</label>
                    <input type="date" name="payment_date" id="paymentDate" class="form-control"
                        value="{{ $sale->payment_date }}">
                </div>
                <div class="col-md-4">
                    <label for="tukarFaktur" class="form-label">Sudah Tukar Faktur</label>
                    <select name="tukar_faktur" id="tukarFaktur" class="form-select">
                        <option value="">-Pilih Status-</option>
                        <option value="1" {{ $sale->tukar_faktur ? 'selected' : '' }}>Ya</option>
                        <option value="0" {{ !$sale->tukar_faktur ? 'selected' : '' }}>Tidak</option>
                    </select>
                </div>
            </div>

            <div class="row mb-5">
                <div class="col-md-4">
                    <label for="dueDate" class="form-label">Tipe Diskon</label>
                    <select onchange="onChangeDiscountType(this)" name="discount_type" id="discountType"
                        class="form-select">
                        <option value="">-Pilih Tipe-</option>
                        <option value="percentage" {{ $sale->discount_type == 'percentage' ? 'selected' : ''
                            }}>Persentase</option>
                        <option value="number" {{ $sale->discount_type == 'number' ? 'selected' : ''
                            }}>Input Nilai</option>
                    </select>
                    <input type="hidden" name="discount_amount" id="discountAmountHidden"
                        value="{{ $sale->discount_amount }}">
                    <div class="input-group mt-2 percentage {{ $sale->discount_type != 'percentage' ? 'd-none' : '' }}">
                        <input id="discountAmountPercentage" min="1" type="number" class="form-control"
                            value="{{ $sale->discount_type == 'percentage' ? $sale->discount_amount : '' }}" />
                        <span class="input-group-text">%</span>
                    </div>
                    <div class="input-group mt-2 number {{ $sale->discount_type != 'number' ? 'd-none' : '' }}">
                        <input id="discountAmountNumber" min="0" max="9999999999999.99" type="number"
                            class="form-control"
                            value="{{ $sale->discount_type == 'number' ? intval($sale->discount_amount) : '' }}" />
                        <span class="input-group-text">IDR</span>
                    </div>
                </div>
                <div class="col-md-4">
                    <label for="paymentDate" class="form-label">Total Harga Akhir Setelah Diskon</label>
                    <input type="text" id="finalPrice" class="form-control"
                        value="{{ $sale->total_price_after_discount ? \App\Helpers\AppHelpers::formatToRupiah($sale->total_price_after_discount) : 0 }}"
                        disabled>
                    <input type="hidden" name="total_price_after_discount" id="finalPriceHidden" value="{{
        $sale->total_price_after_discount ?? 0 }}">
                </div>
            </div>

            {{-- Tombol Tambah Produk --}}
            <div class="my-3">
                <button id="buttonAddProduct" type="button" class="btn btn-primary mb-3" data-bs-toggle="modal"
                    data-bs-target="#modalProduk">
                    Tambah Produk
                </button>
                <br>
                <span id="warningButtonProduct" class="text-danger">Silakan pilih customer terlebih dahulu untuk
                    mengaktifkan tombol pilih
                    produk</span>
            </div>

            {{-- Tabel Produk yang Dipilih --}}
            <div class="table-responsive">
                <table class="table table-bordered" id="tabelDetail">
                    <thead>
                        <tr>
                            <th>Nama Barang</th>
                            <th>Harga</th>
                            <th>Qty</th>
                            <th>Total</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($sale->details as $detail)
                        @php
                        $product = $detail->product;
                        $maxQty = $product->stock ?? 0 - $product->min_stock ?? 0;
                        @endphp
                        <tr data-id="{{ $product->id }}">
                            <td>
                                {{ $product->product_name ?? 'Tidak diketahui' }}
                                <input type="hidden" name="products[{{ $product->id }}][product_id]"
                                    value="{{ $product->id }}">
                                <input type="hidden" name="products[{{ $product->id }}][selling_price]"
                                    value="{{ $detail->selling_price }}">
                                <input type="hidden" class="input-qty" name="products[{{ $product->id }}][quantity]"
                                    value="{{ $detail->quantity }}" max="{{ $maxQty }}">
                                <input type="hidden" class="input-total"
                                    name="products[{{ $product->id }}][total_price]" value="{{ $detail->total_price }}">
                            </td>
                            <td>Rp. {{ number_format($detail->selling_price, 0, ',', '.') }}</td>
                            <td>
                                <input type="number" class="form-control qty-input" value="{{ $detail->quantity }}"
                                    min="1" style="width: 80px;" max="{{ $maxQty }}">
                            </td>
                            <td class="total-harga">Rp. {{ number_format($detail->total_price, 0, ',', '.') }}</td>
                            <td>
                                <button onclick="" type="button" class="btn btn-danger btn-sm btn-hapus">Hapus</button>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Tombol Simpan --}}
            <div class="mt-4 d-grid">
                <button type="submit" class="btn btn-success">Simpan</button>
            </div>
        </form>
    </div>
</div>

{{-- Modal Produk dengan Pagination --}}
<div class="modal fade" id="modalProduk" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Pilih Produk</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <input type="text" id="searchProduk" class="form-control mb-3" placeholder="Cari produk...">
                <div id="tabelProdukContainer">
                    {{-- Data produk akan dimuat via AJAX --}}
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    let selectedProduct = {!! json_encode($sale->details->pluck('product_id')) !!} || [];
        const container = document.querySelector('#tabelProdukContainer');
        const buttonAddProduct = document.querySelector('#buttonAddProduct')
        const finalPriceHidden = document.querySelector('#finalPriceHidden')
        const debouncedUpdate = debounce(updateTotalHarga, 300);
        const percentageInput = document.querySelector('#discountAmountPercentage');
        const numberInput = document.querySelector('#discountAmountNumber');
        let outlet = null;
        let outletId = null;
        function loadProduk(q = '', page = 1, perPage = 5) {

            container.innerHTML = `
            <div style="display: flex; justify-content: center; align-items: center; height: 150px;">
                <div class="spinner-border" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
            </div>
            `;

            const url = new URL("{{ route('sales.getProductCustomerPrices') }}");
            url.searchParams.append('q', q);
            url.searchParams.append('page', page);
            url.searchParams.append('per_page', perPage);
            url.searchParams.append('outlet_id', outletId);

            fetch(url)
                .then(response => response.text())
                .then(html => {
                    container.innerHTML = html;


                    const btn = document.getElementById('btnPilihProduk');
                    if (btn) {

                        btn.addEventListener('click', () => {
                            const selected = [];
                            document.querySelectorAll('.produk-checkbox:checked').forEach(cb => {
                                let d = JSON.parse(cb.dataset.product)

                                selectedProduct.push(d.id)
                                selected.push({
                                    id: d.id,
                                    name: d.product_name,
                                    price: parseInt(d.selling_price),
                                    stock: parseInt(d.stock),
                                    min_stock: parseInt(d.min_stock),
                                });
                            });

                            if (selected.length === 0) {
                                alert('Pilih minimal satu produk terlebih dahulu!');
                                return;
                            }

                            const tbody = document.querySelector('#tabelDetail tbody');

                            selected.forEach(prod => {

                                const existingRow = document.querySelector(`#tabelDetail tbody tr[data-id="${prod.id}"]`);
                                if (existingRow) {
                                    const qtyInput = existingRow.querySelector('.qty-input');
                                    let qty = parseInt(qtyInput.value);
                                    qty = isNaN(qty) ? 1 : qty;
                                    qtyInput.value = qty;

                                    const totalHargaCell = existingRow.querySelector('.total-harga');
                                    const total = qty * prod.price;
                                    totalHargaCell.textContent = `Rp. ${total.toLocaleString()}`;
                                    updateTotalHarga();
                                    return; // Jangan tambahkan baris baru
                                }

                                const tr = document.createElement('tr');
                                tr.setAttribute('data-id', prod.id);
                                tr.innerHTML = `
            <td>
                ${prod.name}
                <input type="hidden" name="products[${prod.id}][product_id]" value="${prod.id}">
                <input type="hidden" name="products[${prod.id}][selling_price]" value="${prod.price}">
                <input type="hidden" class="input-qty" name="products[${prod.id}][quantity]" value="1"
                    max="${prod.stock - prod.min_stock}">
                <input type="hidden" class="input-total" name="products[${prod.id}][total_price]" value="${prod.price}">
            </td>
            <td>Rp. ${prod.price.toLocaleString()}</td>
            <td>
                <input type="number" class="form-control qty-input" value="1" min="${prod.stock - prod.min_stock == 0 ? '' : '1'}"
                    max="10" style="width: 80px;" max="${prod.stock - prod.min_stock}"
                    oninput="if(this.value.length > 10) this.value = this.value.slice(0, 10);"
                    onkeydown="return !(event.key.length === 1 && !event.key.match(/[0-9]/));">
            </td>
            <td class="total-harga">Rp. ${prod.price.toLocaleString()}</td>
            <td>
                <button type="button" class="btn btn-danger btn-sm btn-hapus">Hapus</button>
            </td>
            `;

                                tbody.appendChild(tr);
                                const inputQty = tr.querySelector('.input-qty');
                                const inputTotal = tr.querySelector('.input-total');
                                const qtyInput = tr.querySelector('.qty-input');
                                const totalHargaCell = tr.querySelector('.total-harga');
                                updateTotalHarga();
                                qtyInput.addEventListener('input', () => {
                                    let qty = parseInt(qtyInput.value);
                                    if (isNaN(qty) || qty < 1) { qty = 1; qtyInput.value = qty; } const total = qty * prod.price; totalHargaCell.textContent = `Rp.
                ${total.toLocaleString()}`; inputQty.value = qty; inputTotal.value = total; updateTotalHarga();
                                });
                                tr.querySelector('.btn-hapus').addEventListener('click', () => {
                                    const idProduk = parseInt(tr.getAttribute('data-id'));


                                    tr.remove();


                                    const index = selectedProduct.indexOf(idProduk);
                                    if (index !== -1) {
                                        selectedProduct.splice(index, 1);
                                    }
                                    onChangeDiscountType(discountType)
                                    updateTotalHarga();
                                });
                            });


                            const modalEl = document.querySelector('#modalProduk');
                            if (modalEl) {
                                const modal = bootstrap.Modal.getInstance(modalEl);
                                if (modal) modal.hide();
                            }
                        });
                    }

                    const perPageSelect = document.getElementById('perPageSelect');
                    if (perPageSelect) {
                        perPageSelect.addEventListener('change', () => {
                            const perPage = perPageSelect.value;

                            loadProduk('', 1, perPage);
                        });
                    }
                });
        }



        document.getElementById('modalProduk').addEventListener('shown.bs.modal', function () {

            loadProduk();
        });

        document.getElementById('searchProduk').addEventListener('input', function () {
            loadProduk(this.value);
        });


        document.addEventListener('click', function (e) {
            if (e.target.closest('.pagination a')) {
                e.preventDefault();
                const a = e.target.closest('a');
                const url = new URL(a.href);
                const page = url.searchParams.get('page');
                const q = document.getElementById('searchProduk').value;
                loadProduk(q, page);
            }
        });


        function toggleAll(masterCheckbox) {
            const checked = masterCheckbox.checked;
            document.querySelectorAll('.produk-checkbox').forEach(cb => {
                cb.checked = checked;
            });
        }

        document.querySelectorAll('.qty-input').forEach(input => {
            input.addEventListener('input', debouncedUpdate);
        });

        document.querySelector('#discountAmountPercentage')?.addEventListener('input',
            function () {
                let val = this.value.replace(/\D/g, '');
                // Batasi maksimal 3 digit
                if (val.length > 3) {
                    val = val.slice(0, 3);
                }

                // Batasi maksimum nilai 100
                if (parseInt(val) > 100) {
                    val = '100';
                }

                this.value = val;
                debouncedUpdate();
            }
        );
        document.querySelector('#discountAmountNumber')?.addEventListener('input', function () {
            const totalHarga = parseFloat(document.getElementById('totalPriceHidden')?.value) || 0;
            let val = parseFloat(this.value) || 0;

            if (val > totalHarga) {
                this.value = totalHarga;
                val = totalHarga;
            }

            debouncedUpdate(); // tetap update final harga
        });

        function updateTotalHarga() {
            let total = 0;

            document.querySelectorAll('#tabelDetail tbody tr').forEach(tr => {
                const qty = parseInt(tr.querySelector('.qty-input')?.value) || 0;
                const hargaText = tr.querySelector('td:nth-child(2)')?.textContent?.replace(/[^\d]/g, '') || '0';
                const harga = parseInt(hargaText) || 0;

                total += qty * harga;
            });

            // Ambil diskon
            const discountType = document.getElementById('discountType')?.value;
            let discountAmount = 0;

            // Ambil input diskon sesuai tipe yang sedang ditampilkan (tidak .d-none)
            if (discountType === 'percentage') {
                const input = percentageInput;
                if (input && !document.querySelector('.percentage').classList.contains('d-none')) {
                    discountAmount = parseFloat(input.value) || 0;
                }
            } else if (discountType === 'number') {
                const input = numberInput;
                if (input && !document.querySelector('.number').classList.contains('d-none')) {
                    discountAmount = parseFloat(input.value) || 0;
                }
            }

            // Hitung total setelah diskon
            let totalAfterDiscount = total;
            if (discountType === 'percentage') {
                totalAfterDiscount = total - (total * discountAmount / 100);
            } else if (discountType === 'number') {
                totalAfterDiscount = total - discountAmount;
            }

            totalAfterDiscount = Math.max(totalAfterDiscount, 0);

            document.getElementById('discountAmountHidden').value = discountAmount;

            // Update UI
            document.getElementById('totalPrice').value = `Rp. ${total.toLocaleString()}`;
            document.getElementById('totalPriceHidden').value = total;

            document.getElementById('finalPrice').value = `Rp. ${Math.round(totalAfterDiscount).toLocaleString()}`;
            document.getElementById('finalPriceHidden').value = Math.round(totalAfterDiscount);
        }

        function onChangeCustomer(elem) {
            const selectedOption = elem.options[elem.selectedIndex];
            const outletDataJson = selectedOption.getAttribute('data-outlet');
            if (!selectedOption.value) {
                buttonAddProduct.disabled = true
            } else {

                if (outletDataJson) {
                    outlet = JSON.parse(outletDataJson);
                    outletId = outlet.id
                    buttonAddProduct.disabled = false
                }
            }
        }

        document.querySelector('#tabelDetail tbody').addEventListener('click', function (e) {
            if (e.target.classList.contains('btn-hapus')) {
                const row = e.target.closest('tr');
                if (row) {
                    row.remove();
                    updateTotalHarga();
                }
            }
        });

        function onChangeDiscountType(el) {
            if (selectedProduct.length <= 0) {
                Toast.fire({ icon: 'error', title: 'Silahkan tambah produk terlebih dahulu' });
                el.value = ''; return;
            } const selected = el.value;
            const percentageEl = document.querySelector('.percentage');
            const numberEl = document.querySelector('.number');
            percentageEl.classList.add('d-none');
            numberEl.classList.add('d-none');
            percentageInput.value = '';
            numberInput.value = ''; // Tampilkan yang sesuai 
            if (selected === 'percentage') {
                percentageEl.classList.remove('d-none');
                percentageInput.disabled = false;
            } else if (selected === 'number') {
                numberEl.classList.remove('d-none');
                numberInput.disabled = false;
            }

            percentageInput.value = '';
            numberInput.value = '';

            // Reset final price ke total awal const
            total = parseInt(document.getElementById('totalPriceHidden').value) || 0;
            document.getElementById('finalPrice').value = `Rp. ${total.toLocaleString()}`;
            document.getElementById('finalPriceHidden').value = total;
        }

        function changePerPageModalCustomerPrice() {
            const perPage = document.getElementById('perPageSelect').value;
            const search = document.getElementById('searchProduk')?.value || '';
            const outletId = document.getElementById('outletId')?.value;

            let url = `{{ route('sales.getProductCustomerPrices') }}?q=${encodeURIComponent(search)}&per_page=${perPage}&outlet_id=${outletId}`;

            // Fetch data baru via AJAX
            fetch(url, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            }).then(res => res.text())
                .then(html => {
                    document.getElementById('tabelProdukContainer').innerHTML = html;
                });
        }

</script>
@endsection