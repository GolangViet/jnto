(function () {
    const form = document.getElementById('survey-form');
    const nextButton = document.getElementById('survey-next');
    const panels = Array.from(document.querySelectorAll('[data-survey-panel]'));
    const surveySection = document.querySelector('.survey-section');

    if (!form || !nextButton) {
        return;
    }

    const templates = {
        region: document.getElementById('region-options-template'),
        travelStyle: document.getElementById('travel-style-options-template'),
        information: document.getElementById('information-options-template')
    };

    function populateOptions(selector, template, namePrefix) {
        if (!template) return;
        document.querySelectorAll(selector).forEach(function (container, index) {
            const content = template.content.cloneNode(true);
            content.querySelectorAll('input:not(.survey-other-input)').forEach(function (input) {
                input.name = namePrefix + '_' + (index + 1);
            });
            content.querySelectorAll('.survey-other-input').forEach(function (input) {
                input.name = namePrefix + '_other_' + (index + 1);
            });
            container.appendChild(content);
        });
    }

    populateOptions('[data-region-options]', templates.region, 'japan_regions');
    populateOptions('[data-travel-style-options]', templates.travelStyle, 'travel_style');
    populateOptions('[data-information-options]', templates.information, 'information_sources');

    function showPanel(panelName) {
        panels.forEach(function (panel) {
            const isActive = panel.dataset.surveyPanel === panelName;
            panel.hidden = !isActive;
            panel.classList.toggle('is-active', isActive);
        });
        surveySection.classList.toggle('survey-section--mid', panelName === '3-2-a');
        surveySection.classList.toggle('survey-section--large', panelName === '3-2-b');
        form.classList.remove('was-validated');
        surveySection.scrollIntoView({
            behavior: 'smooth',
            block: 'start'
        });
    }

    nextButton.addEventListener('click', function () {
        const introPanel = form.querySelector('[data-survey-panel="intro"]');
        const requiredInputs = Array.from(introPanel.querySelectorAll('[required]'));
        const isValid = requiredInputs.every(function (input) {
            return input.checkValidity();
        });

        if (!isValid) {
            form.classList.add('was-validated');
            const firstInvalid = requiredInputs.find(function (input) {
                return !input.checkValidity();
            });
            firstInvalid.reportValidity();
            return;
        }

        const selectedExperience = form.querySelector('input[name="travel_experience"]:checked') || form.querySelector('input[data-travel-status]:checked');
        if (selectedExperience) {
            showPanel(selectedExperience.dataset.travelStatus);
            showPanel(selectedExperience.dataset.surveyTarget);
        }
    });

    form.querySelectorAll('[data-survey-prev]').forEach(function (button) {
        button.addEventListener('click', function () {
            showPanel(button.dataset.surveyPrev);
        });
    });

    form.addEventListener('submit', function (event) {
        event.preventDefault();
        window.location.href = '/take-questions';
    });
})();
