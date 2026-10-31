<?php push_style(asset_with_version('css/user/form.css')); ?>

<?php

$questions = $questions ?? [];
$savedAnswers = $savedAnswers ?? [];
$attempt = $attempt ?? null;
$user = $user ?? null;

$introQuestions = [];
$visitedQuestions = [];
$notVisitedQuestions = [];
$triggeredQuestionIds = [];
$visitedOptionTriggerIds = [];
$notVisitedOptionTriggerIds = [];
$mainSurveyId = setting('main_survey_quiz_id');
foreach ($questions as $q) {
    foreach ($q['options'] ?? [] as $opt) {
        if (!empty($opt['related_question_ids'])) {
            foreach ($opt['related_question_ids'] as $rqId) {
                $triggeredQuestionIds[(int)$rqId] = true;
            }

            $optText = strtolower($opt['option_text']);
            if (str_contains($optText, 'chưa từng') || str_contains($optText, 'not-visited') || str_contains($optText, 'not visited') || str_contains($optText, 'chua tung')) {
                foreach ($opt['related_question_ids'] as $rqId) {
                    $notVisitedOptionTriggerIds[(int)$rqId] = true;
                }
            } else {
                foreach ($opt['related_question_ids'] as $rqId) {
                    $visitedOptionTriggerIds[(int)$rqId] = true;
                }
            }
        }
    }
}

foreach ($questions as $q) {
    $qId = (int)$q['id'];
    if (!isset($triggeredQuestionIds[$qId])) {
        $introQuestions[] = $q;
    } elseif (isset($visitedOptionTriggerIds[$qId])) {
        $visitedQuestions[] = $q;
    } elseif (isset($notVisitedOptionTriggerIds[$qId])) {
        $notVisitedQuestions[] = $q;
    } else {
        $introQuestions[] = $q;
    }
}

if (!function_exists('renderDynamicQuestions')) {
    function renderDynamicQuestions(array $qList, array $savedAnswers, &$qCount) {
        foreach ($qList as $q):
            $savedAns = null;
            foreach ($savedAnswers as $ans) {
                if ((int)$ans['question_id'] === (int)$q['id']) {
                    $savedAns = $ans;
                    break;
                }
            }

            $questionType = $q['type'] ?? '';
            $notes = $q['notes'] ?? '';
            $savedOptionIds = $savedAns['selected_option_ids'] ?? [];
            $savedCustomTexts = $savedAns['option_custom_texts'] ?? [];
?>
<fieldset class="survey-question" data-question-id="<?= (int)$q['id'] ?>" data-question-type="<?= e($q['type']) ?>">
    <legend>
        <span class="question-number"><?= $qCount++ ?>.</span>
        <span class="question-copy">
            <?= $q['question_text'] ?>
            <?= $questionType === 'multiple_choice' ? "<em>(Có thể chọn nhiều đáp án)</em>" : '' ?>
            <?= $notes !== '' ? $notes : '' ?>
        </span>
    </legend>

    <?php
        if ($q['type'] === 'single_choice' || $q['type'] === 'true_false'):
            foreach ($q['options'] ?? [] as $opt):
                $travelStatus = '';
                $isChecked = in_array((int)$opt['id'], $savedOptionIds, true);
                if (!empty($opt['related_question_ids'])) {
                    $optText = strtolower($opt['option_text']);
                    if (str_contains($optText, 'chưa từng') || str_contains($optText, 'not-visited') || str_contains($optText, 'not visited') || str_contains($optText, 'chua tung')) {
                        $travelStatus = 'not-visited';
                    } else {
                        $travelStatus = 'visited';
                    }
                }

                $isOther = (bool)($opt['allow_custom_text'] ?? false);
    ?>
        <label class="survey-choice <?= $isOther ? 'survey-choice--other' : '' ?>">
            <input
                type="radio"
                name="q_<?= (int)$q['id'] ?>"
                value="<?= (int)$opt['id'] ?>"
                <?= $isChecked ? 'checked' : '' ?>
                <?= $travelStatus ? 'data-survey-target="' . $travelStatus . '"' : '' ?>
                <?= $q['is_required'] ? 'required' : '' ?>>
            <span><?= $opt['option_text'] ?></span>
            <?php if ($isOther): ?>
                <input
                    class="survey-other-input"
                    type="text"
                    data-option-id="<?= (int)$opt['id'] ?>"
                    value="<?= e($savedCustomTexts[(string)$opt['id']] ?? $savedCustomTexts[(int)$opt['id']] ?? '') ?>"
                    aria-label="<?= $opt['option_text'] ?>">
            <?php endif; ?>
        </label>
            <?php endforeach; ?>
            <?php elseif ($q['type'] === 'multiple_choice'):
                foreach ($q['options'] ?? [] as $opt):
                    $isChecked = in_array((int)$opt['id'], $savedOptionIds, true);
                    $isOther = (bool)($opt['allow_custom_text'] ?? false);
                    ?>
                    <label class="survey-choice <?= $isOther ? 'survey-choice--other' : '' ?>">
                        <input
                            type="checkbox"
                            name="q_<?= (int)$q['id'] ?>[]"
                            value="<?= (int)$opt['id'] ?>"
                            <?= $isChecked ? 'checked' : '' ?>>
                        <span><?= $opt['option_text'] ?></span>
                        <?php if ($isOther): ?>
                            <input
                                class="survey-other-input"
                                type="text"
                                data-option-id="<?= (int)$opt['id'] ?>"
                                value="<?= e($savedCustomTexts[(string)$opt['id']] ?? $savedCustomTexts[(int)$opt['id']] ?? '') ?>"
                                aria-label="<?= $opt['option_text'] ?>">
                        <?php endif; ?>
                    </label>
                <?php endforeach; ?>
                <?php elseif ($q['type'] === 'open_text'): ?>
                    <textarea
                        name="q_<?= (int)$q['id'] ?>"
                        <?= $q['is_required'] ? 'required' : '' ?>><?= $savedAns['answer_text'] ?? '' ?></textarea>
                <?php endif; ?>
            </fieldset>
        <?php endforeach;
    }
}
?>

<section class="survey-section" aria-labelledby="survey-title">
    <form class="survey-form" id="survey-form" action="#" method="post" novalidate data-dynamic="true">
        <h1 class="survey-title" id="survey-title">CÂU HỎI KHẢO SÁT</h1>

        <div class="survey-panel survey-panel--intro is-active" data-survey-panel="intro">
            <?php
            $qCount = 1;
            renderDynamicQuestions($introQuestions, $savedAnswers, $qCount);
            ?>
            <div class="survey-actions">
                <button class="survey-button" type="button" id="survey-next"><span>Tiếp theo</span></button>
            </div>
        </div>

        <div class="survey-panel survey-panel--visited survey-panel--large" data-survey-panel="visited" hidden>
            <?php
            $branchCount = count($introQuestions) + 1;
            renderDynamicQuestions($visitedQuestions, $savedAnswers, $branchCount);
            ?>
            <div class="survey-actions">
                <button class="survey-button" type="button" data-survey-prev="intro" style="background-image: url('<?= assets('images/form/bg-button-next.webp') ?>'); transform: scaleX(-1);">
                    <span style="transform: scaleX(-1); display: inline-block;">Quay lại</span>
                </button>
                <button class="survey-button survey-button--finish" type="submit"><span>Nộp bài</span></button>
            </div>
        </div>

        <div class="survey-panel survey-panel--not-visited" data-survey-panel="not-visited" hidden>
            <?php
            $branchCount = count($introQuestions) + 1;
            renderDynamicQuestions($notVisitedQuestions, $savedAnswers, $branchCount);
            ?>
            <div class="survey-actions">
                <button class="survey-button" type="button" data-survey-prev="intro" style="background-image: url('<?= assets('images/form/bg-button-next.webp') ?>'); transform: scaleX(-1);">
                    <span style="transform: scaleX(-1); display: inline-block;">Quay lại</span>
                </button>
                <button class="survey-button survey-button--finish" type="submit"><span>Nộp bài</span></button>
            </div>
        </div>
    </form>
</section>

<template id="region-options-template">
    <label class="survey-choice"><input type="checkbox" value="hokkaido"><span>Hokkaido</span></label>
    <label class="survey-choice"><input type="checkbox" value="chubu"><span>Chubu (Nagoya, Takayama, núi Phú Sĩ...)</span></label>
    <label class="survey-choice"><input type="checkbox" value="kanto"><span>Kanto (Tokyo, Ibaraki, Chiba,...)</span></label>
    <label class="survey-choice"><input type="checkbox" value="chugoku"><span>Chugoku (Hiroshima, Okayama...)</span></label>
    <label class="survey-choice"><input type="checkbox" value="kyushu"><span>Kyushu (Fukuoka, Kumamoto, Beppu...)</span></label>
    <label class="survey-choice"><input type="checkbox" value="tohoku"><span>Tohoku (Sendai, Aomori...)</span></label>
    <label class="survey-choice"><input type="checkbox" value="kansai"><span>Kansai (Osaka, Kyoto, Wakayama,...)</span></label>
    <label class="survey-choice"><input type="checkbox" value="shikoku"><span>Shikoku (Takamatsu, Matsuyama...)</span></label>
    <label class="survey-choice"><input type="checkbox" value="okinawa"><span>Okinawa</span></label>
    <label class="survey-choice survey-choice--other">
        <input type="checkbox" value="other">
        <span>Khác:</span>
        <input class="survey-other-input" type="text" aria-label="Vùng khác">
    </label>
</template>

<template id="travel-style-options-template">
    <label class="survey-choice"><input type="radio" value="package-tour"><span>Mua tour trọn gói của công ty lữ hành</span></label>
    <label class="survey-choice"><input type="radio" value="custom-tour"><span>Đặt tour thiết kế riêng qua công ty lữ hành</span></label>
    <label class="survey-choice"><input type="radio" value="self-guided"><span>Du lịch tự túc, tự đặt vé máy bay, khách sạn và các dịch vụ qua OTA (Traveloka, Booking, Agoda,...)</span></label>
    <label class="survey-choice"><input type="radio" value="undecided"><span>Chưa quyết định</span></label>
</template>

<template id="information-options-template">
    <label class="survey-choice"><input type="checkbox" value="jnto-social"><span>Các kênh SNS (Cảm Nhận Nhật Bản và visitjapan_vn) và website của JNTO Việt Nam</span></label>
    <label class="survey-choice"><input type="checkbox" value="kol"><span>Các kênh SNS của những người có tầm ảnh hưởng (KOL)</span></label>
    <label class="survey-choice"><input type="checkbox" value="tiktok"><span>TikTok</span></label>
    <label class="survey-choice"><input type="checkbox" value="facebook"><span>Facebook</span></label>
    <label class="survey-choice"><input type="checkbox" value="youtube"><span>YouTube</span></label>
    <label class="survey-choice"><input type="checkbox" value="threads-instagram"><span>Threads/Instagram</span></label>
    <label class="survey-choice"><input type="checkbox" value="travel-company"><span>Tư vấn với công ty du lịch</span></label>
    <label class="survey-choice"><input type="checkbox" value="ota-blog"><span>Các bài blog du lịch trên các trang OTA (Traveloka, Agoda, Klook,...)</span></label>
    <label class="survey-choice"><input type="checkbox" value="print"><span>Sách du lịch, cẩm nang, tạp chí, tờ rơi,...</span></label>
    <label class="survey-choice"><input type="checkbox" value="word-of-mouth"><span>Truyền miệng (từ bạn bè, người quen)</span></label>
    <label class="survey-choice survey-choice--other">
        <input type="checkbox" value="other">
        <span>Khác:</span>
        <input class="survey-other-input" type="text" aria-label="Nguồn thông tin khác">
    </label>
</template>

<?php push_script(asset_with_version('js/user/form.js')); ?>

<script>
(function() {
    const attemptId = <?= isset($attempt['id']) ? (int)$attempt['id'] : 'null' ?>;
    const mainSurveyQuizId = <?= !empty($mainSurveyId) ? (int)$mainSurveyId : 'null' ?>;
    const isLoggedIn = <?= !empty($user) ? 'true' : 'false' ?>;
    const STORAGE_KEY = 'jnto_survey_data';

    function getStoredData() {
        try {
            const raw = localStorage.getItem(STORAGE_KEY);
            return raw ? JSON.parse(raw) : { answers: {}, travel_status: null, pending_sync: false };
        } catch (e) {
            return { answers: {}, travel_status: null, pending_sync: false };
        }
    }

    function setStoredData(data) {
        try {
            localStorage.setItem(STORAGE_KEY, JSON.stringify(data));
        } catch (e) {
            console.error('Failed to update localStorage:', e);
        }
    }

    function collectQuestionAnswer(fieldset) {
        if (!fieldset) return null;
        const questionId = parseInt(fieldset.dataset.questionId, 10);
        const type = fieldset.dataset.questionType;

        let selectedOptionIds = [];
        let answerText = null;
        let optionCustomTexts = {};

        if (type === 'single_choice' || type === 'true_false') {
            const checkedRadio = fieldset.querySelector('input[type="radio"]:checked');
            if (checkedRadio) {
                selectedOptionIds.push(parseInt(checkedRadio.value, 10));
                const otherInput = checkedRadio.closest('.survey-choice')?.querySelector('.survey-other-input');
                if (otherInput && otherInput.value.trim() !== '') {
                    optionCustomTexts[checkedRadio.value] = otherInput.value.trim();
                }
            }
        } else if (type === 'multiple_choice') {
            fieldset.querySelectorAll('input[type="checkbox"]:checked').forEach(cb => {
                selectedOptionIds.push(parseInt(cb.value, 10));
                const otherInput = cb.closest('.survey-choice')?.querySelector('.survey-other-input');
                if (otherInput && otherInput.value.trim() !== '') {
                    optionCustomTexts[cb.value] = otherInput.value.trim();
                }
            });
        } else if (type === 'open_text') {
            const textarea = fieldset.querySelector('textarea');
            if (textarea) {
                answerText = textarea.value;
            }
        }

        return {
            question_id: questionId,
            selected_option_ids: selectedOptionIds,
            answer_text: answerText,
            option_custom_texts: optionCustomTexts
        };
    }

    function collectAllAnswers() {
        const answers = {};
        form.querySelectorAll('.survey-question').forEach(fieldset => {
            const ans = collectQuestionAnswer(fieldset);
            if (ans && (ans.selected_option_ids.length > 0 || (ans.answer_text && ans.answer_text.trim() !== ''))) {
                answers[ans.question_id] = ans;
            }
        });
        return answers;
    }

    async function saveAnswer(questionId, selectedOptionIds, answerText, optionCustomTexts) {
        // Store in localStorage for both guest and backup
        const stored = getStoredData();
        stored.answers = stored.answers || {};
        stored.answers[questionId] = {
            question_id: questionId,
            selected_option_ids: selectedOptionIds,
            answer_text: answerText,
            option_custom_texts: optionCustomTexts
        };

        const selectedExp = form.querySelector('input[name="travel_experience"]:checked') || form.querySelector('input[data-survey-target]:checked');
        if (selectedExp && selectedExp.dataset.surveyTarget) {
            stored.travel_status = selectedExp.dataset.surveyTarget;
        }
        if (!stored.status || stored.status !== 'done') {
            stored.status = 'not_done';
            try {
                localStorage.setItem('jnto_survey_status', 'not_done');
            } catch (e) {}
        }
        setStoredData(stored);

        // If authenticated with attempt, save to server
        if (isLoggedIn && attemptId) {
            try {
                await fetch(`${window.APP_URL}/api/quiz-attempts/${attemptId}/answers`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        question_id: questionId,
                        selected_option_ids: selectedOptionIds,
                        answer_text: answerText,
                        option_custom_texts: optionCustomTexts
                    })
                });
            } catch (err) {
                console.error('Failed to save answer to server:', err);
            }
        }
    }

    function debounce(func, wait) {
        let timeout;
        return function(...args) {
            clearTimeout(timeout);
            timeout = setTimeout(() => func.apply(this, args), wait);
        };
    }

    function updateCustomInputs() {
        const otherInputs = document.querySelectorAll('.survey-other-input');
        otherInputs.forEach(input => {
            const choiceLabel = input.closest('.survey-choice');
            if (choiceLabel) {
                const checkable = choiceLabel.querySelector('input[type="radio"], input[type="checkbox"]');
                if (checkable) {
                    input.required = checkable.checked;
                }
            }
        });
    }

    const handleInputSave = debounce((element) => {
        triggerSave(element);
    }, 600);

    const form = document.getElementById('survey-form');
    if (!form) return;

    form.addEventListener('input', function (event) {
        if (event.target.classList.contains('survey-other-input') || event.target.tagName === 'TEXTAREA') {
            handleInputSave(event.target);
        }
    });

    form.addEventListener('change', function (event) {
        if (event.target.type === 'radio' || event.target.type === 'checkbox') {
            updateCustomInputs();
            triggerSave(event.target);
        }
    });

    form.addEventListener('focusin', function (event) {
        if (event.target.classList.contains('survey-other-input')) {
            const choiceLabel = event.target.closest('.survey-choice');
            if (choiceLabel) {
                const input = choiceLabel.querySelector('input[type="radio"], input[type="checkbox"]');
                if (input && !input.checked) {
                    input.checked = true;
                    updateCustomInputs();
                    input.dispatchEvent(new Event('change', { bubbles: true }));
                }
            }
        }
    });

    function triggerSave(element) {
        const fieldset = element.closest('.survey-question');
        if (!fieldset) return;

        const ans = collectQuestionAnswer(fieldset);
        if (ans) {
            saveAnswer(ans.question_id, ans.selected_option_ids, ans.answer_text, ans.option_custom_texts);
        }
    }

    function restoreFromLocalStorage() {
        const stored = getStoredData();
        if (!stored || !stored.answers) return;

        Object.values(stored.answers).forEach(ans => {
            const qId = ans.question_id;
            const fieldset = form.querySelector(`.survey-question[data-question-id="${qId}"]`);
            if (!fieldset) return;

            const type = fieldset.dataset.questionType;
            if (type === 'single_choice' || type === 'true_false') {
                if (ans.selected_option_ids && ans.selected_option_ids.length > 0) {
                    const optId = ans.selected_option_ids[0];
                    const radio = fieldset.querySelector(`input[type="radio"][value="${optId}"]`);
                    if (radio) {
                        radio.checked = true;
                    }
                }
            } else if (type === 'multiple_choice') {
                if (Array.isArray(ans.selected_option_ids)) {
                    ans.selected_option_ids.forEach(optId => {
                        const cb = fieldset.querySelector(`input[type="checkbox"][value="${optId}"]`);
                        if (cb) {
                            cb.checked = true;
                        }
                    });
                }
            } else if (type === 'open_text') {
                const textarea = fieldset.querySelector('textarea');
                if (textarea && ans.answer_text) {
                    textarea.value = ans.answer_text;
                }
            }

            if (ans.option_custom_texts) {
                Object.entries(ans.option_custom_texts).forEach(([optId, text]) => {
                    const customInput = fieldset.querySelector(`.survey-other-input[data-option-id="${optId}"]`)
                        || fieldset.querySelector(`input[value="${optId}"]`)?.closest('.survey-choice')?.querySelector('.survey-other-input');
                    if (customInput) {
                        customInput.value = text;
                    }
                });
            }
        });

        updateCustomInputs();

        if (stored.travel_status) {
            const targetRadio = form.querySelector(`input[data-survey-target="${stored.travel_status}"]`);
            if (targetRadio) {
                targetRadio.checked = true;
            }
        }
    }

    function createOverlay(text) {
        let overlay = document.getElementById('survey-sync-overlay');
        if (!overlay) {
            overlay = document.createElement('div');
            overlay.id = 'survey-sync-overlay';
            overlay.style.position = 'fixed';
            overlay.style.top = '0';
            overlay.style.left = '0';
            overlay.style.width = '100vw';
            overlay.style.height = '100vh';
            overlay.style.backgroundColor = 'rgba(0, 0, 0, 0.65)';
            overlay.style.display = 'flex';
            overlay.style.flexDirection = 'column';
            overlay.style.alignItems = 'center';
            overlay.style.justifyContent = 'center';
            overlay.style.zIndex = '99999';
            overlay.style.color = '#fff';
            overlay.style.fontFamily = 'inherit';
            overlay.innerHTML = `
                <div style="background: #fff; color: #333; padding: 24px 32px; border-radius: 12px; text-align: center; max-width: 400px; box-shadow: 0 8px 30px rgba(0,0,0,0.3);">
                    <div style="font-size: 28px; margin-bottom: 12px;">⏳</div>
                    <div id="survey-sync-msg" style="font-size: 16px; font-weight: 600; line-height: 1.5;">${text}</div>
                    <div style="margin-top: 8px; font-size: 13px; color: #777;">Vui lòng không đóng trình duyệt...</div>
                </div>
            `;
            document.body.appendChild(overlay);
        } else {
            const msgEl = overlay.querySelector('#survey-sync-msg');
            if (msgEl) msgEl.textContent = text;
            overlay.style.display = 'flex';
        }
    }

    function removeOverlay() {
        const overlay = document.getElementById('survey-sync-overlay');
        if (overlay) {
            overlay.style.display = 'none';
        }
    }

    async function handleAutoSyncIfPending() {
        if (!isLoggedIn || !attemptId) {
            try {
                const surveyStatus = localStorage.getItem('jnto_survey_status');
                const raw = localStorage.getItem('jnto_survey_data');
                const data = raw ? JSON.parse(raw) : null;
                if (surveyStatus === 'done' || data?.status === 'done') {
                    window.location.href = `${window.APP_URL}/register?from=survey`;
                    return;
                }
            } catch (e) {}
            restoreFromLocalStorage();
            return;
        }

        const stored = getStoredData();
        if (!stored || !stored.answers || Object.keys(stored.answers).length === 0) {
            return;
        }

        if (stored.pending_sync || stored.status === 'done') {
            createOverlay('Đang lưu kết quả khảo sát của bạn...');
            try {
                for (const ans of Object.values(stored.answers)) {
                    await fetch(`${window.APP_URL}/api/quiz-attempts/${attemptId}/answers`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({
                            question_id: ans.question_id,
                            selected_option_ids: ans.selected_option_ids,
                            answer_text: ans.answer_text,
                            option_custom_texts: ans.option_custom_texts
                        })
                    });
                }

                const response = await fetch(`${window.APP_URL}/api/quiz-attempts/${attemptId}/submit`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    }
                });

                if (response.ok) {
                    try {
                        localStorage.setItem('jnto_survey_status', 'done');
                        const stored = getStoredData();
                        stored.status = 'done';
                        stored.pending_sync = false;
                        stored.completed_at = new Date().toISOString();
                        setStoredData(stored);
                    } catch (e) {}

                    window.location.href = `${window.APP_URL}/take-questions`;
                    return;
                } else {
                    removeOverlay();
                    const res = await response.json();
                    alert(res.message || 'Không thể gửi kết quả khảo sát. Vui lòng thử lại.');
                    restoreFromLocalStorage();
                }
            } catch (e) {
                console.error('Error auto-syncing survey:', e);
                removeOverlay();
                restoreFromLocalStorage();
            }
        } else {
            restoreFromLocalStorage();
        }
    }

    form.addEventListener('submit', async function (event) {
        event.preventDefault();

        // Flush any pending save for the active element
        if (document.activeElement &&
            (document.activeElement.classList.contains('survey-other-input') || document.activeElement.tagName === 'TEXTAREA')) {
            triggerSave(document.activeElement);
        }

        // Check validation
        const activePanel = form.querySelector('.survey-panel.is-active');
        const requiredInputs = Array.from(activePanel ? activePanel.querySelectorAll('[required]') : form.querySelectorAll('[required]'));
        const isValid = requiredInputs.every(function (input) {
            return input.checkValidity();
        });

        if (!isValid) {
            form.classList.add('was-validated');
            const firstInvalid = requiredInputs.find(function (input) {
                return !input.checkValidity();
            });
            if (firstInvalid) {
                firstInvalid.reportValidity();
            }
            return;
        }

        // Collect all answers
        const allAnswers = collectAllAnswers();
        const selectedExp = form.querySelector('input[name="travel_experience"]:checked') || form.querySelector('input[data-survey-target]:checked');
        const travelStatus = selectedExp?.dataset?.surveyTarget || null;
        const stored = {
            answers: allAnswers,
            travel_status: travelStatus,
            status: 'done',
            pending_sync: false,
            saved_at: new Date().toISOString(),
            completed_at: new Date().toISOString()
        };
        try {
            localStorage.setItem('jnto_survey_status', 'done');
        } catch (e) {}
        setStoredData(stored);

        // If guest (not logged in): save to server staging table via API then redirect
        if (!isLoggedIn) {
            createOverlay('Đang lưu kết quả khảo sát của bạn...');
            try {
                const response = await fetch(`${window.APP_URL}/api/survey/guest-submit`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        quiz_id: mainSurveyQuizId,
                        travel_status: travelStatus,
                        answers: allAnswers
                    })
                });

                const data = await response.json();
                if (response.ok && data.success) {
                    try {
                        stored.pending_sync = false;
                        setStoredData(stored);
                    } catch (e) {}

                    window.location.href = data.redirect || `${window.APP_URL}/register?from=survey`;
                    return;
                } else {
                    removeOverlay();
                    alert(data.message || 'Không thể lưu kết quả khảo sát. Vui lòng thử lại.');
                    return;
                }
            } catch (err) {
                removeOverlay();
                console.error('Failed to submit guest survey:', err);
                alert('Có lỗi xảy ra khi lưu kết quả khảo sát. Vui lòng thử lại.');
                return;
            }
        }

        // If logged in: submit the attempt
        createOverlay('Đang nộp bài khảo sát...');
        try {
            const response = await fetch(`${window.APP_URL}/api/quiz-attempts/${attemptId}/submit`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                }
            });
            if (response.ok) {
                try {
                    localStorage.setItem('jnto_survey_status', 'done');
                    const stored = getStoredData();
                    stored.status = 'done';
                    stored.pending_sync = false;
                    stored.completed_at = new Date().toISOString();
                    setStoredData(stored);
                } catch (e) {}

                window.location.href = `${window.APP_URL}/take-questions`;
            } else {
                removeOverlay();
                const res = await response.json();
                alert(res.message || 'Có lỗi xảy ra khi nộp bài.');
            }
        } catch (err) {
            removeOverlay();
            console.error(err);
            alert('Có lỗi xảy ra trong quá trình nộp bài.');
        }
    });

    updateCustomInputs();
    handleAutoSyncIfPending();
})();
</script>
