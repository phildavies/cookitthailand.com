const enableButtons = (buttons) => {
    for (let index = 0; index < buttons.length; index++) {
        buttons[index].disabled = false;
    }
}

const disableButtons = (buttons) => {
    for (let index = 0; index < buttons.length; index++) {
        buttons[index].disabled = true;
    }
}

const getInput = (fields) => {
    const input = {};
    Array.from(fields).forEach(field => {
        input[field.name] = field.value;
    });
    return input;
}


const output = document.querySelector('#route66-ai-tool-output');
const generateButton = document.querySelector('#route66-ai-generate-button');
const inputFields = document.querySelectorAll('.route66-ai-input');
const insertButton = document.querySelector('#route66-ai-insert-button');
const copyButton = document.querySelector('#route66-ai-copy-button');

generateButton.addEventListener('click', (event) => {
    event.preventDefault();
    disableButtons([generateButton, insertButton, copyButton]);
    const id = event.target.dataset.aiToolId;
    const input = getInput(inputFields);
    window.parent.postMessage({ method: 'route66:ai:run', id: id, target: '#route66-ai-tool-output', input: input });
});

insertButton.addEventListener('click', (event) => {
    event.preventDefault();
    const target = event.target.dataset.target;
    window.parent.postMessage({ method: 'route66:ai:insert', output: output.innerHTML, target: target });
    window.parent.Joomla.Modal.current.close();
});

copyButton.addEventListener('click', (event) => {
    event.preventDefault();
    navigator.clipboard.writeText(output.innerHTML).then(() => {
        window.parent.Joomla.Modal.current.close();
    }).catch(err => {
        console.error(err);
    });
});

window.addEventListener('message', (event) => {

    const root = Joomla.getOptions('system.paths').rootFull;
    const origin = root.substring(0, root.length - 1);

    if (event.origin !== origin) {
        return;
    }

    if (event.data.method === 'route66:ai:input') {

        for (const key in event.data.input) {
            const element = document.getElementById(key);
            if (element) {
                element.value = event.data.input[key];
            }
        }
    }

    if (event.data.method === 'route66:ai:completed') {
        enableButtons([generateButton, insertButton, copyButton]);
    }
});

window.parent.postMessage({ method: 'route66:ai:input' });
