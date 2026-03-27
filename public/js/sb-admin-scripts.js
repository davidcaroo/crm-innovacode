window.addEventListener("DOMContentLoaded", function () {
  const sidebarToggle = document.body.querySelector("#sidebarToggle");
  if (sidebarToggle) {
    sidebarToggle.addEventListener("click", function (event) {
      event.preventDefault();
      document.body.classList.toggle("sb-sidenav-toggled");
      localStorage.setItem(
        "sb|sidebar-toggle",
        document.body.classList.contains("sb-sidenav-toggled"),
      );
    });
  }

  const currentUrl = window.location.href;
  document.querySelectorAll(".sb-sidenav a.nav-link").forEach(function (link) {
    if (link.href === currentUrl) {
      link.classList.add("active");
    }
  });
});
