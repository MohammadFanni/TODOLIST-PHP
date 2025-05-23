export function showSnackbar(message, type = 'error') {
  const snackbar = document.getElementById('snackbar');
  if (!snackbar) return;

  // Reset class awal
  snackbar.className = `fixed top-5 left-1/2 transform -translate-x-1/2 px-4 py-2 rounded shadow-lg opacity-0 pointer-events-none transition-opacity duration-300 z-50 text-white text-sm`;

  if (type === 'success') {
    snackbar.classList.add('bg-green-500');
  } else {
    snackbar.classList.add('bg-red-500');
  }

  snackbar.textContent = message;

  // Tampilkan dengan animasi
  snackbar.classList.remove('opacity-0', 'pointer-events-none');
  snackbar.classList.add('opacity-100', 'animate-slideDown');

  // Sembunyikan setelah 3 detik
  setTimeout(() => {
    snackbar.classList.remove('opacity-100', 'animate-slideDown');
    snackbar.classList.add('opacity-0', 'pointer-events-none');
  }, 3000);
}
