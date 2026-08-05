<style>
/* Tooltip Container */
.question-tooltip {
    position: relative;
    display: inline-flex;
    align-items: center;
    cursor: pointer;
    color: #9ca3af;
    transition: color 0.2s ease;
    vertical-align: middle;
}

.question-tooltip:hover {
    color: #4f46e5;
}

/* Tooltip text */
.question-tooltip .tooltiptext {
    visibility: hidden;
    width: 200px;
    background-color: #1f2937;
    color: #ffffff;
    text-align: center;
    border-radius: 6px;
    padding: 8px 12px;
    position: absolute;
    z-index: 50;
    bottom: 125%; /* Position above the icon */
    left: 50%;
    transform: translateX(-50%);
    opacity: 0;
    transition: opacity 0.2s ease, transform 0.2s ease;
    font-size: 0.75rem;
    font-weight: 500;
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
    pointer-events: none;
    white-space: normal;
}

/* Tooltip arrow */
.question-tooltip .tooltiptext::after {
    content: "";
    position: absolute;
    top: 100%; /* At the bottom of the tooltip */
    left: 50%;
    margin-left: -5px;
    border-width: 5px;
    border-style: solid;
    border-color: #1f2937 transparent transparent transparent;
}

/* Show the tooltip text when hovering */
.question-tooltip:hover .tooltiptext {
    visibility: visible;
    opacity: 1;
    transform: translateX(-50%) translateY(-2px);
}
</style>

<div style="margin-bottom: 24px; animation: fadeIn 0.3s ease-out;">
    <a href="/admin/quizzes" style="text-decoration: none; color: #4f46e5; font-weight: 600; display: inline-flex; align-items: center; gap: 4px; font-size: 0.9rem;">
        <svg style="width: 16px; height: 16px;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
        </svg>
        Back to Quizzes
    </a>
    <h1 style="margin: 8px 0 0; font-size: 2rem; font-weight: 700; color: #111827;">Question Builder: <?= e($quiz['title']) ?></h1>
    <p class="muted" style="margin: 4px 0 0;">Drag and drop questions to reorder. Click a question to edit, or use the form to add a new one.</p>
</div>

<div style="display: grid; grid-template-columns: 350px 1fr; gap: 24px; animation: fadeIn 0.4s ease-out;">
    <!-- SIDEBAR: Question list -->
    <div>
        <div class="card" style="padding: 16px;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px;">
                <h3 style="margin: 0; font-size: 1.1rem; font-weight: 700; color: #111827;">Questions</h3>
                <span id="question-count-badge" style="font-size: 0.8rem; background: #e0e7ff; color: #4f46e5; padding: 2px 8px; border-radius: 9999px; font-weight: 600;">0</span>
            </div>
            
            <div id="no-questions-placeholder" style="text-align: center; padding: 24px; color: #9ca3af; font-size: 0.9rem; display: none;">
                No questions yet. Use the editor to add your first question!
            </div>
            
            <ul id="questions-list" style="list-style: none; padding: 0; margin: 0; display: flex; flex-direction: column; gap: 8px;">
                <!-- Dynamically populated -->
            </ul>
            
            <button onclick="clearFormForNewQuestion()" class="btn" style="width: 100%; margin-top: 14px; background: #f3f4f6; color: #4b5563; border: 1px dashed #d1d5db; font-weight: 600;">
                + Add New Question
            </button>
        </div>
    </div>

    <!-- MAIN PANEL: Question form editor -->
    <div>
        <div class="card" style="padding: 24px;" id="editor-container">
            <h2 id="editor-title" style="margin-top: 0; margin-bottom: 20px; font-size: 1.3rem; font-weight: 700; color: #111827;">Add New Question</h2>
            
            <form id="question-form" onsubmit="saveQuestion(event)">
                <input type="hidden" id="edit-question-id" value="">

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 16px;">
                    <div>
                        <label for="q-type" style="font-weight: 600; color: #374151; font-size: 0.9rem;">Question Type</label>
                        <select id="q-type" onchange="handleTypeChange()" style="margin-top: 4px; margin-bottom: 0;">
                            <option value="single_choice">Single Choice</option>
                            <option value="multiple_choice">Multiple Choice</option>
                            <option value="open_text">Open Text</option>
                            <option value="true_false">True or False</option>
                        </select>
                    </div>
                    <div>
                        <label for="q-score" style="font-weight: 600; color: #374151; font-size: 0.9rem;">Score Weight</label>
                        <input type="number" step="0.5" id="q-score" value="1.0" min="0" style="margin-top: 4px; margin-bottom: 0;">
                    </div>
                </div>

                <div style="margin-bottom: 16px;">
                    <label for="q-text" style="font-weight: 600; color: #374151; font-size: 0.9rem; display: inline-flex; align-items: center; gap: 6px;">
                        Question Text *
                        <span class="question-tooltip">
                            <svg style="width: 16px; height: 16px;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <span class="tooltiptext">Can breakline by add \n</span>
                        </span>
                    </label>
                    <textarea id="q-text" rows="3" required placeholder="Enter the question text here..." style="margin-top: 4px; margin-bottom: 0;"></textarea>
                </div>

                <div style="margin-bottom: 16px;">
                    <label for="q-notes" style="font-weight: 600; color: #374151; font-size: 0.9rem;">Notes</label>
                    <textarea id="q-notes" rows="2" placeholder="Enter private notes or admin instructions..." style="margin-top: 4px; margin-bottom: 0;"></textarea>
                </div>

                <div style="margin-bottom: 16px;">
                    <label for="q-explanation" style="font-weight: 600; color: #374151; font-size: 0.9rem;">Explanation (Shown after submission)</label>
                    <textarea id="q-explanation" rows="2" placeholder="Explain why the correct answer is right..." style="margin-top: 4px; margin-bottom: 0;"></textarea>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 24px;">
                    <div style="display: flex; align-items: center; height: 100%;">
                        <label style="display: inline-flex; align-items: center; gap: 8px; cursor: pointer;">
                            <input type="checkbox" id="q-required" checked style="width: auto; margin: 0;">
                            Required question
                        </label>
                    </div>
                    <div>
                        <label for="q-display-order" style="font-weight: 600; color: #374151; font-size: 0.9rem;">Display Order</label>
                        <input type="number" id="q-display-order" value="0" min="0" style="margin-top: 4px; margin-bottom: 0;">
                    </div>
                </div>

                <!-- DYNAMIC AREA: Options Editor (Choice / True False) -->
                <div id="choice-options-section" style="border-top: 1px solid #e5e7eb; padding-top: 20px; margin-bottom: 24px; display: none;">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px;">
                        <h4 style="margin: 0; font-size: 1rem; font-weight: 600; color: #374151;">Answer Options</h4>
                        <button type="button" onclick="addOptionRow()" id="add-opt-btn" class="btn" style="background: #10b981; font-size: 0.8rem; padding: 6px 12px;">+ Add Option</button>
                    </div>
                    
                    <div id="options-container" style="display: flex; flex-direction: column; gap: 8px;">
                        <!-- Dynamically generated rows -->
                    </div>
                </div>

                <!-- DYNAMIC AREA: Accepted Answers Editor (Open Text) -->
                <div id="open-text-section" style="border-top: 1px solid #e5e7eb; padding-top: 20px; margin-bottom: 24px; display: none;">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px;">
                        <h4 style="margin: 0; font-size: 1rem; font-weight: 600; color: #374151;">Accepted Answers</h4>
                        <button type="button" onclick="addAcceptedAnswerRow()" class="btn" style="background: #10b981; font-size: 0.8rem; padding: 6px 12px;">+ Add Answer</button>
                    </div>
                    
                    <div id="accepted-answers-container" style="display: flex; flex-direction: column; gap: 8px;">
                        <!-- Dynamically generated rows -->
                    </div>
                </div>

                <div style="border-top: 1px solid #e5e7eb; padding-top: 20px; display: flex; justify-content: space-between;">
                    <button type="button" onclick="deleteActiveQuestion()" id="delete-q-btn" class="btn danger" style="display: none;">Delete Question</button>
                    <div style="display: flex; gap: 12px; margin-left: auto;">
                        <button type="button" onclick="clearFormForNewQuestion()" class="btn" style="background: #9ca3af;">Clear</button>
                        <button type="submit" name="submit_action" value="save" class="btn" style="background: #4f46e5;">Save Question</button>
                        <button type="button" onclick="saveAndAddAnother()" id="save-another-btn" class="btn" style="background: #10b981;">Save and Add Another</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
const quizId = <?= (int)$quiz['id'] ?>;
let questions = [];

// DOM elements
const questionsList = document.getElementById('questions-list');
const noQuestionsPlaceholder = document.getElementById('no-questions-placeholder');
const countBadge = document.getElementById('question-count-badge');
const qForm = document.getElementById('question-form');
const editorTitle = document.getElementById('editor-title');
const editIdInput = document.getElementById('edit-question-id');

const qType = document.getElementById('q-type');
const qScore = document.getElementById('q-score');
const qText = document.getElementById('q-text');
const qExplanation = document.getElementById('q-explanation');
const qRequired = document.getElementById('q-required');
const qDisplayOrder = document.getElementById('q-display-order');
const qNotes = document.getElementById('q-notes');

const choiceSection = document.getElementById('choice-options-section');
const optionsContainer = document.getElementById('options-container');
const addOptBtn = document.getElementById('add-opt-btn');

const openTextSection = document.getElementById('open-text-section');
const acceptedContainer = document.getElementById('accepted-answers-container');

const deleteQBtn = document.getElementById('delete-q-btn');
const saveAnotherBtn = document.getElementById('save-another-btn');

// Initial Load
document.addEventListener('DOMContentLoaded', () => {
    fetchQuestions();
    handleTypeChange();
});

async function fetchQuestions() {
    try {
        const response = await fetch(`/api/admin/quizzes/${quizId}/questions`);
        questions = await response.json();
        renderQuestionsList();
    } catch (err) {
        console.error('Error fetching questions:', err);
    }
}

function renderQuestionsList() {
    questionsList.innerHTML = '';
    countBadge.innerText = questions.length;
    
    if (questions.length === 0) {
        noQuestionsPlaceholder.style.display = 'block';
        return;
    }
    
    noQuestionsPlaceholder.style.display = 'none';
    
    questions.forEach((q, index) => {
        const li = document.createElement('li');
        li.className = 'card';
        li.style.padding = '12px';
        li.style.margin = '0';
        li.style.display = 'flex';
        li.style.alignItems = 'center';
        li.style.justifyContent = 'space-between';
        li.style.cursor = 'grab';
        li.style.border = '1px solid #e5e7eb';
        li.draggable = true;
        
        // Drag and drop event listeners
        li.addEventListener('dragstart', (e) => handleDragStart(e, index));
        li.addEventListener('dragover', (e) => handleDragOver(e));
        li.addEventListener('drop', (e) => handleDrop(e, index));
        li.addEventListener('dragend', () => handleDragEnd());

        let typeLabel = q.type.replace('_', ' ');
        typeLabel = typeLabel.charAt(0).toUpperCase() + typeLabel.slice(1);

        li.innerHTML = `
            <div style="display: flex; align-items: center; gap: 8px; width: calc(100% - 80px);" onclick="loadQuestionIntoEditor(${q.id})">
                <span class="muted" style="cursor: grab;">☰</span>
                <div style="overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                    <div style="font-weight: 600; color: #111827; font-size: 0.9rem;">${index + 1}. ${escapeHtml(q.question_text)}</div>
                    <div class="muted" style="font-size: 0.75rem;">${typeLabel} • Score: ${parseFloat(q.score)}</div>
                </div>
            </div>
            <div style="display: flex; gap: 6px;">
                <button type="button" onclick="loadQuestionIntoEditor(${q.id})" class="btn" style="background: #e5e7eb; color: #4b5563; padding: 4px 8px; font-size: 0.75rem;">Edit</button>
                <button type="button" onclick="deleteQuestion(${q.id})" class="btn danger" style="padding: 4px 8px; font-size: 0.75rem;">Del</button>
            </div>
        `;
        questionsList.appendChild(li);
    });
}

// Drag & Drop reordering logic
let dragSrcIndex = null;

function handleDragStart(e, index) {
    dragSrcIndex = index;
    e.target.style.opacity = '0.5';
}

function handleDragOver(e) {
    e.preventDefault();
}

async function handleDrop(e, index) {
    e.preventDefault();
    if (dragSrcIndex === null || dragSrcIndex === index) return;
    
    // Swap display orders in local questions array
    const draggedItem = questions[dragSrcIndex];
    questions.splice(dragSrcIndex, 1);
    questions.splice(index, 0, draggedItem);

    // Update display_order keys
    const reorderPayload = questions.map((q, idx) => {
        q.display_order = idx + 1;
        return { id: q.id, display_order: q.display_order };
    });

    renderQuestionsList();

    // Call API to save new ordering
    try {
        const response = await fetch(`/api/admin/quizzes/${quizId}/questions/reorder`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ questions: reorderPayload })
        });
        if (!response.ok) {
            alert('Failed to save new question order.');
        }
    } catch (err) {
        console.error(err);
        alert('An error occurred while reordering.');
    }
}

function handleDragEnd() {
    dragSrcIndex = null;
    fetchQuestions(); // Refresh list to sync correct display
}

// Helper to enable/disable inputs in a container to manage browser validation
function toggleInputs(container, enabled) {
    const inputs = container.querySelectorAll('input, select, textarea');
    inputs.forEach(input => {
        input.disabled = !enabled;
    });
}

// Dynamic fields toggle based on selection
function handleTypeChange() {
    const type = qType.value;
    if (type === 'single_choice' || type === 'multiple_choice') {
        choiceSection.style.display = 'block';
        openTextSection.style.display = 'none';
        addOptBtn.style.display = 'inline-block';
        
        // Add default two options if container is empty
        if (optionsContainer.children.length === 0) {
            addOptionRow();
            addOptionRow();
        }
    } else if (type === 'true_false') {
        choiceSection.style.display = 'block';
        openTextSection.style.display = 'none';
        addOptBtn.style.display = 'none'; // Lock adding options
        
        // Recreate exactly True and False options
        optionsContainer.innerHTML = '';
        addOptionRow('True', 'A');
        addOptionRow('False', 'B');
    } else if (type === 'open_text') {
        choiceSection.style.display = 'none';
        openTextSection.style.display = 'block';
        
        if (acceptedContainer.children.length === 0) {
            addAcceptedAnswerRow();
        }
    }

    // Toggle disabled status for inputs in choiceSection and openTextSection
    if (type === 'single_choice' || type === 'multiple_choice' || type === 'true_false') {
        toggleInputs(choiceSection, true);
        toggleInputs(openTextSection, false);
    } else if (type === 'open_text') {
        toggleInputs(choiceSection, false);
        toggleInputs(openTextSection, true);
    }
}

// Options Builder helpers
function addOptionRow(text = '', key = '', isCorrect = false, optionId = '', allowCustomText = false, relatedQuestionIds = []) {
    const div = document.createElement('div');
    div.className = 'option-row';
    div.dataset.optionId = optionId;
    div.style.display = 'flex';
    div.style.flexDirection = 'column';
    div.style.gap = '8px';
    div.style.padding = '12px';
    div.style.border = '1px solid #e5e7eb';
    div.style.borderRadius = '8px';
    div.style.background = '#f9fafb';
    div.style.marginBottom = '8px';

    const type = qType.value;
    const inputType = type === 'multiple_choice' ? 'checkbox' : 'radio';
    
    // Build options for related questions dropdown
    const currentQId = editIdInput.value ? parseInt(editIdInput.value) : null;
    let relatedOptionsHtml = '';
    questions.forEach((q, idx) => {
        if (currentQId && q.id === currentQId) return;
        const isSelected = relatedQuestionIds.includes(q.id) ? 'selected' : '';
        relatedOptionsHtml += `<option value="${q.id}" ${isSelected}>Q${idx + 1}: ${escapeHtml(q.question_text)}</option>`;
    });

    div.innerHTML = `
        <div style="display: flex; align-items: center; gap: 8px;">
            <input type="text" placeholder="Key (e.g. A, B)" class="opt-key" value="${key}" style="width: 80px; margin: 0;">
            <input type="text" placeholder="Option Text *" class="opt-text" value="${escapeHtml(text)}" required style="flex-grow: 1; margin: 0;">
            <label style="display: inline-flex; align-items: center; gap: 4px; margin: 0; cursor: pointer; white-space: nowrap;">
                <input type="${inputType}" name="is_correct_option" class="opt-correct" ${isCorrect ? 'checked' : ''} style="width: auto; margin: 0;">
                Correct
            </label>
            ${type !== 'true_false' ? `<button type="button" onclick="this.parentElement.parentElement.remove()" class="btn danger" style="padding: 8px 12px; margin: 0;">Remove</button>` : ''}
        </div>
        <div style="display: flex; align-items: center; gap: 16px; margin-top: 4px; padding-left: 88px; flex-wrap: wrap;">
            <label style="display: inline-flex; align-items: center; gap: 6px; cursor: pointer; margin: 0; font-size: 0.85rem; color: #4b5563;">
                <input type="checkbox" class="opt-allow-custom" ${allowCustomText ? 'checked' : ''} style="width: auto; margin: 0;">
                Allow text input when chosen
            </label>
            <div style="display: inline-flex; align-items: center; gap: 6px; font-size: 0.85rem; color: #4b5563;">
                <span>Show Related Questions:</span>
                <select class="opt-related-questions" multiple style="width: 250px; margin: 0; padding: 2px 4px; font-size: 0.8rem; height: 50px;">
                    ${relatedOptionsHtml}
                </select>
            </div>
        </div>
    `;
    optionsContainer.appendChild(div);
}

// Accepted Answers Builder helpers
function addAcceptedAnswerRow(text = '', matchType = 'exact', similarity = '0.85') {
    const div = document.createElement('div');
    div.className = 'accepted-answer-row';
    div.style.display = 'flex';
    div.style.alignItems = 'center';
    div.style.gap = '8px';
    div.style.marginBottom = '6px';

    div.innerHTML = `
        <input type="text" placeholder="Accepted answer text *" class="ans-text" value="${escapeHtml(text)}" required style="flex-grow: 1; margin: 0;">
        <select class="ans-match-type" onchange="toggleSimilarity(this)" style="width: 130px; margin: 0;">
            <option value="exact" ${matchType === 'exact' ? 'selected' : ''}>Exact</option>
            <option value="contains" ${matchType === 'contains' ? 'selected' : ''}>Contains</option>
            <option value="fuzzy" ${matchType === 'fuzzy' ? 'selected' : ''}>Fuzzy (Typo)</option>
        </select>
        <input type="number" step="0.05" min="0" max="1" placeholder="Sim (0.85)" class="ans-similarity" value="${similarity}" style="width: 90px; margin: 0; display: ${matchType === 'fuzzy' ? 'block' : 'none'};">
        <button type="button" onclick="this.parentElement.remove()" class="btn danger" style="padding: 8px 12px; margin: 0;">Remove</button>
    `;
    acceptedContainer.appendChild(div);
}

function toggleSimilarity(selectEl) {
    const simInput = selectEl.parentElement.querySelector('.ans-similarity');
    if (selectEl.value === 'fuzzy') {
        simInput.style.display = 'block';
    } else {
        simInput.style.display = 'none';
    }
}

// Load existing question into Editor
async function loadQuestionIntoEditor(id) {
    try {
        const response = await fetch(`/api/admin/questions/${id}`);
        if (!response.ok) throw new Error('Question not found');
        const q = await response.json();
        
        // Populate inputs
        editIdInput.value = q.id;
        qType.value = q.type;
        qScore.value = q.score;
        qText.value = q.question_text;
        qExplanation.value = q.explanation || '';
        qRequired.checked = q.is_required ? true : false;
        qDisplayOrder.value = q.display_order;
        qNotes.value = q.notes || '';

        editorTitle.innerText = `Edit Question #${q.id}`;
        deleteQBtn.style.display = 'inline-block';
        saveAnotherBtn.style.display = 'none';

        // Render dynamic parts
        optionsContainer.innerHTML = '';
        acceptedContainer.innerHTML = '';

        if (q.type === 'open_text') {
            q.accepted_answers.forEach(ans => {
                addAcceptedAnswerRow(ans.answer_text, ans.match_type, ans.similarity_threshold || '0.85');
            });
            handleTypeChange();
        } else {
            q.options.forEach((opt, idx) => {
                addOptionRow(opt.option_text, opt.option_key || '', opt.is_correct ? true : false, opt.id, opt.allow_custom_text ? true : false, opt.related_question_ids || []);
            });
            handleTypeChange();
        }
    } catch (err) {
        console.error(err);
        alert('Could not load question details.');
    }
}

function clearFormForNewQuestion() {
    editIdInput.value = '';
    editorTitle.innerText = 'Add New Question';
    deleteQBtn.style.display = 'none';
    saveAnotherBtn.style.display = 'inline-block';

    qText.value = '';
    qExplanation.value = '';
    qRequired.checked = true;
    qNotes.value = '';
    
    // Auto increment display order based on current list length
    qDisplayOrder.value = questions.length + 1;

    optionsContainer.innerHTML = '';
    acceptedContainer.innerHTML = '';
    handleTypeChange();
}

// Save Actions
async function saveQuestion(event) {
    if (event) event.preventDefault();

    const id = editIdInput.value;
    const type = qType.value;
    
    const payload = {
        type: type,
        score: parseFloat(qScore.value),
        question_text: qText.value,
        explanation: qExplanation.value,
        is_required: qRequired.checked ? 1 : 0,
        display_order: parseInt(qDisplayOrder.value),
        notes: qNotes.value,
        options: [],
        accepted_answers: []
    };

    if (inChoiceMode(type)) {
        const optionRows = optionsContainer.querySelectorAll('.option-row');
        optionRows.forEach((row, idx) => {
            const idVal = row.dataset.optionId;
            const key = row.querySelector('.opt-key').value;
            const text = row.querySelector('.opt-text').value;
            const isCorrect = row.querySelector('.opt-correct').checked;
            const allowCustomText = row.querySelector('.opt-allow-custom').checked;
            const relatedSelect = row.querySelector('.opt-related-questions');
            const relatedQuestionIds = Array.from(relatedSelect.selectedOptions).map(o => parseInt(o.value));
            
            payload.options.push({
                id: idVal ? parseInt(idVal) : null,
                option_key: key,
                option_text: text,
                is_correct: isCorrect,
                display_order: idx + 1,
                allow_custom_text: allowCustomText,
                related_question_ids: relatedQuestionIds
            });
        });
    } else if (type === 'open_text') {
        const acceptedRows = acceptedContainer.querySelectorAll('.accepted-answer-row');
        acceptedRows.forEach(row => {
            const text = row.querySelector('.ans-text').value;
            const matchType = row.querySelector('.ans-match-type').value;
            const similarity = row.querySelector('.ans-similarity').value;
            
            payload.accepted_answers.push({
                answer_text: text,
                match_type: matchType,
                similarity_threshold: matchType === 'fuzzy' && similarity !== '' ? parseFloat(similarity) : null
            });
        });
    }

    // Call save API
    const url = id ? `/api/admin/questions/${id}` : `/api/admin/quizzes/${quizId}/questions`;
    const method = id ? 'PUT' : 'POST';

    try {
        const response = await fetch(url, {
            method: method,
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(payload)
        });
        const res = await response.json();

        if (response.ok) {
            alert(id ? 'Question updated successfully.' : 'Question created successfully.');
            fetchQuestions();
            if (id) {
                clearFormForNewQuestion();
            } else {
                clearFormForNewQuestion();
            }
            return true;
        } else {
            if (res.errors) {
                // Collect validation messages
                let msg = '';
                for (const field in res.errors) {
                    msg += res.errors[field].join('\n') + '\n';
                }
                alert('Validation errors:\n' + msg);
            } else {
                alert(res.message || 'Failed to save question.');
            }
            return false;
        }
    } catch (err) {
        console.error(err);
        alert('An error occurred during save.');
        return false;
    }
}

async function saveAndAddAnother() {
    const success = await saveQuestion();
    if (success) {
        clearFormForNewQuestion();
    }
}

async function deleteActiveQuestion() {
    const id = editIdInput.value;
    if (!id) return;
    
    if (confirm('Are you sure you want to delete this question?')) {
        await deleteQuestion(parseInt(id));
        clearFormForNewQuestion();
    }
}

async function deleteQuestion(id) {
    try {
        const response = await fetch(`/api/admin/questions/${id}`, {
            method: 'DELETE'
        });
        if (response.ok) {
            alert('Question deleted successfully.');
            fetchQuestions();
        } else {
            const res = await response.json();
            alert(res.message || 'Failed to delete question.');
        }
    } catch (err) {
        console.error(err);
        alert('An error occurred during deletion.');
    }
}

// Helpers
function inChoiceMode(type) {
    return ['single_choice', 'multiple_choice', 'true_false'].includes(type);
}

function escapeHtml(text) {
    if (!text) return '';
    return text
        .replace(/&/g, "&amp;")
        .replace(/</g, "&lt;")
        .replace(/>/g, "&gt;")
        .replace(/"/g, "&quot;")
        .replace(/'/g, "&#039;");
}
</script>
