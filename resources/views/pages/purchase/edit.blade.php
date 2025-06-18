@extends('layout.index')
@section('content')
@php
$helpers = \App\Helpers\AppHelpers::class;
@endphp
<div class="card">
    <div class="card-body py-4">
        <form id="purchaseForm" action="{{ route('purchases.update', $purchase->id) }}" method="POST">
            @csrf
            @method("PUT")
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3 row">
                        <label class="col-sm-4 col-form-label">Supplier</label>
                        <div class="col-sm-8">
                            <select onchange="onChangeSupplier(this)" name="supplier_id" id="supplierId"
                                class="form-select" required>
                                <option value="">-Pilih Supplier- </option>
                                @foreach ($suppliers as $s)
                                <option {{ old('supplier_id', $purchase->supplier_id) == $s->id ? 'selected' : '' }}
                                    data-supplier="{{
                                    json_encode($s) }}" value="{{ $s->id }}">{{ $s->supplier_name
                                    }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="mb-3 row">
                        <label class="col-sm-4 col-form-label">Tanggal Pemesanan</label>
                        <div class="col-sm-8">
                            <input value="{{ old('order_date', $purchase->order_date) }}" type="date" name="order_date"
                                class="form-control">
                        </div>
                    </div>
                    <div class="mb-3 row">
                        <label class="col-sm-4 col-form-label">Total Harga</label>
                        <div class="col-sm-8">
                            <input value="{{ old('total_price', $helpers::formatToRupiah($purchase->total_price)) }}"
                                type="text" id="totalPrice" class="form-control" disabled>
                            <input value="{{ old('total_price', $helpers::formatToRupiah($purchase->total_price)) }}"
                                type="hidden" name="total_price" id="totalPriceHidden">
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="mb-3 row">
                        <label class="col-sm-4 col-form-label">Tanggal Jatuh Tempo</label>
                        <div class="col-sm-8">

                            <input value="{{ old('due_date', $purchase->due_date) }}" type="date" name="due_date"
                                class="form-control">

                        </div>
                    </div>
                    <div class="mb-3 row">
                        <label class="col-sm-4 col-form-label">Tanggal Bayar</label>
                        <div class="col-sm-8">
                            <input value="{{ old('payment_date', $purchase->payment_date) }}" type="date"
                                name="payment_date" class="form-control">
                        </div>
                    </div>
                    <div class="mb-3 row d-flex align-items-center">
                        <label class="col-sm-4 col-form-label">Sudah Bayar</label>
                        <div class="col-sm-8">
                            <input {{ $purchase->is_paid ? 'checked' : '' }} value="{{ old('is_paid', 1) }}"
                            type="checkbox" id="isPaid" name="is_paid">
                        </div>
                    </div>
                </div>
            </div>
            <hr>
            <div class="my-3">
                <button type="button" id="addProduct" class="btn btn-primary mb-3" {{ $purchase->supplier_id != '' ? ''
                    : 'disabled' }}>Tambah Produk</button>
                <br>
                <span id="warningButtonProduct" class="text-danger">Silakan pilih supplier terlebih dahulu untuk
                    mengaktifkan tombol pilih
                    produk</span>
            </div>
            <div class="table-responsive">
                <table class="table table-lg table-stripped" id="tabelDetail">
                    <thead>
                        <tr>
                            <th>SKU</th>
                            <th>Nama Barang</th>
                            <th>Harga Beli</th>
                            <th>Harga Jual</th>
                            <th>Quantity</th>
                            <th>Harga Total</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="tabelProdukBody">
                        @foreach ($purchase->purchaseDetails as $key => $pp)
                        <tr>
                            <td>
                                <input type="hidden" name="products[{{ $key }}][id]" value="{{ $pp->product?->id }}">
                                <input type="hidden" name="products[{{ $key }}][sku]"
                                    value="{{ $pp->product?->product_id }}">
                                <input type="text" class="form-control input-sku" maxlength="20" placeholder="SKU"
                                    value="{{ $pp->product?->product_id }}" disabled>

                            </td>
                            <td>
                                <input type="text" class="form-control" name="products[{{ $key }}][name]"
                                    placeholder="Nama Barang" maxlength="30" value="{{ $pp->product?->product_name }}"
                                    required>
                            </td>
                            <td>
                                <input type="number" class="form-control harga-beli"
                                    name="products[{{ $key }}][buy_price]" placeholder="Harga Beli" min="0"
                                    value="{{ intval($pp->product?->base_price) }}" required>
                            </td>
                            <td>
                                <input type="number" class="form-control harga-jual"
                                    name="products[{{ $key }}][sell_price]" placeholder="Harga Jual" min="0"
                                    value="{{ intval($pp->product?->selling_price) }}" required>
                            </td>
                            <td>
                                <input type="number" class="form-control qty" name="products[{{ $key }}][quantity]"
                                    placeholder="Qty" value="{{ intval($pp->quantity) }}" min="1" required>
                            </td>
                            <td class="total-harga">{{ $helpers::formatToRupiah($pp->total_price) }}</td>
                            <input type="hidden" name="products[{{ $key }}][total_price]" class="total-price-hidden"
                                value="{{ intval($pp->total_price) }}">
                            <td>
                                <button type="button" class="btn btn-danger btn-sm btn-hapus">Hapus</button>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="mt-4 d-grid">
                <button type="submit" class="btn btn-success">Simpan</button>
            </div>
        </form>
    </div>
</div>

<script>
    let selectedProduct = [];
        const container = document.querySelector('#tabelProdukContainer');
        const addProduct = document.querySelector('#addProduct')
        const tbody = document.getElementById('tabelProdukBody');
        const form = document.getElementById('purchaseForm');

        document.querySelectorAll('#tabelProdukBody tr').forEach(tr => {
            initializeProductRow(tr);
        });


        form.addEventListener('submit', function (e) {
            const barisProduk = document.querySelectorAll('#tabelProdukBody tr');
            if (barisProduk.length === 0) {
                e.preventDefault();
                Toast.fire({ icon: 'error', title: 'Minimal 1 produk pembelian' });
            }
        });

        document.getElementById('isPaid').addEventListener('change', function () {
            this.value = this.checked ? '1' : '0';
        });

        addProduct.addEventListener('click', () => {
            const tr = document.createElement('tr');
            let index = document.querySelectorAll('#tabelProdukBody tr').length;
            tr.innerHTML = `
                            <td>
                                <input type="text" class="form-control input-sku" name="products[${index}][sku]" maxlength="20" placeholder="SKU" required>
                            </td>
                            <td>
                                <input type="text" class="form-control" name="products[${index}][name]" placeholder="Nama Barang" maxlength="30" required>
                            </td>
                            <td>
                                <input type="number" class="form-control harga-beli" name="products[${index}][buy_price]" placeholder="Harga Beli" min="0" required>
                            </td>
                            <td>
                                <input type="number" class="form-control harga-jual" name="products[${index}][sell_price]" placeholder="Harga Jual" min="0" required>
                            </td>
                            <td>
                                <input type="number" class="form-control qty" name="products[${index}][quantity]" placeholder="Qty" value="1" min="1" required>
                            </td>
                            <td class="total-harga">Rp. 0</td>
                            <input type="hidden" name="products[${index}][total_price]" class="total-price-hidden" value="0">
                            <td>
                                <button type="button" class="btn btn-danger btn-sm btn-hapus">Hapus</button>
                            </td>
                            `;
            tbody.appendChild(tr);

            initializeProductRow(tr);

        });

        function updateTotalKeseluruhan() {
            let total = 0;
            document.querySelectorAll('.total-price-hidden').forEach(input => {
                total += parseInt(input.value) || 0;
            });

            // Simpan ke input hidden dan tampilkan ke user
            const totalInput = document.getElementById('totalPriceHidden');
            const totalText = document.getElementById('totalPrice');

            if (totalInput) totalInput.value = total;
            if (totalText) totalText.value = `Rp. ${total.toLocaleString()}`;
        }

        function onChangeSupplier(el) {
            if (el.value != "") {
                addProduct.disabled = false
            } else {
                addProduct.disabled = true

            }
        }


        document.querySelectorAll('.btn-hapus').forEach(btn => {
            btn.addEventListener('click', function () {
                const tr = this.closest('tr');
                tr.remove();
                updateTotalKeseluruhan();
            });
        });

        function initializeProductRow(tr) {
            const hargaBeliInput = tr.querySelector('.harga-beli');
            const hargaJualInput = tr.querySelector('.harga-jual');
            const qtyInput = tr.querySelector('.qty');
            const totalCell = tr.querySelector('.total-harga');

            function updateTotal() {
                const harga = parseInt(hargaBeliInput.value) || 0;
                const qty = parseInt(qtyInput.value) || 1;
                const total = harga * qty;
                totalCell.textContent = `Rp. ${total.toLocaleString()}`;

                const hiddenInput = tr.querySelector('.total-price-hidden');
                if (hiddenInput) hiddenInput.value = total;

                updateTotalKeseluruhan(); // pastikan fungsi ini ada
            }

            [hargaBeliInput, hargaJualInput, qtyInput].forEach(input => {
                input.addEventListener('input', function () {
                    this.value = this.value.replace(/\D/g, '').slice(0, 10);
                    updateTotal();
                });

                input.addEventListener('paste', function (e) {
                    const paste = (e.clipboardData || window.clipboardData).getData('text');
                    if ((this.value + paste).replace(/\D/g, '').length > 10) {
                        e.preventDefault();
                    }
                });
            });

            const skuInput = tr.querySelector('.input-sku');
            if (skuInput) {
                skuInput.addEventListener('input', function () {
                    this.value = this.value.slice(0, 20);
                });
            }

            // Tombol hapus baris
            const btnHapus = tr.querySelector('.btn-hapus');
            if (btnHapus) {
                btnHapus.addEventListener('click', () => {
                    tr.remove();
                    updateTotalKeseluruhan();
                });
            }

            // Hitung total awal (kalau datanya sudah ada di input)
            updateTotal();
        }


        // document.querySelectorAll('input[type="number"]').forEach(input => {
        //     input.addEventListener('input', function () {
        //         // Hanya izinkan maksimal 10 digit angka
        //         if (this.value.length > 10) {
        //             this.value = this.value.slice(0, 10);
        //         }
        //     });

        //     // Cegah paste lebih dari 10 digit
        //     input.addEventListener('paste', function (e) {
        //         const paste = (e.clipboardData || window.clipboardData).getData('text');
        //         if ((this.value + paste).length > 10) {
        //             e.preventDefault();
        //         }
        //     });
        // });


        // document.getElementById('modalProduk').addEventListener('shown.bs.modal', function () {

        //     loadProduk();

        // });

        // function loadProduk(q = '', page = 1) {

        //     container.innerHTML = `
        //                 <div style="display: flex; justify-content: center; align-items: center; height: 150px;">
        //                     <div class="spinner-border" role="status">
        //                         <span class="visually-hidden">Loading...</span>
        //                     </div>
        //                 </div>
        //                 `;

        //     const url = new URL("{{ route('purchases.getProduct') }}");
        //     url.searchParams.append('q', q);
        //     url.searchParams.append('page', page);

        //     fetch(url)
        //         .then(response => response.text())
        //         .then(html => {
        //             container.innerHTML = html;

        //             const btn = document.getElementById('btnPilihProduk');
        //             if (btn) {
        //                 btn.addEventListener('click', () => {
        //                     const selected = [];
        //                     document.querySelectorAll('.produk-checkbox:checked').forEach(cb => {
        //                         let d = JSON.parse(cb.dataset.product);
        //                         if (!selectedProduct.includes(d.id)) {
        //                             selectedProduct.push(d.id);
        //                         }

        //                         selected.push({
        //                             id: d.id,
        //                             name: d.product_name,
        //                             price: parseInt(d.selling_price),
        //                             buy_price: parseInt(d.buy_price),
        //                             stock: parseInt(d.stock),
        //                             min_stock: parseInt(d.min_stock),
        //                         });
        //                     });

        //                     if (selected.length === 0) {
        //                         alert('Pilih minimal satu produk terlebih dahulu!');
        //                         return;
        //                     }

        //                     const tbody = document.querySelector('#tabelDetail tbody');

        //                     selected.forEach(prod => {
        //                         const existingRow = document.querySelector(`#tabelDetail tbody tr[data-id="${prod.id}"]`);
        //                         if (existingRow) {
        //                             const qtyInput = existingRow.querySelector('.qty-input');
        //                             let qty = parseInt(qtyInput.value);
        //                             qty = isNaN(qty) ? 1 : qty + 1;
        //                             qtyInput.value = qty;

        //                             const buyPriceInput = existingRow.querySelector('.harga-beli-input');
        //                             const totalHargaCell = existingRow.querySelector('.total-harga');
        //                             const total = qty * parseInt(buyPriceInput.value);
        //                             totalHargaCell.textContent = `Rp. ${total.toLocaleString()}`;

        //                             const inputTotal = existingRow.querySelector('.input-total');
        //                             inputTotal.value = total;

        //                             updateTotalHarga();
        //                             return;
        //                         }

        //                         const tr = document.createElement('tr');
        //                         tr.setAttribute('data-id', prod.id);
        //                         tr.innerHTML = `
        //                     <td>
        //                         ${prod.name}
        //                         <input type="hidden" name="products[${prod.id}][product_id]" value="${prod.id}">
        //                         <input type="hidden" class="input-total" name="products[${prod.id}][total_price]" value="">
        //                     </td>
        //                     <td>
        //                         <input type="number" class="form-control harga-beli-input" name="products[${prod.id}][buy_price]"
        //                             value="" min="0" style="width: 200px;">
        //                     </td>
        //                     <td>
        //                         <input type="number" class="form-control harga-jual-input" name="products[${prod.id}][selling_price]"
        //                             value="" min="0" style="width: 200px;">
        //                     </td>
        //                     <td>
        //                         <input type="number" class="form-control qty-input" name="products[${prod.id}][quantity]" value="1" min="1"
        //                             max="10" style="width: 100px;">
        //                     </td>
        //                     <td class="total-harga">Rp. 0</td>
        //                     <td>
        //                         <button type="button" class="btn btn-danger btn-sm btn-hapus">Hapus</button>
        //                     </td>
        //                     `;

        //                         tbody.appendChild(tr);

        //                         tr.querySelectorAll('input[type="number"]').forEach(input => {
        //                             input.addEventListener('input', function () {
        //                                 if (this.value.length > 10) {
        //                                     this.value = this.value.slice(0, 10);
        //                                 }
        //                             });

        //                             input.addEventListener('paste', function (e) {
        //                                 const paste = (e.clipboardData || window.clipboardData).getData('text');
        //                                 if ((this.value + paste).length > 10) {
        //                                     e.preventDefault();
        //                                 }
        //                             });
        //                         });

        //                         const qtyInput = tr.querySelector('.qty-input');
        //                         const buyPriceInput = tr.querySelector('.harga-beli-input');
        //                         const totalHargaCell = tr.querySelector('.total-harga');
        //                         const inputTotal = tr.querySelector('.input-total');

        //                         function updateRowTotal() {
        //                             const qty = parseInt(qtyInput.value) || 0;
        //                             const buyPrice = parseInt(buyPriceInput.value) || 0;
        //                             const total = qty * buyPrice;

        //                             totalHargaCell.textContent = `Rp. ${total.toLocaleString()}`;
        //                             inputTotal.value = total;
        //                             updateTotalHarga();
        //                         }

        //                         // Event listener perubahan qty
        //                         qtyInput.addEventListener('input', () => {
        //                             let qty = parseInt(qtyInput.value);
        //                             if (isNaN(qty) || qty < 1) { qty = 1; qtyInput.value = qty; } updateRowTotal();
        //                         }); // Event listener perubahan harga beli
        //                         buyPriceInput.addEventListener('input', () => {
        //                             updateRowTotal();
        //                         });

        //                         // Tombol hapus
        //                         tr.querySelector('.btn-hapus').addEventListener('click', () => {
        //                             const idProduk = parseInt(tr.getAttribute('data-id'));
        //                             tr.remove();

        //                             const index = selectedProduct.indexOf(idProduk);
        //                             if (index !== -1) {
        //                                 selectedProduct.splice(index, 1);
        //                             }

        //                             updateTotalHarga();
        //                         });
        //                     });

        //                     // Tutup modal
        //                     const modalEl = document.querySelector('#modalPilihProduk');
        //                     if (modalEl) {
        //                         const modal = bootstrap.Modal.getInstance(modalEl);
        //                         if (modal) modal.hide();
        //                     }
        //                 });
        //             }
        //         });
        // }

        // function toggleAll(masterCheckbox) {
        //     const checked = masterCheckbox.checked;
        //     document.querySelectorAll('.produk-checkbox').forEach(cb => {
        //         cb.checked = checked;
        //     });
        // }

        // function updateTotalHarga() {
        //     let total = 0;

        //     document.querySelectorAll('#tabelDetail tbody tr').forEach(tr => {
        //         const qty = parseInt(tr.querySelector('.qty-input')?.value) || 0;
        //         const harga = parseInt(tr.querySelector('.harga-beli-input')?.value) || 0;

        //         total += qty * harga;
        //     });

        //     document.getElementById('totalPrice').value = `Rp. ${total.toLocaleString()}`;
        //     document.getElementById('totalPriceHidden').value = total;

        // }
</script>
@endsection