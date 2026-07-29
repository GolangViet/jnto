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
<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 48px; gap: 16px; animation: fadeIn 0.4s ease-out;">
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

<script>
// Quiz taking state variables
const attemptId = <?= (int)$attempt['id'] ?>;
const durationMinutes = <?= $attempt['duration_minutes'] ? (int)$attempt['duration_minutes'] : 'null' ?>;
const startedAt = new Date("<?= e($attempt['started_at']) ?>").getTime();

const questions = <?= json_encode($questions) ?>;
const savedAnswers = <?= json_encode($savedAnswers) ?>;

let currentIdx = 0;
// Map question ID to user answers
const userAnswers = {};

// Initialize state
questions.forEach(q => {
    userAnswers[q.id] = {
        question_id: q.id,
        selected_option_ids: [],
        answer_text: ''
    };
});

// Load saved answers
savedAnswers.forEach(ans => {
    const qId = parseInt(ans.question_id);
    if (userAnswers[qId]) {
        userAnswers[qId].answer_text = ans.answer_text || '';
        userAnswers[qId].selected_option_ids = ans.selected_option_ids || [];
    }
});

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
    
    const q = questions[currentIdx];
    
    // Update header
    qNumBadge.innerText = `Question ${currentIdx + 1} of ${questions.length}`;
    qScoreBadge.innerText = `${parseFloat(q.score)} ${parseFloat(q.score) > 1 ? 'Points' : 'Point'}`;
    qText.innerText = q.question_text;
    
    // Render input based on type
    inputsContainer.innerHTML = '';
    const currentAnswer = userAnswers[q.id];

    if (q.type === 'single_choice' || q.type === 'true_false') {
        q.options.forEach(opt => {
            const label = document.createElement('label');
            label.style.display = 'flex';
            label.style.alignItems = 'center';
            label.style.padding = '14px 18px';
            label.style.border = '1px solid var(--border)';
            label.style.borderRadius = '12px';
            label.style.marginBottom = '12px';
            label.style.cursor = 'pointer';
            label.style.fontWeight = '500';
            label.style.transition = 'var(--transition)';
            label.style.background = '#ffffff';

            const isChecked = currentAnswer.selected_option_ids.includes(opt.id);
            if (isChecked) {
                label.style.borderColor = 'var(--primary)';
                label.style.background = 'var(--primary-light)';
            }

            label.innerHTML = `
                <input type="radio" name="question_option" value="${opt.id}" ${isChecked ? 'checked' : ''} style="width: auto; margin: 0 12px 0 0;" onchange="saveSelectedRadio(${q.id}, ${opt.id}, this)">
                <span>${opt.option_key ? `<strong>${escapeHtml(opt.option_key)}.</strong> ` : ''}${escapeHtml(opt.option_text)}</span>
            `;
            inputsContainer.appendChild(label);
        });
    } else if (q.type === 'multiple_choice') {
        q.options.forEach(opt => {
            const label = document.createElement('label');
            label.style.display = 'flex';
            label.style.alignItems = 'center';
            label.style.padding = '14px 18px';
            label.style.border = '1px solid var(--border)';
            label.style.borderRadius = '12px';
            label.style.marginBottom = '12px';
            label.style.cursor = 'pointer';
            label.style.fontWeight = '500';
            label.style.transition = 'var(--transition)';
            label.style.background = '#ffffff';

            const isChecked = currentAnswer.selected_option_ids.includes(opt.id);
            if (isChecked) {
                label.style.borderColor = 'var(--primary)';
                label.style.background = 'var(--primary-light)';
            }

            label.innerHTML = `
                <input type="checkbox" name="question_option" value="${opt.id}" ${isChecked ? 'checked' : ''} style="width: auto; margin: 0 12px 0 0;" onchange="saveSelectedCheckbox(${q.id}, ${opt.id}, this)">
                <span>${opt.option_key ? `<strong>${escapeHtml(opt.option_key)}.</strong> ` : ''}${escapeHtml(opt.option_text)}</span>
            `;
            inputsContainer.appendChild(label);
        });
    } else if (q.type === 'open_text') {
        const textarea = document.createElement('textarea');
        textarea.rows = 4;
        textarea.placeholder = 'Type your answer here...';
        textarea.value = currentAnswer.answer_text;
        textarea.style.margin = '0';
        textarea.style.width = '100%';
        textarea.oninput = (e) => debounce(() => saveOpenTextAnswer(q.id, e.target.value), 600)();
        inputsContainer.appendChild(textarea);
    }

    // Toggle nav buttons
    prevBtn.disabled = currentIdx === 0;
    prevBtn.style.opacity = currentIdx === 0 ? '0.5' : '1';
    prevBtn.style.cursor = currentIdx === 0 ? 'not-allowed' : 'pointer';

    if (currentIdx === questions.length - 1) {
        nextBtn.style.display = 'none';
        submitBtn.style.display = 'inline-block';
    } else {
        nextBtn.style.display = 'inline-block';
        submitBtn.style.display = 'none';
    }
}

// Answer Save handlers
function saveSelectedRadio(questionId, optionId, inputEl) {
    // Reset labels background
    const labels = inputsContainer.querySelectorAll('label');
    labels.forEach(lbl => {
        lbl.style.borderColor = 'var(--border)';
        lbl.style.background = '#ffffff';
    });

    const parentLabel = inputEl.parentElement;
    parentLabel.style.borderColor = 'var(--primary)';
    parentLabel.style.background = 'var(--primary-light)';

    userAnswers[questionId].selected_option_ids = [optionId];
    userAnswers[questionId].answer_text = '';
    
    sendAnswerToServer(questionId);
    updateProgress();
}

function saveSelectedCheckbox(questionId, optionId, inputEl) {
    const parentLabel = inputEl.parentElement;
    const isChecked = inputEl.checked;

    if (isChecked) {
        parentLabel.style.borderColor = 'var(--primary)';
        parentLabel.style.background = 'var(--primary-light)';
        if (!userAnswers[questionId].selected_option_ids.includes(optionId)) {
            userAnswers[questionId].selected_option_ids.push(optionId);
        }
    } else {
        parentLabel.style.borderColor = 'var(--border)';
        parentLabel.style.background = '#ffffff';
        userAnswers[questionId].selected_option_ids = userAnswers[questionId].selected_option_ids.filter(id => id !== optionId);
    }
    
    userAnswers[questionId].answer_text = '';
    sendAnswerToServer(questionId);
    updateProgress();
}

function saveOpenTextAnswer(questionId, text) {
    userAnswers[questionId].answer_text = text;
    userAnswers[questionId].selected_option_ids = [];
    
    sendAnswerToServer(questionId);
    updateProgress();
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
    if (currentIdx < questions.length - 1) {
        currentIdx++;
        renderQuestion();
    }
}

function prevQuestion() {
    if (currentIdx > 0) {
        currentIdx--;
        renderQuestion();
    }
}

function updateProgress() {
    let answered = 0;
    questions.forEach(q => {
        const ans = userAnswers[q.id];
        if (ans.selected_option_ids.length > 0 || (ans.answer_text && ans.answer_text.trim() !== '')) {
            answered++;
        }
    });

    const percent = questions.length > 0 ? Math.round((answered / questions.length) * 100) : 0;
    progressBar.style.width = `${percent}%`;
    progressText.innerText = `${percent}% Completed (${answered} of ${questions.length} answered)`;
}

// Timer/Countdown
function startCountdown() {
    if (!durationMinutes) return;
    
    const timerBox = document.getElementById('timer-box');
    const countdownEl = document.getElementById('countdown');
    const totalDurationSeconds = durationMinutes * 60;
    
    const interval = setInterval(() => {
        const now = new Date().getTime();
        const elapsedSeconds = Math.floor((now - startedAt) / 1000);
        const remainingSeconds = totalDurationSeconds - elapsedSeconds;
        
        if (remainingSeconds <= 0) {
            clearInterval(interval);
            countdownEl.innerText = "00:00";
            timerBox.style.background = '#dc2626';
            alert('Time limit reached! Submitting your answers automatically.');
            submitQuiz(true); // Forced submit
            return;
        }

        const mins = Math.floor(remainingSeconds / 60);
        const secs = remainingSeconds % 60;
        countdownEl.innerText = `${String(mins).padStart(2, '0')}:${String(secs).padStart(2, '0')}`;

        if (remainingSeconds < 60) {
            timerBox.style.background = '#dc2626'; // Red color warning
        }
    }, 1000);
}

// Submission
function confirmSubmit() {
    // Check if unanswered questions exist
    let unansweredCount = 0;
    questions.forEach(q => {
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
            window.location.href = `/quiz-attempts/${attemptId}/result`;
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
