/* =====================================================
   TEMA GLOBAL PERPUSTAKAAN
   Dark Mode dikontrol dari Dashboard
===================================================== */


/* =====================================================
   CEK TEMA SAAT HALAMAN DIBUKA
===================================================== */

function cekTema() {

    const darkMode = localStorage.getItem("darkMode");

    if (darkMode === "true") {

        document.documentElement.classList.add("dark-mode");

    } else {

        document.documentElement.classList.remove("dark-mode");

    }
}


/* Jalankan langsung */
cekTema();


/* =====================================================
   TOGGLE DARK MODE
   Tombol ini hanya ada di Dashboard
===================================================== */

function toggleDarkMode() {

    const sedangDark =
        document.documentElement.classList.contains("dark-mode");


    if (sedangDark) {

        localStorage.setItem("darkMode", "false");

        document.documentElement.classList.remove("dark-mode");

    } else {

        localStorage.setItem("darkMode", "true");

        document.documentElement.classList.add("dark-mode");

    }


    updateTombolDarkMode();
}


/* =====================================================
   UPDATE TEKS TOMBOL DI DASHBOARD
===================================================== */

function updateTombolDarkMode() {

    const tombol =
        document.getElementById("darkModeBtn");


    if (!tombol) {
        return;
    }


    if (
        document.documentElement.classList.contains(
            "dark-mode"
        )
    ) {

        tombol.innerHTML = "☀️ Light Mode";

    } else {

        tombol.innerHTML = "🌙 Dark Mode";

    }
}


/* =====================================================
   SAAT HALAMAN SELESAI DIMUAT
===================================================== */

document.addEventListener(
    "DOMContentLoaded",
    function () {

        cekTema();

        updateTombolDarkMode();

    }
);