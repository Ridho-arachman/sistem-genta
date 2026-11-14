// Form user create & edit — data dapur dikirim dari view
document.addEventListener('alpine:init', () => {
    Alpine.data('userForm', (dapurs = [], role = '', dapurIds = [], kepalaDapurId = '') => ({
        selectedRole: role,
        selectedDapurIds: dapurIds,
        kepalaDapurId,
        dapurs,

        get selectedKepalaDapur() {
            const selected = this.dapurs.find((d) => d.id == this.kepalaDapurId);
            return selected?.kepala_dapur_name || '';
        },

        handleRoleChange() {
            this.selectedDapurIds = [];
            this.kepalaDapurId = '';
        },

        toggleDapur(id) {
            const idx = this.selectedDapurIds.indexOf(id);
            if (idx === -1) {
                this.selectedDapurIds.push(id);
            } else {
                this.selectedDapurIds.splice(idx, 1);
            }
        },

        removeDapur(id) {
            this.selectedDapurIds = this.selectedDapurIds.filter((d) => d !== id);
        },

        getDapurName(id) {
            const d = this.dapurs.find((d) => d.id == id);
            return d ? d.nama : id;
        },
    }));
});
