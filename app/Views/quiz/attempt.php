<?php
$remainingSeconds = null;
if ($attempt['duration_minutes']) {
    $started = strtotime($attempt['started_at']);
    $expiry = $started + ((int) $attempt['duration_minutes'] * 60);
    $remainingSeconds = max(0, $expiry - time());
}
?>
<style>
    .quiz-layout {
        display: grid;
        grid-template-columns: 1fr;
        gap: 24px;
        margin-bottom: 48px;
    }
    
    @media (min-width: 850px) {
        .quiz-layout {
            grid-template-columns: 1fr 300px;
            align-items: start;
        }
    }

    /* Navigation grid styling */
    .nav-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(48px, 1fr));
        gap: 10px;
        margin-top: 18px;
    }

    .nav-badge {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 100%;
        aspect-ratio: 1;
        border-radius: 12px;
        font-weight: 700;
        font-size: 0.95rem;
        cursor: pointer;
        transition: var(--transition);
        border: 2px solid var(--border);
        background: #f8fafc;
        color: var(--text-muted);
        outline: none;
        padding: 0;
    }

    .nav-badge:hover {
        border-color: var(--primary);
        color: var(--primary);
        transform: translateY(-2px);
    }

    .nav-badge.active {
        border-color: var(--primary);
        background: var(--primary);
        color: #ffffff !important;
        box-shadow: 0 4px 12px rgba(79, 70, 229, 0.3);
    }

    .nav-badge.answered {
        background: var(--primary-light);
        border-color: var(--primary);
        color: var(--primary);
    }

    .nav-badge.unanswered {
        background: #f8fafc;
        border-color: var(--border);
        color: var(--text-muted);
    }

    /* Option item */
    .option-item {
        display: flex;
        align-items: center;
        padding: 16px 20px;
        border: 2px solid var(--border);
        border-radius: 14px;
        margin-bottom: 14px;
        cursor: pointer;
        font-weight: 500;
        transition: var(--transition);
        background: #ffffff;
        position: relative;
        overflow: hidden;
    }

    .option-item:hover {
        border-color: #cbd5e1;
        background: #f8fafc;
        transform: translateX(4px);
    }

    .option-item.selected {
        border-color: var(--primary);
        background: var(--primary-light);
        color: var(--primary);
    }

    .option-item input {
        width: auto;
        margin: 0 12px 0 0;
        flex-shrink: 0;
        accent-color: var(--primary);
    }

    /* Textarea answer styling */
    .open-text-input {
        width: 100%;
        min-height: 140px;
        padding: 16px;
        border: 2px solid var(--border);
        border-radius: 12px;
        font-size: 1rem;
        font-family: inherit;
        transition: var(--transition);
        box-sizing: border-box;
        resize: vertical;
        margin: 0;
    }

    .open-text-input:focus {
        border-color: var(--primary);
        outline: none;
        box-shadow: 0 0 0 4px var(--primary-light);
    }

    @keyframes questionFadeIn {
        from {
            opacity: 0;
            transform: translateY(8px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .fade-animation {
        animation: questionFadeIn 0.3s cubic-bezier(0.4, 0, 0.2, 1) forwards;
    }
</style>

<div class="card" style="margin-bottom: 24px; animation: fadeIn 0.3s ease-out; padding: 20px;">
    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px;">
        <div>
            <h1 style="margin: 0; font-size: 1.5rem; font-weight: 800; color: var(--text-main);"><?= e($attempt['quiz_title']) ?></h1>
            <span class="muted" style="margin-top: 4px; display: inline-block;">Candidate: <strong><?= e($attempt['user_name']) ?></strong></span>
        </div>
        
        <div style="display: flex; align-items: center; gap: 12px;">
            <div id="saving-indicator" style="font-size: 0.85rem; color: var(--text-muted); display: flex; align-items: center; gap: 6px;">
                <span style="display: inline-block; width: 8px; height: 8px; border-radius: 50%; background: #10b981;"></span>
                All answers saved
            </div>
            
            <?php if ($attempt['duration_minutes']): ?>
                <div id="timer-box" style="display: flex; align-items: center; gap: 8px; padding: 10px 16px; background: #1e293b; color: #fff; border-radius: 10px; font-weight: 700; font-size: 1.1rem; min-width: 100px; justify-content: center; transition: background 0.3s;">
                    <svg style="width: 20px; height: 20px;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span id="countdown">--:--</span>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Progress bar -->
    <div style="margin-top: 20px;">
        <div style="display: flex; justify-content: space-between; font-size: 0.8rem; font-weight: 600; color: var(--text-muted); margin-bottom: 6px;">
            <span>PROGRESS</span>
            <span id="progress-text">0% Completed</span>
        </div>
        <div style="width: 100%; height: 6px; background: var(--border); border-radius: 9999px; overflow: hidden;">
            <div id="progress-bar" style="width: 0%; height: 100%; background: var(--primary); transition: width 0.3s ease-out;"></div>
        </div>
    </div>
</div>

<div class="quiz-layout">
    <!-- Left column: Question body & controls -->
    <div>
        <!-- Question Display Card -->
        <div class="card" id="question-card" style="padding: 32px; min-height: 250px; position: relative; animation: fadeIn 0.4s ease-out;">
            <div id="question-loading" style="text-align: center; padding: 48px; color: var(--text-muted); display: none;">
                Loading question...
            </div>
            
            <div id="question-body">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px;">
                    <span id="question-number-badge" style="font-size: 0.85rem; font-weight: 700; color: var(--primary); background: var(--primary-light); padding: 4px 12px; border-radius: 9999px; text-transform: uppercase;">Question 1</span>
                    <span id="question-score-badge" class="muted">1.0 Point</span>
                </div>
                
                <h2 id="question-text" style="font-size: 1.35rem; font-weight: 700; margin: 0 0 24px; color: var(--text-main); line-height: 1.5;">Question Text</h2>
                
                <!-- Render Option Inputs Container -->
                <div id="answers-input-container" style="margin-bottom: 24px;">
                    <!-- Dynamically populated options (radios/checkboxes/textareas) -->
                </div>
            </div>
        </div>

        <!-- Navigation Controls -->
        <div style="display: flex; justify-content: space-between; align-items: center; gap: 16px; animation: fadeIn 0.4s ease-out;">
            <button onclick="prevQuestion()" id="prev-btn" class="btn" style="background: #94a3b8; padding: 12px 24px; font-weight: 700;">
                ← Previous
            </button>
            
            <div style="display: flex; gap: 12px; margin-left: auto;">
                <button onclick="nextQuestion()" id="next-btn" class="btn" style="padding: 12px 24px; font-weight: 700;">
                    Next →
                </button>
                <button onclick="confirmSubmit()" id="submit-btn" class="btn danger" style="padding: 12px 30px; font-weight: 700;">
                    Submit Quiz
                </button>
            </div>
        </div>
    </div>

    <!-- Right column: Sidebar navigation panel -->
    <div>
        <div class="card" style="padding: 24px;">
            <h3 style="margin: 0 0 16px; font-size: 1.1rem; font-weight: 700; color: var(--text-main); border-bottom: 1px solid var(--border); padding-bottom: 12px;">Question Map</h3>
            
            <div class="nav-grid" id="question-nav-grid">
                <!-- Badges dynamically generated here -->
            </div>
            
            <div style="margin-top: 24px; border-top: 1px solid var(--border); padding-top: 16px; font-size: 0.85rem; display: flex; flex-direction: column; gap: 10px;">
                <div style="display: flex; align-items: center; gap: 8px;">
                    <span style="display: inline-block; width: 14px; height: 14px; border-radius: 4px; background: var(--primary); box-shadow: 0 2px 4px rgba(79, 70, 229, 0.2);"></span>
                    <span style="font-weight: 600; color: var(--text-main);">Current active question</span>
                </div>
                <div style="display: flex; align-items: center; gap: 8px;">
                    <span style="display: inline-block; width: 14px; height: 14px; border-radius: 4px; background: var(--primary-light); border: 2px solid var(--primary);"></span>
                    <span style="font-weight: 500; color: var(--text-muted);">Answered / saved</span>
                </div>
                <div style="display: flex; align-items: center; gap: 8px;">
                    <span style="display: inline-block; width: 14px; height: 14px; border-radius: 4px; background: #f8fafc; border: 2px solid var(--border);"></span>
                    <span style="font-weight: 500; color: var(--text-muted);">Unanswered</span>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Quiz taking state variables
const attemptId = <?= (int)$attempt['id'] ?>;
let remainingSeconds = <?= $remainingSeconds !== null ? (int)$remainingSeconds : 'null' ?>;

const questions = <?= json_encode($questions) ?>;
const savedAnswers = <?= json_encode($savedAnswers) ?>;

let currentIdx = 0;
// Map question ID to user answers
const userAnswers = {};
const triggerOptionsMap = {};

// Initialize state
questions.forEach(q => {
    userAnswers[q.id] = {
        question_id: q.id,
        selected_option_ids: [],
        answer_text: '',
        option_custom_texts: {}
    };

    // Build trigger options map
    if (q.options) {
        q.options.forEach(opt => {
            if (opt.related_question_ids && opt.related_question_ids.length > 0) {
                opt.related_question_ids.forEach(childId => {
                    if (!triggerOptionsMap[childId]) {
                        triggerOptionsMap[childId] = [];
                    }
                    if (!triggerOptionsMap[childId].includes(opt.id)) {
                        triggerOptionsMap[childId].push(opt.id);
                    }
                });
            }
        });
    }
});

// Load saved answers
savedAnswers.forEach(ans => {
    const qId = parseInt(ans.question_id);
    if (userAnswers[qId]) {
        userAnswers[qId].answer_text = ans.answer_text || '';
        userAnswers[qId].selected_option_ids = ans.selected_option_ids || [];
        userAnswers[qId].option_custom_texts = ans.option_custom_texts || {};
    }
});

function isQuestionVisible(qId) {
    const triggerOptionIds = triggerOptionsMap[qId];
    if (!triggerOptionIds || triggerOptionIds.length === 0) {
        return true;
    }
    return triggerOptionIds.some(optId => {
        let isParentOptionSelected = false;
        questions.forEach(parentQ => {
            const ans = userAnswers[parentQ.id];
            if (ans && ans.selected_option_ids.includes(optId)) {
                isParentOptionSelected = true;
            }
        });
        return isParentOptionSelected;
    });
}

function adjustCurrentIdx() {
    if (!isQuestionVisible(questions[currentIdx].id)) {
        let newIdx = currentIdx;
        while (newIdx >= 0) {
            if (isQuestionVisible(questions[newIdx].id)) {
                currentIdx = newIdx;
                return;
            }
            newIdx--;
        }
        newIdx = currentIdx;
        while (newIdx < questions.length) {
            if (isQuestionVisible(questions[newIdx].id)) {
                currentIdx = newIdx;
                return;
            }
            newIdx++;
        }
        currentIdx = 0;
    }
}

// DOM Elements
const qNumBadge = document.getElementById('question-number-badge');
const qScoreBadge = document.getElementById('question-score-badge');
const qText = document.getElementById('question-text');
const inputsContainer = document.getElementById('answers-input-container');

const prevBtn = document.getElementById('prev-btn');
const nextBtn = document.getElementById('next-btn');
const submitBtn = document.getElementById('submit-btn');

const savingIndicator = document.getElementById('saving-indicator');
const progressBar = document.getElementById('progress-bar');
const progressText = document.getElementById('progress-text');

document.addEventListener('DOMContentLoaded', () => {
    renderQuestion();
    startCountdown();
    updateProgress();
});

function renderQuestion() {
    if (questions.length === 0) return;
    
    adjustCurrentIdx();
    
    const visibleQuestions = questions.filter(q => isQuestionVisible(q.id));
    const displayIdx = visibleQuestions.indexOf(questions[currentIdx]);
    
    const qBody = document.getElementById('question-body');
    if (qBody) {
        qBody.classList.remove('fade-animation');
        void qBody.offsetWidth; // Trigger reflow to restart CSS animation
        qBody.classList.add('fade-animation');
    }
    
    const q = questions[currentIdx];
    
    // Update header
    qNumBadge.innerText = `Question ${displayIdx + 1} of ${visibleQuestions.length}`;
    qScoreBadge.innerText = `${parseFloat(q.score)} ${parseFloat(q.score) > 1 ? 'Points' : 'Point'}`;
    qText.innerText = q.question_text;
    
    // Render input based on type
    inputsContainer.innerHTML = '';
    const currentAnswer = userAnswers[q.id];

    if (q.type === 'single_choice' || q.type === 'true_false') {
        q.options.forEach(opt => {
            const wrapper = document.createElement('div');
            wrapper.style.marginBottom = '14px';

            const label = document.createElement('label');
            label.className = 'option-item';
            label.style.marginBottom = '0';

            const isChecked = currentAnswer.selected_option_ids.includes(opt.id);
            if (isChecked) {
                label.classList.add('selected');
            }

            label.innerHTML = `
                <input type="radio" name="question_option" value="${opt.id}" ${isChecked ? 'checked' : ''} onchange="saveSelectedRadio(${q.id}, ${opt.id}, this)">
                <span>${opt.option_key ? `<strong>${escapeHtml(opt.option_key)}.</strong> ` : ''}${escapeHtml(opt.option_text)}</span>
            `;
            wrapper.appendChild(label);

            if (opt.allow_custom_text && isChecked) {
                const textContainer = document.createElement('div');
                textContainer.style.padding = '8px 20px';
                textContainer.style.background = '#f8fafc';
                textContainer.style.border = '2px solid var(--primary-light)';
                textContainer.style.borderTop = 'none';
                textContainer.style.borderBottomLeftRadius = '14px';
                textContainer.style.borderBottomRightRadius = '14px';

                const input = document.createElement('input');
                input.type = 'text';
                input.placeholder = 'Please specify...';
                input.value = currentAnswer.option_custom_texts[opt.id] || '';
                input.style.width = '100%';
                input.style.padding = '8px 12px';
                input.style.border = '1px solid var(--border)';
                input.style.borderRadius = '8px';
                input.style.margin = '0';
                
                input.oninput = (e) => debounce(() => saveOptionCustomText(q.id, opt.id, e.target.value), 600)();
                
                textContainer.appendChild(input);
                wrapper.appendChild(textContainer);
                
                label.style.borderBottomLeftRadius = '0';
                label.style.borderBottomRightRadius = '0';
            }

            inputsContainer.appendChild(wrapper);
        });
    } else if (q.type === 'multiple_choice') {
        q.options.forEach(opt => {
            const wrapper = document.createElement('div');
            wrapper.style.marginBottom = '14px';

            const label = document.createElement('label');
            label.className = 'option-item';
            label.style.marginBottom = '0';

            const isChecked = currentAnswer.selected_option_ids.includes(opt.id);
            if (isChecked) {
                label.classList.add('selected');
            }

            label.innerHTML = `
                <input type="checkbox" name="question_option" value="${opt.id}" ${isChecked ? 'checked' : ''} onchange="saveSelectedCheckbox(${q.id}, ${opt.id}, this)">
                <span>${opt.option_key ? `<strong>${escapeHtml(opt.option_key)}.</strong> ` : ''}${escapeHtml(opt.option_text)}</span>
            `;
            wrapper.appendChild(label);

            if (opt.allow_custom_text && isChecked) {
                const textContainer = document.createElement('div');
                textContainer.style.padding = '8px 20px';
                textContainer.style.background = '#f8fafc';
                textContainer.style.border = '2px solid var(--primary-light)';
                textContainer.style.borderTop = 'none';
                textContainer.style.borderBottomLeftRadius = '14px';
                textContainer.style.borderBottomRightRadius = '14px';

                const input = document.createElement('input');
                input.type = 'text';
                input.placeholder = 'Please specify...';
                input.value = currentAnswer.option_custom_texts[opt.id] || '';
                input.style.width = '100%';
                input.style.padding = '8px 12px';
                input.style.border = '1px solid var(--border)';
                input.style.borderRadius = '8px';
                input.style.margin = '0';
                
                input.oninput = (e) => debounce(() => saveOptionCustomText(q.id, opt.id, e.target.value), 600)();
                
                textContainer.appendChild(input);
                wrapper.appendChild(textContainer);
                
                label.style.borderBottomLeftRadius = '0';
                label.style.borderBottomRightRadius = '0';
            }

            inputsContainer.appendChild(wrapper);
        });
    } else if (q.type === 'open_text') {
        const textarea = document.createElement('textarea');
        textarea.className = 'open-text-input';
        textarea.rows = 4;
        textarea.placeholder = 'Type your answer here...';
        textarea.value = currentAnswer.answer_text;
        textarea.oninput = (e) => debounce(() => saveOpenTextAnswer(q.id, e.target.value), 600)();
        inputsContainer.appendChild(textarea);
    }

    // Toggle nav buttons
    const isFirstVisible = currentIdx === questions.indexOf(visibleQuestions[0]);
    const isLastVisible = currentIdx === questions.indexOf(visibleQuestions[visibleQuestions.length - 1]);

    prevBtn.disabled = isFirstVisible;
    prevBtn.style.opacity = isFirstVisible ? '0.5' : '1';
    prevBtn.style.cursor = isFirstVisible ? 'not-allowed' : 'pointer';

    if (isLastVisible) {
        nextBtn.style.display = 'none';
        submitBtn.style.display = 'inline-block';
    } else {
        nextBtn.style.display = 'inline-block';
        submitBtn.style.display = 'none';
    }

    renderNavigationGrid();
}

function renderNavigationGrid() {
    const grid = document.getElementById('question-nav-grid');
    if (!grid) return;
    grid.innerHTML = '';
    
    const visibleQuestions = questions.filter(q => isQuestionVisible(q.id));
    
    visibleQuestions.forEach((q, idx) => {
        const btn = document.createElement('button');
        btn.type = 'button';
        btn.className = 'nav-badge';
        btn.innerText = idx + 1;
        btn.onclick = () => jumpToQuestion(questions.indexOf(q));
        
        // Determine status
        const isCurrent = questions.indexOf(q) === currentIdx;
        const ans = userAnswers[q.id];
        const hasAnswered = ans && (ans.selected_option_ids.length > 0 || (ans.answer_text && ans.answer_text.trim() !== ''));
        
        if (isCurrent) {
            btn.classList.add('active');
        } else if (hasAnswered) {
            btn.classList.add('answered');
        } else {
            btn.classList.add('unanswered');
        }
        
        grid.appendChild(btn);
    });
}

function jumpToQuestion(idx) {
    if (idx >= 0 && idx < questions.length && isQuestionVisible(questions[idx].id)) {
        currentIdx = idx;
        renderQuestion();
    }
}

// Answer Save handlers
function saveSelectedRadio(questionId, optionId, inputEl) {
    userAnswers[questionId].selected_option_ids = [optionId];
    userAnswers[questionId].answer_text = '';
    
    // Clean other option custom texts
    const newCustomTexts = {};
    if (userAnswers[questionId].option_custom_texts[optionId]) {
        newCustomTexts[optionId] = userAnswers[questionId].option_custom_texts[optionId];
    }
    userAnswers[questionId].option_custom_texts = newCustomTexts;
    
    sendAnswerToServer(questionId);
    updateProgress();
    renderQuestion(); // Re-render to show custom text field immediately
}

function saveSelectedCheckbox(questionId, optionId, inputEl) {
    const isChecked = inputEl.checked;

    if (isChecked) {
        if (!userAnswers[questionId].selected_option_ids.includes(optionId)) {
            userAnswers[questionId].selected_option_ids.push(optionId);
        }
    } else {
        userAnswers[questionId].selected_option_ids = userAnswers[questionId].selected_option_ids.filter(id => id !== optionId);
        delete userAnswers[questionId].option_custom_texts[optionId];
    }
    
    userAnswers[questionId].answer_text = '';
    sendAnswerToServer(questionId);
    updateProgress();
    renderQuestion();
}

function saveOptionCustomText(questionId, optionId, text) {
    userAnswers[questionId].option_custom_texts[optionId] = text;
    sendAnswerToServer(questionId);
}

function saveOpenTextAnswer(questionId, text) {
    userAnswers[questionId].answer_text = text;
    userAnswers[questionId].selected_option_ids = [];
    
    sendAnswerToServer(questionId);
    updateProgress();
    renderNavigationGrid();
}

async function sendAnswerToServer(questionId) {
    showSaving();
    const ans = userAnswers[questionId];
    
    try {
        const response = await fetch(`/api/quiz-attempts/${attemptId}/answers`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(ans)
        });
        if (response.ok) {
            showSaved();
        } else {
            showErrorSaving();
        }
    } catch (err) {
        showErrorSaving();
    }
}

// Navigation Actions
function nextQuestion() {
    const visibleQuestions = questions.filter(q => isQuestionVisible(q.id));
    const currentVisibleIdx = visibleQuestions.indexOf(questions[currentIdx]);
    if (currentVisibleIdx < visibleQuestions.length - 1) {
        const nextVisibleQ = visibleQuestions[currentVisibleIdx + 1];
        currentIdx = questions.indexOf(nextVisibleQ);
        renderQuestion();
    }
}

function prevQuestion() {
    const visibleQuestions = questions.filter(q => isQuestionVisible(q.id));
    const currentVisibleIdx = visibleQuestions.indexOf(questions[currentIdx]);
    if (currentVisibleIdx > 0) {
        const prevVisibleQ = visibleQuestions[currentVisibleIdx - 1];
        currentIdx = questions.indexOf(prevVisibleQ);
        renderQuestion();
    }
}

// Update overall progress bar
function updateProgress() {
    const visibleQuestions = questions.filter(q => isQuestionVisible(q.id));
    let answered = 0;
    visibleQuestions.forEach(q => {
        const ans = userAnswers[q.id];
        if (ans.selected_option_ids.length > 0 || (ans.answer_text && ans.answer_text.trim() !== '')) {
            answered++;
        }
    });

    const percent = visibleQuestions.length > 0 ? Math.round((answered / visibleQuestions.length) * 100) : 0;
    progressBar.style.width = `${percent}%`;
    progressText.innerText = `${percent}% Completed (${answered} of ${visibleQuestions.length} answered)`;
}

// Timer/Countdown logic
function startCountdown() {
    if (remainingSeconds === null) return;
    
    const timerBox = document.getElementById('timer-box');
    const countdownEl = document.getElementById('countdown');
    
    updateTimerDisplay(remainingSeconds);
    
    const interval = setInterval(() => {
        remainingSeconds--;
        
        if (remainingSeconds <= 0) {
            clearInterval(interval);
            countdownEl.innerText = "00:00";
            timerBox.style.background = '#dc2626';
            alert('Time limit reached! Submitting your answers automatically.');
            submitQuiz(true); // Forced submit
            return;
        }

        updateTimerDisplay(remainingSeconds);
    }, 1000);
}

function updateTimerDisplay(totalSecs) {
    const timerBox = document.getElementById('timer-box');
    const countdownEl = document.getElementById('countdown');
    if (!timerBox || !countdownEl) return;

    const mins = Math.floor(totalSecs / 60);
    const secs = totalSecs % 60;
    countdownEl.innerText = `${String(mins).padStart(2, '0')}:${String(secs).padStart(2, '0')}`;

    if (totalSecs < 60) {
        timerBox.style.background = '#dc2626'; // Red color warning
    }
}

// Submission
function confirmSubmit() {
    const visibleQuestions = questions.filter(q => isQuestionVisible(q.id));
    // Check if unanswered questions exist
    let unansweredCount = 0;
    visibleQuestions.forEach(q => {
        const ans = userAnswers[q.id];
        if (ans.selected_option_ids.length === 0 && (!ans.answer_text || ans.answer_text.trim() === '')) {
            unansweredCount++;
        }
    });

    let msg = 'Are you sure you want to submit the quiz?';
    if (unansweredCount > 0) {
        msg = `You have ${unansweredCount} unanswered questions. Are you sure you want to submit the quiz?`;
    }

    if (confirm(msg)) {
        submitQuiz();
    }
}

async function submitQuiz(forced = false) {
    showSaving();
    
    try {
        const response = await fetch(`/api/quiz-attempts/${attemptId}/submit`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            }
        });
        
        if (response.ok) {
            const mainSurveyQuizId = <?= json_encode(setting('main_survey_quiz_id')) ?>;
            const attemptQuizId = <?= (int) $attempt['quiz_id'] ?>;
            if (mainSurveyQuizId && Number(mainSurveyQuizId) === attemptQuizId) {
                window.location.href = '/thank-you';
            } else {
                window.location.href = `/quiz-attempts/${attemptId}/result`;
            }
        } else {
            const res = await response.json();
            alert(res.message || 'Failed to submit quiz.');
        }
    } catch (err) {
        console.error(err);
        alert('An error occurred during submission.');
    }
}

// Saving indicator state managers
function showSaving() {
    savingIndicator.innerHTML = `
        <svg style="animation: spin 1s linear infinite; width: 14px; height: 14px; color: var(--text-muted);" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 1121.283 6H18" />
        </svg>
        <span>Saving...</span>
    `;
    // Add inline keyframes spin style once dynamically
    if (!document.getElementById('spin-keyframes')) {
        const style = document.createElement('style');
        style.id = 'spin-keyframes';
        style.innerText = `@keyframes spin { from { transform: rotate(0deg); } to { transform: rotate(360deg); } }`;
        document.head.appendChild(style);
    }
}

function showSaved() {
    savingIndicator.innerHTML = `
        <span style="display: inline-block; width: 8px; height: 8px; border-radius: 50%; background: #10b981;"></span>
        <span>All answers saved</span>
    `;
}

function showErrorSaving() {
    savingIndicator.innerHTML = `
        <span style="display: inline-block; width: 8px; height: 8px; border-radius: 50%; background: #ef4444;"></span>
        <span style="color: #ef4444;">Error auto-saving</span>
    `;
}

// Debounce helper
let timeoutId = null;
function debounce(func, delay) {
    return function(...args) {
        clearTimeout(timeoutId);
        timeoutId = setTimeout(() => func.apply(this, args), delay);
    };
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
