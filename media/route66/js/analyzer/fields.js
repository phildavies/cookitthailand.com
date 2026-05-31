class Route66FormFieldCloner {

    constructor(sourceFormSelector, targetFormSelector) {

        this.sourceForm = document.querySelector(sourceFormSelector);
        this.targetForm = document.querySelector(targetFormSelector);

        if (!this.sourceForm || !this.targetForm) {
            console.warn('One or both forms not found');
            return;
        }

        this.fields = Array.from(this.sourceForm.querySelectorAll('input[type="text"], input[type="hidden"], select, textarea'));
        this.hiddenInputsMap = new Map();
        this.initHiddenInputs();
        this.addEvents();
    }

    initHiddenInputs() {
        this.fields.forEach(field => {
            const hiddenInput = document.createElement('input');
            hiddenInput.type = 'hidden';
            hiddenInput.name = field.name;
            hiddenInput.value = field.value;
            this.targetForm.appendChild(hiddenInput);
            this.hiddenInputsMap.set(field.name, hiddenInput);
        });
    }

    update() {
        this.fields.forEach(field => {
            const hiddenInput = this.hiddenInputsMap.get(field.name);
            if (hiddenInput) {
                hiddenInput.value = field.value;
            }
        });
    }

    addEvents() {
        this.sourceForm.addEventListener('change', () => this.update());
    }
}

export default Route66FormFieldCloner;