const buttons = document.querySelectorAll('.route66-ai-generate-button');

for (let index = 0; index < buttons.length; index++) {
    const button = buttons[index];
    button.addEventListener('click', (event) => {
        event.preventDefault();
        const id = event.target.dataset.aiToolId;
        const target = event.target.dataset.aiToolTarget;
        window.parent.postMessage({ method: 'route66:ai:run', id: id, target: target });
        window.parent.Joomla.Modal.current.close();
    });
}