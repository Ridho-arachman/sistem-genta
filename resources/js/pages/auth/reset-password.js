// Halaman reset password: toggle password + status loading
document.addEventListener('alpine:init', () => {
    Alpine.data('resetPasswordForm', () => ({
        showPassword: false,
        loading: false,
    }));
});
