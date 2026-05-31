import { JoomlaEditor } from 'editor-api';
import Route66Yoast from 'route66-yoast';
import Route66AnalyzerResults from 'route66-analyzer-results';

class Route66Analyzer {

    constructor() {
        this.options = Joomla.getOptions('route66');
        this.analyzer = new Route66Yoast(this.options);
        this.renderer = new Route66AnalyzerResults();
        this.setFields(this.options);
        this.addEvents();
        this.addEditorEvent();
        this.start();
    }

    get keyphrase() {
        return this.fields.keyphrase.value;
    }

    get title() {

        let title = this.extensionFields.title.value;

        if (this.extensionFields.metadataTitle && this.extensionFields.metadataTitle.value) {
            title = this.extensionFields.metadataTitle.value;
        }

        if (this.menuTitle) {
            title = this.menuTitle;
        }

        if (this.fields.metadataTitle && this.fields.metadataTitle.value) {
            title = this.fields.metadataTitle.value;
        }

        if (this.options.sitenameInTitle == 1) {
            title = this.options.sitename + ' - ' + title;
        } else if (this.options.sitenameInTitle == 2) {
            title += ' - ' + this.options.sitename;
        }

        return title;
    }

    get description() {

        let description = this.extensionFields.metadataDescription.value;

        if (this.menuDescription) {
            description = this.menuDescription;
        }

        if (this.fields.metadataDescription && this.fields.metadataDescription.value) {
            description = this.fields.metadataDescription.value;
        }

        return description;
    }

    get slug() {
        return this.extensionFields.slug ? this.extensionFields.slug.value : '';
    }

    get locale() {

        let locale = this.options.locale;

        if (this.options.multilanguage && this.extensionFields.language && this.extensionFields.language.value && this.extensionFields.language.value !== '*') {
            locale = this.extensionFields.language.value;
        }

        locale = locale.replace('-', '_');

        return locale;
    }

    get attributes() {
        return {
            keyword: this.keyphrase,
            title: this.title,
            description: this.description,
            slug: this.slug,
            permalink: this.permalink,
            locale: this.locale,
        }
    }

    get text() {

        let text = '';

        this.extensionFields.images.forEach((imageField) => {
            const image = document.createElement('img');
            image.src = imageField.input.value;
            image.alt = imageField.alt ? imageField.alt.value : imageField.caption ? imageField.caption.value : '';
            text += image.innerHTML;
        });

        if (this.editor) {
            text += this.editor.getValue();
        } else if (this.extensionFields.text) {
            text += this.extensionFields.text.value;
        }

        return text;
    }

    get language() {

        let language = this.options.language;

        if (this.options.multilanguage && this.extensionFields.language && this.extensionFields.language.value && this.extensionFields.language.value !== '*') {
            language = this.extensionFields.language;
        }

        return language;
    }

    async start() {
        await this.getPermalinkData();
        this.analyze();
        this.updateSearchPreview();
    }

    setFields(options) {

        // Core Route66 fields
        this.fields = {};
        this.fields.keyphrase = document.querySelector('.route66-analyzer-keyphrase');
        this.fields.seoScore = document.querySelector('.route66-analyzer-seo-score');
        this.fields.readabilityScore = document.querySelector('.route66-readabilityScore-seo-score');
        this.fields.metadataTitle = document.querySelector('.route66-metadata-title');
        this.fields.metadataDescription = document.querySelector('.route66-metadata-description');

        // Extension fields
        this.extensionFields = {};
        this.extensionFields.title = document.querySelector(options.fields.title);
        this.extensionFields.slug = document.querySelector(options.fields.slug);
        this.extensionFields.language = document.querySelector(options.fields.language);
        this.extensionFields.text = document.querySelector(options.fields.text);
        this.extensionFields.images = options.fields.images ? this.getImageFields(options.fields.images) : [];
        this.extensionFields.metadataTitle = options.fields.metadata.title ? document.querySelector(options.fields.metadata.title) : null;
        this.extensionFields.metadataDescription = options.fields.metadata.description ? document.querySelector(options.fields.metadata.description) : null;

        // Extension route fields
        this.routeFields = {};
        this.routeVars = {};
        for (const [key, value] of Object.entries(options.route)) {
            if (value.startsWith('#') || value.startsWith('.') || value.includes('[')) {
                this.routeFields[key] = document.querySelector(value);
                this.routeFields[key].addEventListener('change', async () => {
                    await this.getPermalinkData();
                });
            } else {
                this.routeVars[key] = value;
            }
        }
    }

    getImageFields(images) {

        const imageFields = [];

        for (let i = 0; i < images.length; i++) {

            const image = images[i];

            if (!image.input) {
                continue;
            }

            imageFields.push({
                input: document.querySelector(image.input),
                alt: image.alt ? document.querySelector(image.alt) : null,
                caption: image.caption ? document.querySelector(image.caption) : null,
            });
        }

        return imageFields;
    }

    addEvents() {

        this.fields.keyphrase.addEventListener('change', () => {
            this.analyze();
        });

        this.fields.metadataTitle.addEventListener('input', () => {
            this.updateSearchPreview();
        });

        this.fields.metadataTitle.addEventListener('change', () => {
            this.analyze();
        });

        this.fields.metadataDescription.addEventListener('input', () => {
            this.updateSearchPreview();
        });

        this.fields.metadataDescription.addEventListener('change', () => {
            this.analyze();
        });

        this.extensionFields.title.addEventListener('change', () => {
            this.analyze();
            this.updateSearchPreview();
        });

        if (this.extensionFields.slug) {
            this.extensionFields.slug.addEventListener('change', async () => {
                await this.getPermalinkData();
                this.analyze();
                this.updateSearchPreview();
            });
        }

        if (this.extensionFields.text) {

            this.extensionFields.text.addEventListener('change', () => {
                this.analyze();
            });
        }

        this.extensionFields.images.forEach(imageField => {

            imageField.input.addEventListener('change', () => {
                this.analyze();
            });

            if (imageField.alt) {
                imageField.alt.addEventListener('change', () => {
                    this.analyze();
                });
            }

            if (imageField.caption) {
                imageField.caption.addEventListener('change', () => {
                    this.analyze();
                });
            }

        });

        if (this.options.multilanguage && this.extensionFields.language) {
            this.extensionFields.language.addEventListener('change', () => {
                this.analyze();
            });
        }

        if (this.extensionFields.metadataTitle) {
            this.extensionFields.metadataTitle.addEventListener('change', () => {
                this.analyze();
                this.updateSearchPreview();
            });
        }

        if (this.extensionFields.metadataDescription) {
            this.extensionFields.metadataDescription.addEventListener('change', () => {
                this.analyze();
                this.updateSearchPreview();
            });
        }
    }

    analyze() {

        this.analyzer.analyze(this.text, this.attributes).then((data) => {

            const results = {
                seo: {
                    score: Math.max(0, data.result.seo[''].score),
                    rating: this.analyzer.getRatingFromScore(data.result.seo[''].score / 10),
                    results: [],
                },
                readability: {
                    score: Math.max(0, data.result.readability.score),
                    rating: this.analyzer.getRatingFromScore(data.result.readability.score / 10),
                    results: [],
                }
            };

            for (const result of data.result.seo[''].results) {
                if (!result.text) {
                    continue;
                }
                result.rating = this.analyzer.getRatingFromScore(result.score);
                results.seo.results.push(result);
            }

            results.seo.results.sort((a, b) => {
                return a.score - b.score;
            });

            for (const result of data.result.readability.results) {
                if (!result.text) {
                    continue;
                }
                result.rating = this.analyzer.getRatingFromScore(result.score);
                results.readability.results.push(result);
            }

            results.readability.results.sort((a, b) => {
                return a.score - b.score;
            });

            this.renderer.render(results);
        });
    }

    addEditorEvent() {

        if (!this.options.editor) {
            return;
        }

        let interval;

        interval = window.setInterval(() => {
            if (this.editor) {
                if (typeof this.editor.instance.on === 'function') {
                    this.editor.instance.on('change', () => {
                        this.analyze();
                    });
                }
                window.clearInterval(interval);
                this.analyze();
                return;
            }
            this.editor = JoomlaEditor.get(this.extensionFields.text.id);
        }, 1000);
    }

    updateSearchPreview() {

        const preview = document.querySelector('.route66-search-preview');

        if (!preview) {
            return;
        }

        preview.querySelector('.route66-search-preview-title').textContent = this.title;
        preview.querySelector('.route66-search-preview-url').textContent = this.permalink;

        let description = '';

        if (this.description) {
            description = this.description;
        } else {
            description = this.stripHtml(this.text);
        }

        if (description.length > 160) {
            description = description.substring(0, 160) + '...';
        }

        preview.querySelector('.route66-search-preview-description').textContent = description;
    }


    async getPermalinkData() {

        this.permalink = '';
        this.menuTitle = '';
        this.menuDescription = '';

        if (this.options.permalink) {
            this.permalink = this.options.permalink;
            return;
        }

        const url = Joomla.getOptions('system.paths').rootFull + 'administrator/index.php?option=com_route66&task=uri.build&format=json';

        const payload = this.routeVars;

        for (const key in this.routeFields) {
            payload[key] = this.routeFields[key].value;
        }

        try {

            const response = await fetch(url, {
                method: 'POST',
                body: JSON.stringify(payload),
            });

            if (!response.ok) {
                throw new Error(`Response status: ${response.status}`);
            }

            const json = await response.json();

            this.permalink = json.uri;
            this.menuTitle = json.title;
            this.menuDescription = json.description;

        } catch (error) {
            console.error(error.message);
        }
    }

    stripHtml(html) {
        let doc = new DOMParser().parseFromString(html, 'text/html');
        return doc.body.textContent || '';
    }
}

export default Route66Analyzer;