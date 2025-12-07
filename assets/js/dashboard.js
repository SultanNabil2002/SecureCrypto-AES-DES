// add hovered class to selected list item
// Bagian ini untuk efek hover pada item navigasi, biarkan seperti adanya.
let list = document.querySelectorAll(".navigation li");

function activeLink() {
  list.forEach((item) => {
    item.classList.remove("hovered");
  });
  this.classList.add("hovered");
}

list.forEach((item) => item.addEventListener("mouseover", activeLink));


// Menu Toggle
let toggle = document.querySelector(".toggle");
let navigation = document.querySelector(".navigation");
let main = document.querySelector(".main");
// let toggleIcon = document.querySelector(".toggle ion-icon"); // Baris ini tidak diperlukan lagi karena ikon tidak diubah

// Pastikan semua elemen penting ada sebelum menambahkan event listener
// Ini untuk mencegah error jika skrip ini termuat di halaman yang tidak memiliki elemen .toggle, .navigation, atau .main
if (toggle && navigation && main) {
    toggle.addEventListener("click", function () {
        // Toggle kelas 'active' pada elemen navigasi dan main
        navigation.classList.toggle("active");
        main.classList.toggle("active");

        // Logika untuk mencegah scroll body saat menu mobile terbuka (tetap relevan)
        // Kita masih perlu tahu apakah navigasi sedang aktif atau tidak untuk ini.
        if (navigation.classList.contains("active")) {
            // Jika navigasi SEKARANG AKTIF (terbuka/melebar)
            if (window.innerWidth <= 480) { // Hanya berlaku untuk mobile (atau breakpoint yang Anda inginkan)
                document.body.style.overflow = "hidden"; // Cegah body di-scroll
            }
        } else {
            // Jika navigasi SEKARANG TIDAK AKTIF (tertutup/menyempit)
            if (window.innerWidth <= 480) { // Hanya untuk mobile
                document.body.style.overflow = "auto"; // Kembalikan scroll body
            }
        }
        // Tidak ada lagi kode untuk mengubah toggleIcon.setAttribute("name", ...);
    });
} else {
    // Opsional: Memberi tahu di console jika ada elemen yang tidak ditemukan.
    // Ini bisa membantu debugging jika ada halaman yang tidak menampilkan toggle dengan benar.
    if (!toggle) {
        console.warn("Elemen .toggle tidak ditemukan di halaman ini.");
    }
    if (!navigation) {
        console.warn("Elemen .navigation tidak ditemukan di halaman ini.");
    }
    if (!main) {
        console.warn("Elemen .main tidak ditemukan di halaman ini.");
    }
}