/**
 * =====================
 * UI Helper Functions (preview + toggle password)
 * Author: Festara for Hanzel
 * =====================
 */

/**
 * Preview selected image file before upload.
 *
 * @param {string} inputSelector - Selector for the input[type="file"]
 * @param {string} imgSelector - Selector for the <img> element to display preview
 *
 * Example:
 * previewImage('#gambarProfile', '#imgPreviewProfile');
 */
function previewImage(inputSelector, imgSelector) {
  const allowedTypes = ["image/jpeg", "image/png"];

  $(document).on("change", inputSelector, function () {
    const file = this.files[0];
    const $img = $(imgSelector);

    if (file) {
      if (!allowedTypes.includes(file.type)) {
        Swal.fire({
          icon: "error",
          title: "Format Tidak Didukung",
          text: "Hanya file gambar JPEG dan PNG yang diizinkan.",
        });
        $(this).val("");
        $img.attr("src", "").hide();
        return;
      }

      const reader = new FileReader();
      reader.onload = function (e) {
        $img.attr("src", e.target.result).css("display", "block");
      };
      reader.readAsDataURL(file);
    }
  });
}

/**
 * Toggle password visibility for any input with a related toggle-password trigger
 *
 * HTML Example:
 * <input type="password" id="password">
 * <span class="input-group-text toggle-password" data-target="#password" style="cursor:pointer;">
 *   <i class="fas fa-eye"></i>
 * </span>
 */
$(document).on("click", ".toggle-password", function () {
  const targetSelector = $(this).data("target");
  const $input = $(targetSelector);
  const $icon = $(this).find("i");

  if ($input.attr("type") === "password") {
    $input.attr("type", "text");
    $icon.removeClass("fa-eye").addClass("fa-eye-slash");
  } else {
    $input.attr("type", "password");
    $icon.removeClass("fa-eye-slash").addClass("fa-eye");
  }
});

/**
 * confirmAction - Helper konfirmasi universal menggunakan SweetAlert2
 *
 * Fungsi ini memunculkan popup konfirmasi yang dapat digunakan untuk logout, delete, dan lainnya.
 *
 * -------------------------------
 * Contoh Penggunaan (Form Submit):
 *
 * document.getElementById('logoutBtn').addEventListener('click', function () {
 *     confirmAction({
 *         title: 'Yakin ingin logout?',
 *         text: 'Sesi Anda akan diakhiri.',
 *         icon: 'warning',
 *         confirmButtonText: 'Ya, logout',
 *         cancelButtonText: 'Batal',
 *         formId: 'logoutForm'
 *     });
 * });
 *
 * -------------------------------
 * Contoh Penggunaan (Callback/AJAX/Delete):
 *
 * $('.deleteBtn').on('click', function () {
 *     const id = $(this).data('id');
 *     confirmAction({
 *         title: 'Hapus Data?',
 *         text: 'Data akan dihapus permanen.',
 *         icon: 'warning',
 *         confirmButtonText: 'Ya, hapus',
 *         cancelButtonText: 'Batal',
 *         callback: function () {
 *             window.location.href = 'delete.php?id=' + id;
 *         }
 *     });
 * });
 *
 * @param {Object} options - Opsi konfigurasi konfirmasi
 * @param {string} options.title - Judul konfirmasi
 * @param {string} options.text - Pesan tambahan
 * @param {string} options.icon - Ikon Swal ('warning', 'success', 'error', 'info', 'question')
 * @param {string} options.confirmButtonText - Teks tombol konfirmasi
 * @param {string} options.cancelButtonText - Teks tombol batal
 * @param {string|null} options.formId - ID form yang akan dikirim jika dikonfirmasi
 * @param {function|null} options.callback - Fungsi callback yang dipanggil jika dikonfirmasi
 */

function confirmAction({
  title = "Apakah kamu yakin?",
  text = "",
  icon = "warning",
  confirmButtonText = "Ya",
  cancelButtonText = "Batal",
  formId = null,
  callback = null,
}) {
  Swal.fire({
    title: title,
    text: text,
    icon: icon,
    showCancelButton: true,
    confirmButtonText: confirmButtonText,
    cancelButtonText: cancelButtonText,
    reverseButtons: true,
  }).then((result) => {
    if (result.isConfirmed) {
      if (formId) {
        document.getElementById(formId).submit();
      } else if (typeof callback === "function") {
        callback();
      }
    }
  });
}
/**
 * renderExternalFilters
 * ---------------------
 * Membuat dropdown filter eksternal berdasarkan kolom DataTable tertentu.
 *
 * @param {string} tableSelector - Selector tabel DataTable (misal: '#myTable')
 * @param {string} containerSelector - Selector container filter (misal: '.filterArea')
 * @param {array} columnIndexes - Index kolom yang ingin difilter (misal: [1, 2, 4])
 */
function renderExternalFilters(
  tableSelector,
  containerSelector,
  columnIndexes = []
) {
  const $table = $(tableSelector);
  const $container = $(containerSelector);

  if (!$.fn.DataTable.isDataTable($table))
    return console.warn("Table belum terinisialisasi.");

  const table = $table.DataTable();
  $container.empty();

  // ✅ Inject CSS jika belum ada
  if (!document.getElementById("external-filter-style")) {
    const css = `
            .filterArea {
                display: flex;
                flex-wrap: wrap;
                gap: 1rem;
                margin-bottom: 1rem;
                align-items: flex-end;
            }
            .filterArea .filter-item {
                min-width: 160px;
                max-width: 220px;
                flex: 1 1 auto;
                display: flex;
                flex-direction: column;
            }
            .filterArea label {
                font-size: 0.85rem;
                font-weight: 600;
                margin-bottom: 0.25rem;
                white-space: nowrap;
            }
            .filterArea select {
                font-size: 0.85rem;
                padding: 0.25rem 0.5rem;
                white-space: normal !important;
                word-break: break-word;
                display: inline-block;
                width: 100%;
                max-width: 100%;
            }
            .filterArea .filter-reset {
                margin-left: auto;
                margin-top: auto;
            }
        `;
    $("<style>", { id: "external-filter-style", html: css }).appendTo("head");
  }

  // 🔁 Ambil header kolom
  const headers = $table
    .find("thead th")
    .map((_, th) => $(th).text().trim())
    .get();

  // 🔁 Render tiap dropdown filter
  columnIndexes.forEach((index) => {
    const column = table.column(index);
    const title = headers[index] || `Kolom ${index}`;
    const current = column.search() || "";

    const values = column
      .data({ search: "applied" })
      .unique()
      .sort()
      .toArray()
      .filter((v) => v && v.trim());

    const wrapper = $('<div class="filter-item"></div>');
    const label = $("<label>").text(title);
    const select = $(
      `<select class="form-control form-control-sm select2"><option value="">All</option></select>`
    );

    values.forEach((val) => {
      const escaped = val.replace(/[.*+?^${}()|[\]\\]/g, "\\$&");
      const isSelected = current === `^${escaped}$`;

      $("<option>", {
        value: val,
        text: val,
        title: val,
        selected: isSelected,
      }).appendTo(select);
    });

    // On Change → filter with regex
    select.on("change", function () {
      const val = $(this).val();
      const escaped = val.replace(/[.*+?^${}()|[\]\\]/g, "\\$&");
      column.search(val ? `^${escaped}$` : "", true, false).draw();
    });

    wrapper.append(label, select);
    $container.append(wrapper);
  });

  // ✅ Init Select2 hanya sekali
  if ($.fn.select2) {
    $container.find("select.select2").select2({
      theme: "bootstrap4", // or bootstrap-5 if needed
      width: "100%",
      dropdownAutoWidth: true,
      placeholder: "Pilih filter",
      allowClear: true,
    });
  }

  // ✅ Tombol reset filter
  const resetBtn = $(`
        <div class="filter-reset">
            <button type="button" class="btn btn-sm btn-outline-danger" id="resetFilters">
                <i class="fas fa-times-circle"></i> Reset Filter
            </button>
        </div>
    `);
  $container.append(resetBtn);

  // ✅ Reset semua filter
  $(document)
    .off("click", "#resetFilters")
    .on("click", "#resetFilters", function () {
      table.columns().search("");
      table.search("").draw();
    });
}
