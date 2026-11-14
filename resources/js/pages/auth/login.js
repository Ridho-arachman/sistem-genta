// Halaman login: tampilkan/sembunyikan password + cegah submit ganda
document.addEventListener('alpine:init', () => {
    Alpine.data('loginForm', () => ({
        showPassword: false,
        loading: false,
    }));
});
