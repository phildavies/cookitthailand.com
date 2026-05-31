class Route66AnalyzerResults {

    constructor() {
        this.options = Joomla.getOptions('route66');
        this.setup();
    }


    setup() {
        this.resultTemplate = document.querySelector('#route66-analyzer-result-template');
        this.seoResultsContainer = document.querySelector('#route66-analyzer-seo-results');
        this.readabilityResults = document.querySelector('#route66-analyzer-readability-results');
        this.seoBadges = document.querySelectorAll('.route66-seo-badge');
        this.readabilityBadges = document.querySelectorAll('.route66-readability-badge');
        this.seoScoreField = document.querySelector('.route66-analyzer-seo-score');
        this.readbilityScoreField = document.querySelector('.route66-analyzer-readability-score');
    }


    render(data) {
        this.renderResults(data.seo.results, this.seoResultsContainer);
        this.renderBadges(data.seo.rating, data.seo.score, this.seoBadges);
        this.renderResults(data.readability.results, this.readabilityResults);
        this.renderBadges(data.readability.rating, data.readability.score, this.readabilityBadges);
        this.seoScoreField.value = data.seo.score;
        this.readbilityScoreField.value = data.readability.score;
    }

    renderBadges(rating, score, badges) {
        badges.forEach((badge) => {
            badge.innerHTML = '<i class="fa-solid ' + this.getIconFromRating(rating) + '"></i>';
            badge.classList.remove('ok', 'bad', 'good');
            badge.classList.add(rating);
        });
    }

    renderResults(results, container) {

        container.innerHTML = '';

        const div = document.createElement('div');

        for (const result of results) {
            const row = this.resultTemplate.content.cloneNode(true);
            row.querySelector('.route66-analyzer-result-text').innerHTML = result.text;
            row.querySelector('.route66-analyzer-result-icon').classList.remove('ok', 'bad', 'good');
            row.querySelector('.route66-analyzer-result-icon').classList.add(result.rating);
            div.appendChild(row);
        }

        this.removeLinks(div);

        container.appendChild(div);
    }


    getIconFromRating(rating) {

        let icon = '';

        if (rating === 'good') {
            icon = 'fa-circle-check';
        } else if (rating === 'ok') {
            icon = 'fa-circle-exclamation';
        } else {
            icon = 'fa-circle-xmark';
        }

        return icon;
    }


    removeLinks(container) {
        const links = container.querySelectorAll('a');
        for (const link of links) {
            link.outerHTML = link.innerHTML;
        }
    }
}

export default Route66AnalyzerResults;


