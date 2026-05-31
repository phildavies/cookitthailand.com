import { JoomlaEditor, JoomlaEditorButton } from 'editor-api';
import { Parser } from 'route66-parser';

class Route66AI {

    constructor() {
        this.options = Joomla.getOptions('route66');
        this.setFields(this.options);
        this.addEvents();
    }

    get keyphrase() {
        return this.fields.keyphrase.value;
    }

    get title() {
        return this.extensionFields.title.value;
    }

    get text() {

        let text = '';

        if (this.options.editor) {
            text += JoomlaEditor.get(this.extensionFields.text.id).getValue();
        } else if (this.extensionFields.text) {
            text += this.extensionFields.text.value;
        }

        return this.stripHtml(text);
    }

    get language() {

        let language = this.options.language;

        if (this.options.multilanguage && this.extensionFields.language && this.extensionFields.language.value && this.extensionFields.language.value !== '*') {
            language = this.extensionFields.language.value;
        }

        return language;
    }

    getInput(overrides) {

        const input = {
            keyphrase: this.keyphrase,
            title: this.title,
            text: this.text,
            language: this.language
        }

        return Object.assign({}, input, overrides || {});
    }

    setFields(options) {

        // Core Route 66 fields
        this.fields = {};
        this.fields.keyphrase = document.querySelector('.route66-analyzer-keyphrase');

        // Extension fields
        this.extensionFields = {};
        this.extensionFields.title = document.querySelector(options.fields.title);
        this.extensionFields.text = document.querySelector(options.fields.text);
        this.extensionFields.language = document.querySelector(options.fields.language);

        // Target fields
        this.targetFields = {};
        this.targetFields['title'] = document.querySelector(options.fields.title);
        this.targetFields['text'] = document.querySelector(options.fields.text);
        this.targetFields['seo_title'] = document.querySelector('.route66-metadata-title');
        this.targetFields['meta_description'] = document.querySelector('.route66-metadata-description');
        this.targetFields['og_title'] = document.querySelector('.route66-og-title');
        this.targetFields['og_description'] = document.querySelector('.route66-og-description');
        this.targetFields['x_title'] = document.querySelector('.route66-x-title');
        this.targetFields['x_description'] = document.querySelector('.route66-x-description');

    }

    addEvents() {

        window.addEventListener('message', (event) => {

            const root = Joomla.getOptions('system.paths').rootFull;
            const origin = root.substring(0, root.length - 1);

            if (event.origin !== origin) {
                return;
            }

            if (event.data.method === 'route66:ai:run') {
                const target = event.data.target.startsWith('#') || event.data.target.startsWith('.') ? event.source.document.querySelector(event.data.target) : event.data.target;
                this.run(event.data.id, target, event.data.input);
            } else if (event.data.method === 'route66:ai:insert') {
                this.insert(event.data.output, event.data.target);
            } else if (event.data.method === 'route66:ai:input') {
                event.source.postMessage({ method: 'route66:ai:input', input: this.getInput() });
            }

        });

        const aiButtons = document.querySelectorAll('.route66-ai-tool-button');

        for (let index = 0; index < aiButtons.length; index++) {
            const button = aiButtons[index];

            button.addEventListener('click', async (event) => {
                event.preventDefault();
                event.target.disabled = true;
                event.target.classList.add('disabled');
                event.target.classList.add('pe-none');
                const toolId = event.target.dataset.aiToolId;
                const target = this.targetFields[event.target.dataset.aiToolTarget];
                await this.run(toolId, target);
                event.target.disabled = false;
                event.target.classList.remove('disabled');
                event.target.classList.remove('pe-none');
            });
        }

        JoomlaEditorButton.registerAction('route66-ai-tool', (editor, options) => {
            this.editorAction(editor, options);
        });
    }

    insert(output, target) {

        target = this.targetFields[target];

        if (target.nodeName === 'TEXTAREA') {
            const editor = JoomlaEditor.get(target.id);
            if (editor) {
                editor.setValue(editor.getValue() + output);
                return;
            }
        }

        if (target.nodeName === 'INPUT' || target.nodeName === 'TEXTAREA') {
            target.value = output;
        } else if (target.nodeName === 'DIV') {
            target.innerHTML = output;
        }
    }

    async run(id, target, input = {}) {

        if (typeof target === 'string') {
            target = this.targetFields[target];
            const editor = JoomlaEditor.get(target.id);
            if (editor) {
                target = editor;
            }
        }

        const payload = Object.assign({}, this.getInput(), input);

        await this.stream(id, payload, {
            onStart: this.onStart(target),
            onChunk: this.onChunk(target),
            onComplete: this.onComplete(target)
        });
    }

    async init(id, input) {

        const data = new FormData();
        for (const key in input) {
            data.append(key, input[key]);
        }
        data.append('id', id);
        data.append(window.Joomla.getOptions('csrf.token'), 1);

        const url = Joomla.getOptions('system.paths').rootFull + 'administrator/index.php?option=com_route66&task=aitool.init';

        const response = await fetch(url, {
            method: 'POST',
            body: data
        });

        return response.text();
    }


    async stream(id, input, { onStart, onChunk, onComplete }) {

        const requestId = await this.init(id, input);

        const url = Joomla.getOptions('system.paths').rootFull + 'administrator/index.php?option=com_route66&task=aitool.run&request=' + encodeURIComponent(requestId) + '&id=' + encodeURIComponent(id) + '&' + encodeURIComponent(window.Joomla.getOptions('csrf.token')) + '=1';

        onStart();

        const source = new EventSource(url, {
            withCredentials: true,
        });

        source.addEventListener('chunk', (event) => {
            onChunk(event.data);
        });

        source.addEventListener('done', () => {
            source.close();
            onComplete();
        });

        source.onerror = (err) => {
            console.error('SSE error:', err);
            source.close();
            onComplete();
        };
    }

    onStart(target) {

        if (target.nodeName === 'INPUT' || target.nodeName === 'TEXTAREA') {
            return function () {
                target.value = '';
            }
        }

        return function () {
            target.innerHTML = '';
        }
    }

    onChunk(target) {

        if (target.nodeName === 'INPUT' || target.nodeName === 'TEXTAREA') {
            return function (chunk) {
                target.value += chunk;
            }
        }

        if (target.nodeName === 'DIV') {
            let parser = null;
            return (chunk) => {
                if (!parser) {
                    parser = this.getParser(target, target);
                }
                parser.write(chunk);
            }
        }

        if (typeof target.replaceSelection === 'function') {

            const container = document.createElement('div');
            container.id = 'route66-editor-output';

            let currentNode = null;

            if (target.instance && target.instance.contentDocument) {
                target.replaceSelection(container.outerHTML);
                currentNode = target.instance.contentDocument.querySelector('#route66-editor-output');
            } else {
                container.style.display = 'none';
                document.body.append(container);
                currentNode = container;
            }

            let parser = null;
            return (chunk) => {
                if (!parser) {
                    parser = this.getParser(target, currentNode);
                }
                parser.write(chunk);
                if (target.instance && typeof target.instance.execCommand === 'function') {
                    target.instance.execCommand('mceAutoResize');
                }
            }
        }
    }

    onComplete(target) {

        if (target.nodeName === 'INPUT' || target.nodeName === 'TEXTAREA') {
            return function () {
                target.dispatchEvent(new Event('input', { bubbles: true }));
                target.dispatchEvent(new Event('change', { bubbles: true }));
            }
        }

        if (target.nodeName === 'DIV') {
            return function () {
                const modal = document.querySelector('joomla-dialog.route66-ai-tools iframe');
                if (modal) {
                    modal.contentWindow.postMessage({ method: 'route66:ai:completed' });
                }
            }
        }

        if (typeof target.replaceSelection === 'function') {

            return function () {
                const container = target.instance && target.instance.contentDocument ? target.instance.contentDocument.querySelector('#route66-editor-output') : document.querySelector('#route66-editor-output');
                if (container) {
                    const html = container.innerHTML;
                    container.remove();
                    target.replaceSelection(html);
                }
            }
        }
    }

    async editorAction(editor, options) {
        const selection = editor.getSelection();
        const input = selection ? this.getInput({ text: selection }) : this.getInput();
        await this.stream(options.tool, input, { onStart: this.onStart(editor), onChunk: this.onChunk(editor), onComplete: this.onComplete(editor) });
    }

    stripHtml(html) {
        let doc = new DOMParser().parseFromString(html, 'text/html');
        return doc.body.textContent || '';
    }

    getParser(target, currentNode) {
        return new Parser({
            onopentag(name, attribs) {
                const element = document.createElement(name);
                Object.entries(attribs).forEach(([key, val]) => {
                    element.setAttribute(key, val);
                });
                currentNode.appendChild(element);
                currentNode = element;
            },
            ontext(text) {
                if (text.trim()) {
                    currentNode.appendChild(document.createTextNode(text));
                }
            },
            onclosetag(name) {
                if (currentNode !== target) {
                    currentNode = currentNode.parentNode;
                }
            },
        });
    }
}

export default Route66AI;