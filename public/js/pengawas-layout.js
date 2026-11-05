document.addEventListener('DOMContentLoaded', function() {
    const profileMenu = document.getElementById('profile-menu');
    const profileBtn = document.querySelector('[data-action="toggle-profile"]');
    const profileDropdown = document.getElementById('profile-menu-dropdown');

    if (!profileMenu || !profileBtn || !profileDropdown) return;

    // Toggle profile menu
    profileBtn.addEventListener('click', function(e) {
        e.stopPropagation();
        profileDropdown.classList.toggle('hidden');
    });

    // Close on backdrop click
    document.addEventListener('click', function(e) {
        if (!profileMenu.contains(e.target)) {
            profileDropdown.classList.add('hidden');
        }
    });
});
