document.addEventListener("DOMContentLoaded", function () {
  // =======================================================
  // FUNGSI GLOBAL & UMUM
  // =======================================================

  /**
   * Mengatur fungsionalitas toggle untuk menu mobile.
   * Target: #menu-toggle-btn, #mobile-menu
   */
  function setupMobileMenu() {
    const menuToggleButton = document.getElementById("menu-toggle-btn");
    const mobileMenu = document.getElementById("mobile-menu");

    if (menuToggleButton && mobileMenu) {
      menuToggleButton.addEventListener("click", function () {
        mobileMenu.classList.toggle("hidden"); // Gunakan kelas 'hidden' dari Tailwind
        const icon = menuToggleButton.querySelector("i");
        if (mobileMenu.classList.contains("hidden")) {
          icon.classList.remove("fa-times");
          icon.classList.add("fa-bars");
        } else {
          icon.classList.remove("fa-bars");
          icon.classList.add("fa-times");
        }
      });
    }
  }

  /**
   * Menutup semua elemen .alert secara otomatis setelah 5 detik.
   */
  function setupAutoCloseAlerts() {
    const alerts = document.querySelectorAll(".alert");
    if (alerts.length > 0) {
      setTimeout(() => {
        alerts.forEach((alert) => {
          // Tambahkan kelas untuk transisi fade-out
          alert.classList.add("opacity-0", "transition-opacity", "duration-500", "ease-out");
          setTimeout(() => alert.remove(), 500); // Hapus elemen dari DOM setelah transisi selesai
        });
      }, 5000);
    }
  }

  /**
   * Mengatur validasi dasar pada form pencarian kamar.
   * Mencegah tanggal check-out lebih awal dari check-in.
   * Target: form yang memiliki input #check_in dan #check_out
   */
  function setupRoomSearchForm() {
    const checkInInput = document.querySelector("#check_in");
    const checkOutInput = document.querySelector("#check_out");

    if (checkInInput && checkOutInput) {
      const searchForm = checkInInput.closest("form");

      // Update tanggal minimum check-out saat check-in berubah
      checkInInput.addEventListener("change", function () {
        const checkInDate = new Date(this.value);
        if (isNaN(checkInDate.getTime())) return;

        const nextDay = new Date(checkInDate);
        nextDay.setDate(nextDay.getDate() + 1);

        const minCheckoutDate = nextDay.toISOString().split("T")[0];
        checkOutInput.min = minCheckoutDate;

        if (new Date(checkOutInput.value) <= checkInDate) {
          checkOutInput.value = minCheckoutDate;
        }
      });

      // Validasi saat submit
      if (searchForm) {
        searchForm.addEventListener("submit", function (e) {
          const checkInDate = new Date(checkInInput.value);
          const checkOutDate = new Date(checkOutInput.value);
          if (checkOutDate <= checkInDate) {
            e.preventDefault();
            alert("Check-out date must be after check-in date."); // UI bisa ditingkatkan
          }
        });
      }
    }
  }

  /**
   * Mengatur toggle untuk menampilkan/menyembunyikan password.
   * Target: .password-toggle
   */
  function setupPasswordToggle() {
    const passwordToggles = document.querySelectorAll(".password-toggle");
    passwordToggles.forEach((toggle) => {
      toggle.addEventListener("click", function () {
        const targetId = this.getAttribute("data-target");
        const passwordField = document.getElementById(targetId);
        if (passwordField) {
          if (passwordField.type === "password") {
            passwordField.type = "text";
            this.innerHTML = '<i class="fas fa-eye-slash"></i>';
          } else {
            passwordField.type = "password";
            this.innerHTML = '<i class="fas fa-eye"></i>';
          }
        }
      });
    });
  }

  /**
   * Validasi konfirmasi password pada form registrasi.
   * Target: #register-form
   */
  function setupRegistrationForm() {
    const registerForm = document.getElementById("register-form");
    if (registerForm) {
      registerForm.addEventListener("submit", function (e) {
        const password = document.getElementById("password");
        const confirmPassword = document.getElementById("confirm_password");
        if (password && confirmPassword && password.value !== confirmPassword.value) {
          e.preventDefault();
          alert("Passwords do not match."); // UI bisa ditingkatkan
        }
      });
    }
  }

  /**
   * Memformat tanggal yang memiliki kelas .format-date.
   * Mengambil tanggal dari atribut data-date.
   */
  function setupDateFormatter() {
    const formatDates = document.querySelectorAll(".format-date");
    formatDates.forEach((dateEl) => {
      const originalDate = dateEl.getAttribute("data-date");
      if (originalDate) {
        const dateObj = new Date(originalDate);
        const options = { year: "numeric", month: "short", day: "numeric" };
        dateEl.textContent = dateObj.toLocaleDateString("en-US", options);
      }
    });
  }

  // =======================================================
  // FUNGSI SPESIFIK UNTUK HALAMAN DETAIL KAMAR
  // =======================================================

  /**
   * Mengatur kalkulator harga pada sidebar booking di halaman detail kamar.
   * Target: #booking-sidebar
   */
  function setupBookingPriceCalculator() {
    const bookingSidebar = document.getElementById("booking-sidebar");
    if (!bookingSidebar) return; // Keluar jika bukan di halaman detail

    const checkInInput = document.getElementById("check_in");
    const checkOutInput = document.getElementById("check_out");
    const numNightsElement = document.getElementById("num-nights");
    const totalPriceElement = document.getElementById("total-price");
    const totalPriceInput = document.getElementById("total-price-input");
    const pricePerNight = parseFloat(bookingSidebar.dataset.pricePerNight) || 0;

    const updatePricing = () => {
      if (!checkInInput.value || !checkOutInput.value) {
        if (numNightsElement) numNightsElement.textContent = "0";
        if (totalPriceElement) totalPriceElement.textContent = "Rp 0";
        if (totalPriceInput) totalPriceInput.value = 0;
        return;
      }

      const checkIn = new Date(checkInInput.value);
      const checkOut = new Date(checkOutInput.value);

      if (isNaN(checkIn.getTime()) || isNaN(checkOut.getTime()) || checkOut <= checkIn) {
        if (numNightsElement) numNightsElement.textContent = "0";
        if (totalPriceElement) totalPriceElement.textContent = "Rp 0";
        if (totalPriceInput) totalPriceInput.value = 0;
        return;
      }

      const timeDiff = checkOut.getTime() - checkIn.getTime();
      const nightCount = Math.max(0, Math.ceil(timeDiff / (1000 * 3600 * 24)));
      const total = nightCount * pricePerNight;

      if (numNightsElement) numNightsElement.textContent = nightCount;
      if (totalPriceElement) totalPriceElement.textContent = "Rp " + new Intl.NumberFormat("id-ID").format(total);
      if (totalPriceInput) totalPriceInput.value = total;
    };

    if (checkInInput && checkOutInput) {
      checkInInput.addEventListener("change", () => {
        const checkInDate = new Date(checkInInput.value);
        if (isNaN(checkInDate.getTime())) {
          updatePricing();
          return;
        }
        const nextDay = new Date(checkInDate);
        nextDay.setDate(nextDay.getDate() + 1);
        const minCheckoutDate = nextDay.toISOString().split("T")[0];
        checkOutInput.min = minCheckoutDate;
        if (new Date(checkOutInput.value) <= checkInDate) {
          checkOutInput.value = minCheckoutDate;
        }
        updatePricing();
      });
      checkOutInput.addEventListener("change", updatePricing);
      updatePricing(); // Inisialisasi saat halaman dimuat
    }
  }

  /**
   * Mengatur galeri gambar di halaman detail kamar.
   * Target: #main-room-image, .thumbnail
   */
  function setupImageGallery() {
    const mainImage = document.getElementById("main-room-image");
    const thumbnails = document.querySelectorAll(".thumbnail");

    if (!mainImage || thumbnails.length === 0) return; // Keluar jika bukan di halaman detail

    thumbnails.forEach((thumbnail) => {
      thumbnail.addEventListener("click", function () {
        // Hapus kelas aktif dari semua thumbnail
        thumbnails.forEach((thumb) => thumb.classList.remove("border-accent"));
        // Tambahkan kelas aktif ke thumbnail yang diklik
        this.classList.add("border-accent");

        const newImageSrc = this.getAttribute("data-image");
        if (newImageSrc) {
          mainImage.src = newImageSrc;
        }
      });
    });
  }

  // =======================================================
  // FUNGSI SPESIFIK UNTUK HALAMAN CHECKOUT
  // =======================================================

  /**
   * Mengatur fungsionalitas pada halaman checkout pembayaran.
   * Termasuk toggle metode pembayaran, format input, dan validasi.
   * Target: Halaman dengan elemen #complete-payment-btn
   */
  // function setupCheckoutPage() {
  //   const paymentForm = document.getElementById("payment-form");
  //   if (!paymentForm) return; // Hanya jalankan jika kita berada di halaman checkout

  //   // --- 1. Toggle metode pembayaran ---
  //   const paymentMethodSelect = document.getElementById("payment_method");
  //   const creditCardForm = document.getElementById("credit-card-form");
  //   const bankTransferForm = document.getElementById("bank-transfer-form");
  //   const cashForm = document.getElementById("cash-form");
  //   const allForms = [creditCardForm, bankTransferForm, cashForm];

  //   const transferProofInput = document.getElementById("transfer_proof");
  //   const creditCardInputs = creditCardForm ? creditCardForm.querySelectorAll("input") : [];

  // if (paymentMethodSelect && allForms.every((form) => form)) {
  //   paymentMethodSelect.addEventListener("change", function () {
  //     const selectedValue = this.value;

  //     // Sembunyikan semua form dan nonaktifkan input di dalamnya
  //     allForms.forEach((form) => {
  //       form.classList.add("hidden");
  //     });
  //     if (transferProofInput) transferProofInput.required = false;
  //     creditCardInputs.forEach((input) => (input.required = false));

  //     // Tampilkan form yang dipilih dan aktifkan inputnya
  //     if (selectedValue === "credit_card") {
  //       creditCardForm.classList.remove("hidden");
  //       creditCardInputs.forEach((input) => (input.required = true));
  //     } else if (selectedValue === "bank_transfer") {
  //       bankTransferForm.classList.remove("hidden");
  //       if (transferProofInput) transferProofInput.required = true;
  //     } else if (selectedValue === "cash") {
  //       cashForm.classList.remove("hidden");
  //     }
  //   });
  //}

  // // --- 2. Format input kartu kredit ---
  // const cardNumberInput = document.getElementById("card_number");
  // if (cardNumberInput) {
  //   cardNumberInput.addEventListener("input", function (e) {
  //     // Hanya izinkan angka dan batasi panjang
  //     let value = this.value.replace(/\D/g, "").substring(0, 16);
  //     if (value.length > 0) {
  //       // Tambahkan spasi setiap 4 digit
  //       value = value.match(/.{1,4}/g).join(" ");
  //     }
  //     this.value = value;
  //   });
  // }

  // const expiryDateInput = document.getElementById("expiry_date");
  // if (expiryDateInput) {
  //   expiryDateInput.addEventListener("input", function (e) {
  //     // Hanya izinkan angka dan batasi panjang
  //     let value = this.value.replace(/\D/g, "").substring(0, 4);
  //     if (value.length > 2) {
  //       // Tambahkan '/' setelah bulan
  //       value = value.substring(0, 2) + "/" + value.substring(2);
  //     }
  //     this.value = value;
  //   });
  // }

  // const cvvInput = document.getElementById("cvv");
  // if (cvvInput) {
  //   cvvInput.addEventListener("input", function (e) {
  //     this.value = this.value.replace(/\D/g, "").substring(0, 4);
  //   });
  // }

  // // --- 3. Logika submit form ---
  // // Logika validasi kini bisa ditangani oleh atribut `required` HTML5
  // // yang kita toggle dengan JS di atas. Tombol submit bisa disederhanakan.
  // paymentForm.addEventListener("submit", function (e) {
  //   const submitButton = document.getElementById("complete-payment-btn");
  //   if (submitButton) {
  //     submitButton.disabled = true;
  //     submitButton.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Processing...';

  //     // Logika redirect setelah 'pemrosesan'
  //     const successUrl = submitButton.getAttribute("data-success-url");
  //     if (successUrl) {
  //       // Dalam aplikasi nyata, ini akan terjadi setelah respons sukses dari server
  //       setTimeout(() => {
  //         window.location.href = successUrl;
  //       }, 2000);
  //     }
  //   }
  // });
  //}

  // =======================================================
  // INISIALISASI SEMUA FUNGSI
  // =======================================================

  // Panggil semua fungsi setup saat DOM siap.
  // Setiap fungsi memiliki pengecekan internal untuk memastikan
  // hanya berjalan di halaman yang relevan.
  setupMobileMenu();
  setupAutoCloseAlerts();
  setupRoomSearchForm();
  setupPasswordToggle();
  setupRegistrationForm();
  setupDateFormatter();
  setupBookingPriceCalculator();
  setupImageGallery();
  setupCheckoutPage();
});
