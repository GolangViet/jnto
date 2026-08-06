<?php push_style(asset_with_version('css/user/form.css')); ?>

<?php

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
    const attemptId = <?= (int)$attempt['id'] ?>;

    async function saveAnswer(questionId, selectedOptionIds, answerText, optionCustomTexts) {
        try {
            await fetch(`/api/quiz-attempts/${attemptId}/answers`, {
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
            console.error('Failed to save answer:', err);
        }
    }

    function debounce(func, wait) {
        let timeout;
        return function(...args) {
            clearTimeout(timeout);
            timeout = setTimeout(() => func.apply(this, args), wait);
        };
    }

    const handleInputSave = debounce((element) => {
        triggerSave(element);
    }, 600);

    const form = document.getElementById('survey-form');
    form.addEventListener('input', function (event) {
        if (event.target.classList.contains('survey-other-input') || event.target.tagName === 'TEXTAREA') {
            handleInputSave(event.target);
        }
    });

    form.addEventListener('change', function (event) {
        if (event.target.type === 'radio' || event.target.type === 'checkbox') {
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
                    input.dispatchEvent(new Event('change', { bubbles: true }));
                }
            }
        }
    });

    function triggerSave(element) {
        const fieldset = element.closest('.survey-question');
        if (!fieldset) return;

        const questionId = parseInt(fieldset.dataset.questionId);
        const type = fieldset.dataset.questionType;

        let selectedOptionIds = [];
        let answerText = null;
        let optionCustomTexts = {};

        if (type === 'single_choice' || type === 'true_false') {
            const checkedRadio = fieldset.querySelector('input[type="radio"]:checked');
            if (checkedRadio) {
                selectedOptionIds.push(parseInt(checkedRadio.value));
                const otherInput = checkedRadio.closest('.survey-choice').querySelector('.survey-other-input');
                if (otherInput) {
                    optionCustomTexts[checkedRadio.value] = otherInput.value;
                }
            }
        } else if (type === 'multiple_choice') {
            fieldset.querySelectorAll('input[type="checkbox"]:checked').forEach(cb => {
                selectedOptionIds.push(parseInt(cb.value));
                const otherInput = cb.closest('.survey-choice').querySelector('.survey-other-input');
                if (otherInput) {
                    optionCustomTexts[cb.value] = otherInput.value;
                }
            });
        } else if (type === 'open_text') {
            const textarea = fieldset.querySelector('textarea');
            if (textarea) {
                answerText = textarea.value;
            }
        }

        saveAnswer(questionId, selectedOptionIds, answerText, optionCustomTexts);
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
        const requiredInputs = Array.from(activePanel.querySelectorAll('[required]'));
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

        try {
            const response = await fetch(`/api/quiz-attempts/${attemptId}/submit`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                }
            });
            if (response.ok) {
                window.location.href = '/take-questions';
            } else {
                const res = await response.json();
                alert(res.message || 'Failed to submit survey.');
            }
        } catch (err) {
            console.error(err);
            alert('An error occurred during submission.');
        }
    });
})();
</script>
