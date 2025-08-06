/**
 * ======================================================
 * 🌟 UI Helper Library - Festara for Hanzel
 * ------------------------------------------------------
 * Kumpulan fungsi pendukung antarmuka: preview gambar,
 * toggle password, konfirmasi SweetAlert, filter DataTable,
 * validasi tanda bintang required, dan kloning form dinamis.
 * ======================================================
 */

/**
 * 📷 previewImage
 * Preview selected image before upload.
 *
 * @param {string} inputSelector - input[type="file"] selector
 * @param {string} imgSelector - target <img> to show preview
 *
 * 💡 Example:
 * previewImage('#inputGambar', '#imgPreview');
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
      reader.onload = (e) =>
        $img
          .attr("src", e.target.result)
          .css({ display: "block", margin: "0 auto" });
      reader.readAsDataURL(file);
    }
  });
}

/**
 * 👁 toggle-password
 * Toggle visibility of password input.
 *
 * HTML:
 * <input type="password" id="myPass">
 * <span class="toggle-password" data-target="#myPass"><i class="fas fa-eye"></i></span>
 */
$(document).on("click", ".toggle-password", function () {
  const $input = $($(this).data("target"));
  const $icon = $(this).find("i");
  const isPassword = $input.attr("type") === "password";

  $input.attr("type", isPassword ? "text" : "password");
  $icon.toggleClass("fa-eye fa-eye-slash");
});

/**
 * 🛡 confirmAction
 * Konfirmasi universal menggunakan SweetAlert2.
 *
 * @param {Object} options - Konfigurasi popup
 * @param {string} options.title
 * @param {string} options.text
 * @param {string} options.icon - warning/success/error/info
 * @param {string} options.confirmButtonText
 * @param {string} options.cancelButtonText
 * @param {string|null} options.formId - Kirim form saat confirm
 * @param {function|null} options.callback - Callback jika confirm
 *
 * 📌 Example:
 * confirmAction({
 *   title: 'Hapus?', text: 'Data ini akan dihapus.', icon: 'warning',
 *   confirmButtonText: 'Ya', cancelButtonText: 'Batal',
 *   callback: () => deleteData()
 * });
 */
function confirmAction({
  title = "Yakin?",
  text = "",
  icon = "warning",
  confirmButtonText = "Ya",
  cancelButtonText = "Batal",
  formId = null,
  callback = null,
}) {
  Swal.fire({
    title,
    text,
    icon,
    showCancelButton: true,
    confirmButtonText,
    cancelButtonText,
    reverseButtons: true,
  }).then((result) => {
    if (result.isConfirmed) {
      if (formId) document.getElementById(formId)?.submit();
      else if (typeof callback === "function") callback();
    }
  });
}

/**
 * 🧼 escapeRegex
 * Escape string agar aman untuk pencarian regex.
 */
function escapeRegex(text) {
  return text.replace(/[.*+?^${}()|[\]\\]/g, "\\$&");
}

/**
 * 📊 renderExternalFiltersWithGetters
 * Buat dropdown filter di luar tabel & ambil data aktif.
 *
 * @param {string} tableSelector - Tabel target (DataTable)
 * @param {string} containerSelector - Container filter
 * @param {array} columnIndexes - Index kolom yang difilter
 * @returns {Function} getActiveFilters - Ambil filter aktif {label: value}
 */
function renderExternalFiltersWithGetters(
  tableSelector,
  containerSelector,
  columnIndexes = []
) {
  const $table = $(tableSelector);
  const $container = $(containerSelector);

  if (!$.fn.DataTable.isDataTable($table)) {
    console.warn("DataTable belum terinisialisasi.");
    return () => ({});
  }

  const table = $table.DataTable();
  const headers = $table
    .find("thead th")
    .map((_, th) => $(th).text().trim())
    .get();

  $container.empty();

  if (!document.getElementById("external-filter-style")) {
    $("<style>", {
      id: "external-filter-style",
      html: `.filterArea{display:flex;flex-wrap:wrap;gap:1rem;margin-bottom:1rem;align-items:flex-end;}
             .filterArea .filter-item{min-width:160px;max-width:220px;flex:1;display:flex;flex-direction:column;}
             .filterArea label{font-size:.85rem;font-weight:600;margin-bottom:.25rem;white-space:nowrap;}
             .filterArea select{font-size:.85rem;padding:.25rem .5rem;width:100%;}
             .filterArea .filter-reset{margin-left:auto;margin-top:auto;}`,
    }).appendTo("head");
  }

  columnIndexes.forEach((index) => {
    const column = table.column(index);
    const title = headers[index] || `Kolom ${index}`;
    const currentSearch = column.search() || "";
    const uniqueValues = column
      .data({ search: "applied" })
      .unique()
      .sort()
      .toArray()
      .filter((v) => v?.trim());

    const $filter = $(
      `<div class="filter-item"><label>${title}</label><select class="form-control form-control-sm select2"><option value="">All</option></select></div>`
    );
    const $select = $filter.find("select");

    uniqueValues.forEach((val) => {
      $select.append(
        new Option(val, val, false, currentSearch === `^${escapeRegex(val)}$`)
      );
    });

    $select.on("change", function () {
      column
        .search(this.value ? `^${escapeRegex(this.value)}$` : "", true, false)
        .draw();
    });

    $container.append($filter);
  });

  if ($.fn.select2) {
    $container
      .find("select.select2")
      .select2({ theme: "bootstrap4", width: "100%", dropdownAutoWidth: true });
  }

  $container.append(
    `<div class="filter-reset"><button class="btn btn-sm btn-outline-danger" id="resetFilters"><i class="fas fa-times-circle"></i> Reset</button></div>`
  );

  $(document)
    .off("click", "#resetFilters")
    .on("click", "#resetFilters", function () {
      $container.find("select").val("").trigger("change");
      table.columns().search("").draw();
    });

  return function getActiveFilters() {
    const active = {};
    table.columns().every(function (i) {
      const s = this.search();
      if (s) active[headers[i]] = s.replace(/^\^|\$$/g, "");
    });
    return active;
  };
}

/**
 * ⭐ applyRequiredMarkers
 * Tambah bintang merah pada field required dan sembunyikan bila terisi.
 *
 * @param {HTMLElement|Document} container - Elemen pembungkus form atau document
 * @param {Array} configs - [{ selector: '...', position: 'label-right|above' }]
 */
function applyRequiredMarkers(container, configs) {
  configs.forEach(({ selector, position }) => {
    container.querySelectorAll(selector).forEach((el) => {
      if (el.classList.contains("has-star")) return;

      if (position === "label-right") {
        const star = document.createElement("span");
        star.className = "text-danger required-star";
        star.innerHTML = " *";
        el.appendChild(star);
      } else if (position === "above") {
        const notice = document.createElement("div");
        notice.className = "mb-1 small required-star";
        notice.innerHTML = '<span class="text-danger">*</span> Wajib diisi';

        if (!el.querySelector(".required-star")) {
          el.insertBefore(notice, el.firstChild);
        }
      }

      el.classList.add("has-star");
    });
  });

  // Add listeners for input/select/textarea
  container
    .querySelectorAll("input[required], select[required], textarea[required]")
    .forEach((field) => {
      if (!field._hasRequiredListener) {
        // Listen for normal input event on input/textarea
        field.addEventListener("input", () =>
          toggleStarVisibility(field, container)
        );

        // Listen for 'change' event on selects (including Select2)
        field.addEventListener("change", () =>
          toggleStarVisibility(field, container)
        );

        // If this select has select2, also listen for its specific event
        if ($(field).hasClass("select2-hidden-accessible")) {
          $(field).on("select2:select select2:unselect", () =>
            toggleStarVisibility(field, container)
          );
        }

        field._hasRequiredListener = true;
      }

      // Initial visibility update
      toggleStarVisibility(field, container);
    });
}

/**
 * 🔁 toggleStarVisibility
 * Sembunyikan bintang bila input/select/textarea sudah terisi
 *
 * @param {HTMLElement} input - input/select/textarea element yang dicek
 * @param {HTMLElement|Document} container - container tempat label dan .form-group berada
 */
function toggleStarVisibility(input, container) {
  if (!input || !container) return;

  const id = input.id;

  // Jika ada ID, sembunyikan star di label yang terkait
  if (id) {
    const labelStar = container.querySelector(
      `label[for="${id}"] .required-star`
    );
    if (labelStar) {
      labelStar.style.display = input.value.trim() ? "none" : "inline";
    }
  }

  // Sembunyikan star di .form-group.required (posisi "above")
  const formGroup = input.closest(".form-group.required");
  if (formGroup) {
    const formGroupStar = formGroup.querySelector(".required-star");
    if (formGroupStar) {
      formGroupStar.style.display = input.value.trim() ? "none" : "block";
    }
  }
}

/**
 * ➕ cloneFormGroup
 * Tambah form dinamis dengan validasi dan batasan maksimum.
 *
 * @param {Object} options
 * @param {string} options.containerSelector - Selector untuk container utama yang menampung semua grup form.
 * @param {string} options.groupSelector - Selector satu grup form yang akan di-clone.
 * @param {string} options.addBtnSelector - Selector tombol yang memicu penambahan clone.
 * @param {number} options.max - Jumlah maksimal grup yang boleh ditambahkan.
 * @param {function} options.updateHeaders - Fungsi callback untuk update urutan/judul.
 * @param {Array} options.requiredConfigs - Konfigurasi tanda bintang dari applyRequiredMarkers.
 */
function cloneFormGroup({
  containerSelector,
  groupSelector,
  addBtnSelector,
  max = 5,
  updateHeaders,
  requiredConfigs = [],
}) {
  $(document).on("click", addBtnSelector, function () {
    const $container = $(containerSelector);
    const $groups = $container.find(groupSelector);
    const total = $groups.length;

    if (total >= max) {
      Swal.fire({
        icon: "warning",
        title: `Maksimal ${max} form tercapai!`,
        timer: 1500,
        showConfirmButton: true,
      });
      return;
    }

    const $original = $groups.first();

    // Destroy select2 before cloning
    $original.find("select.select2").each(function () {
      if ($(this).hasClass("select2-hidden-accessible")) {
        $(this).select2("destroy");
      }
    });

    const $clone = $original.clone();

    // Update data-index attr
    $clone.attr("data-index", total);

    // Update IDs and label for attributes
    $clone.find("[id]").each(function () {
      const oldId = $(this).attr("id");
      if (!oldId) return;
      const baseId = oldId.replace(/\d+$/, "");
      const newId = baseId + total;
      $(this).attr("id", newId);
    });

    $clone.find("label[for]").each(function () {
      const oldFor = $(this).attr("for");
      if (!oldFor) return;
      const baseFor = oldFor.replace(/\d+$/, "");
      const newFor = baseFor + total;
      $(this).attr("for", newFor);
    });

    // Clear inputs/selects in clone
    $clone.find("input, select, textarea").each(function () {
      $(this).val("");
    });

    // Append clone
    $container.append($clone);

    // Re-init select2 only if not already initialized
    $original.find("select.select2").each(function () {
      if (!$(this).hasClass("select2-hidden-accessible")) {
        $(this).select2({ theme: "bootstrap4" }); // ← Apply Bootstrap 4 theme
      }
    });
    $clone.find("select.select2").each(function () {
      if (!$(this).hasClass("select2-hidden-accessible")) {
        $(this).select2({ theme: "bootstrap4" }); // ← Apply Bootstrap 4 theme
      }
    });

    // Show form-body if hidden
    $clone.find(".form-body").show();

    // Update headers
    if (typeof updateHeaders === "function") {
      updateHeaders();
    }

    // Apply required markers on clone
    if (requiredConfigs.length > 0) {
      applyRequiredMarkers($clone[0], requiredConfigs);
    }
  });
}
