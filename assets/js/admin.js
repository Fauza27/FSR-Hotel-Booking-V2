// assets/js/admin.js
// Script for admin panel interactivity

document.addEventListener("DOMContentLoaded", function () {
  // Sidebar active state (for dynamic nav highlighting if needed)
  const navLinks = document.querySelectorAll(".nav-link");
  navLinks.forEach((link) => {
    if (link.href === window.location.href) {
      link.classList.add("active");
    }
  });

  // Dropdown for user menu
  const dropdownToggles = document.querySelectorAll(".dropdown-toggle");
  dropdownToggles.forEach((toggle) => {
    toggle.addEventListener("click", function (e) {
      e.preventDefault();
      const menu = this.nextElementSibling;
      if (menu) {
        menu.classList.toggle("show");
      }
    });
  });

  // Close dropdown on outside click
  document.addEventListener("click", function (e) {
    document.querySelectorAll(".dropdown-menu").forEach((menu) => {
      if (!menu.contains(e.target) && !menu.previousElementSibling.contains(e.target)) {
        menu.classList.remove("show");
      }
    });
  });

  // Dismiss alert
  document.querySelectorAll(".alert .btn-close").forEach((btn) => {
    btn.addEventListener("click", function () {
      this.closest(".alert").style.display = "none";
    });
  });

  // Responsive sidebar toggle (optional, if you want to add a hamburger menu)
  // ...

  // Table row click highlight (optional)
  // document.querySelectorAll('.admin-table tbody tr').forEach(row => {
  //     row.addEventListener('click', function () {
  //         this.classList.toggle('selected');
  //     });
  // });
});
