async function apiRequest(url, method = 'GET', data = null, headers = {}) {
  const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

  const config = {
    method,
    headers: {
      'Content-Type': 'application/json',
      'X-CSRF-TOKEN': token,
      ...headers
    },
  };

  if (data && method !== 'GET') {
    config.body = JSON.stringify(data);
  }

  try {
    const response = await fetch(url, config);
    const result = await response.json();
    if (!response.ok) throw result;
    return result;
  } catch (error) {
    throw error;
  }
}

function debounce(func, delay = 900) {
  let timeoutId;
  return function (...args) {
    if (timeoutId) clearTimeout(timeoutId);
    timeoutId = setTimeout(() => {
      func.apply(this, args);
    }, delay);
  };
}

function showLoading() {
  if (document.getElementById('loadingOverlay')) return;

  const overlay = document.createElement('div');
  overlay.id = 'loadingOverlay';
  overlay.className = 'position-fixed top-0 start-0 w-100 h-100 d-flex justify-content-center align-items-center';
  overlay.style.backgroundColor = 'rgba(0, 0, 0, 0.5)';
  overlay.style.zIndex = '1055';

  overlay.innerHTML = `
        <div class="text-center">
            <div class="spinner-border text-light" role="status" style="width: 3rem; height: 3rem;">
                <span class="visually-hidden">Loading...</span>
            </div>
            <div class="mt-2 text-white fw-bold">Memuat data...</div>
        </div>
    `;

  document.body.appendChild(overlay);
}

function loadingButton(element) {
  element.disabled = true;
  element.setAttribute('data-original-text', element.innerHTML);
  element.innerHTML = `
                <div class="spinner-border text-light" role="status" style="width: 1rem; height: 1rem;">
                    <span class="visually-hidden">Loading...</span>
                </div>
            `;
}

function resetButton(element) {
  const original = element.getAttribute('data-original-text');
  if (original) {
    element.innerHTML = original;
    element.disabled = false;
    element.removeAttribute('data-original-text');
  }
}

function updatePaginationCounter({ totalItemId = 'totalItem', toItemId = 'toItem', increment = 1 }) {
  const totalItemSpan = document.getElementById(totalItemId);
  const toItemSpan = document.getElementById(toItemId);

  if (totalItemSpan && toItemSpan) {
    const currentTotal = parseInt(totalItemSpan.textContent) || 0;
    const currentTo = parseInt(toItemSpan.textContent) || 0;

    totalItemSpan.textContent = currentTotal + increment;
    toItemSpan.textContent = currentTo + increment;
  }
}

function getQueryParam(param) {
  const urlParams = new URLSearchParams(window.location.search);
  return urlParams.get(param);
}


function hideLoading() {
  const overlay = document.getElementById('loadingOverlay');
  if (overlay) {
    overlay.remove();
  }
}

async function confirmDelete(url, rowId = null) {
  const result = await Swal.fire({
    title: 'Apakah Anda yakin?',
    text: "Aksi anda tidak dapat dibatalkan",
    icon: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#d33',
    cancelButtonColor: '#6c757d',
    confirmButtonText: 'Ya, hapus!',
    cancelButtonText: 'Batal'
  });

  if (result.isConfirmed) {
    try {
      showLoading()
      const res = await apiRequest(url, 'DELETE');

      if (res.code === 200) {
        hideLoading()
        Toast.fire({ icon: 'success', title: res.message });

        if (rowId) {
          const row = document.getElementById(rowId);
          if (row) row.remove();
        }

        return true;
      } else {
        hideLoading()
        Toast.fire({ icon: 'error', title: res.message });
        return false;
      }



    } catch (err) {
      hideLoading()
      Toast.fire({ icon: 'error', title: err.message || 'Gagal menghapus data.' });
      return false;
    }
  }

  return false; // user cancel
}

const formatRupiah = (angka) => {
  const number_string = angka.toString();
  const sisa = number_string.length % 3;
  let rupiah = number_string.substr(0, sisa);
  const ribuan = number_string.substr(sisa).match(/\d{3}/g);
  if (ribuan) {
    rupiah += (sisa ? '.' : '') + ribuan.join('.');
  }
  return 'Rp. ' + rupiah;
};

function unformatRupiah(rupiah) {
  return rupiah.replace(/[^\d]/g, '');
}


const handleRupiahFormat = (inputElement) => {
  inputElement.addEventListener('input', function () {
    let value = this.value.replace(/[^\d]/g, '');
    this.value = value ? formatRupiah(value) : '';
  });
};

function allowOnlyNumbers(inputId) {
  const input = document.getElementById(inputId);
  if (!input) return;

  input.addEventListener('input', function () {
    let value = this.value.replace(/[^\d]/g, '');

    if (value.length > 1) {
      value = value.replace(/^0+/, '');
    }

    this.value = value;
  });

  input.addEventListener('paste', function (e) {
    const pasted = (e.clipboardData || window.clipboardData).getData('text');
    if (!/^\d+$/.test(pasted)) {
      e.preventDefault();
    }
  });
}

function restrictToNumericInput(element) {
  element.addEventListener('input', function () {
    this.value = this.value.replace(/[^0-9]/g, '');
  });
}

function closeCustomModal() {
  const modalElement = document.getElementById('modalCustomPriceGroup');

  // Periksa apakah modal sedang terbuka
  let modalInstance = bootstrap.Modal.getInstance(modalElement);

  if (!modalInstance) {
    modalInstance = new bootstrap.Modal(modalElement);
  }

  modalInstance.hide();
}



