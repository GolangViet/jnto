<?php push_style(assets('css/user/page4.css')); ?>

<section class="region-map" aria-labelledby="region-map-title">
    <h2 class="visually-hidden" id="region-map-title">Khám phá các vùng của Nhật Bản</h2>

    <div class="region-map__guide">
        <img class="region-map__search-line" src="<?= assets('images/page-4/line-search.webp') ?>" alt="">
        <p>Vui lòng truy cập vào từng vùng để tìm hiểu thông tin<br>và trả lời các câu hỏi tương ứng</p>
        <img class="region-map__line" src="<?= assets('images/page-4/line.webp') ?>" alt="">
    </div>

    <a class="region-card region-card--kyushu"
       href="https://www.japan.travel/vi/destinations/kyushu/"
       target="_blank" rel="noopener noreferrer"
       aria-label="Tìm hiểu về vùng Kyushu">
        <img src="<?= assets('images/page-4/box-kyushu.webp') ?>" alt="">
    </a>

    <a class="region-card region-card--hokuriku"
       href="https://www.japan.travel/vi/destinations/hokuriku-shinetsu/"
       target="_blank" rel="noopener noreferrer"
       aria-label="Tìm hiểu về vùng Chubu, Hokuriku Shinetsu">
        <img src="<?= assets('images/page-4/box-hokuriki.webp') ?>" alt="">
        <span class="region-card__label">
                    <strong>CHUBU</strong>
                    <small>(Hokuriku Shinetsu)</small>
                </span>
    </a>

    <a class="region-card region-card--tokai"
       href="https://www.japan.travel/vi/destinations/tokai/"
       target="_blank" rel="noopener noreferrer"
       aria-label="Tìm hiểu về vùng Chubu, Tokai">
        <img src="<?= assets('images/page-4/box-tokai.webp') ?>" alt="">
        <span class="region-card__label">
                    <strong>CHUBU</strong>
                    <small>(Tokai)</small>
                </span>
    </a>
</section>

<section class="knowledge-section" aria-labelledby="knowledge-title">
    <form class="knowledge-form" action="#" method="post" novalidate>
        <h2 class="knowledge-title" id="knowledge-title"><span>CÂU HỎI KIẾN THỨC</span></h2>
        <?php
        $quizMCQs = [];
        $quizEssays = [];
        foreach ($quizQuestions as $q) {
            if ($q['type'] === 'open_text') {
                $quizEssays[] = $q;
            } else {
                $quizMCQs[] = $q;
            }
        }
        ?>

        <?php if (!empty($quizMCQs)): ?>
            <section class="knowledge-part knowledge-part--quiz" aria-labelledby="quiz-title">
                <h3 class="knowledge-part__title knowledge-part__title--quiz" id="quiz-title"><span>TRẮC NGHIỆM</span></h3>

                <div class="knowledge-quiz-list">
                    <?php
                    $mcqCount = 1;
                    foreach ($quizMCQs as $q):
                        $savedAns = null;
                        foreach ($quizSavedAnswers as $ans) {
                            if ((int)$ans['question_id'] === (int)$q['id']) {
                                $savedAns = $ans;
                                break;
                            }
                        }
                        $savedOptionIds = $savedAns['selected_option_ids'] ?? [];
                        $savedCustomTexts = $savedAns['option_custom_texts'] ?? [];
                        ?>
                        <fieldset class="knowledge-question" data-question-id="<?= (int)$q['id'] ?>" data-question-type="<?= e($q['type']) ?>" data-quiz-type="knowledge">
                            <legend>
                                <span class="knowledge-question__number"><?= $mcqCount++ ?>.</span>
                                <span class="knowledge-question__copy"><?= e($q['question_text']) ?></span>
                            </legend>
                            <?php
                            if ($q['type'] === 'single_choice' || $q['type'] === 'true_false'):
                                foreach ($q['options'] ?? [] as $opt):
                                    $isChecked = in_array((int)$opt['id'], $savedOptionIds, true);
                                    $isOther = (bool)($opt['allow_custom_text'] ?? false);
                                    ?>
                                    <label class="knowledge-choice <?= $isOther ? 'knowledge-choice--other' : '' ?>">
                                        <input type="radio"
                                               name="q_<?= (int)$q['id'] ?>"
                                               value="<?= (int)$opt['id'] ?>"
                                               <?= $isChecked ? 'checked' : '' ?>
                                               <?= $q['is_required'] ? 'required' : '' ?>>
                                        <span><?= e($opt['option_text']) ?></span>
                                        <?php if ($isOther): ?>
                                            <input class="knowledge-other-input"
                                                   type="text"
                                                   data-option-id="<?= (int)$opt['id'] ?>"
                                                   value="<?= e($savedCustomTexts[(string)$opt['id']] ?? $savedCustomTexts[(int)$opt['id']] ?? '') ?>"
                                                   aria-label="<?= e($opt['option_text']) ?>">
                                        <?php endif; ?>
                                    </label>
                                <?php endforeach; ?>
                            <?php elseif ($q['type'] === 'multiple_choice'):
                                foreach ($q['options'] ?? [] as $opt):
                                    $isChecked = in_array((int)$opt['id'], $savedOptionIds, true);
                                    $isOther = (bool)($opt['allow_custom_text'] ?? false);
                                    ?>
                                    <label class="knowledge-choice <?= $isOther ? 'knowledge-choice--other' : '' ?>">
                                        <input type="checkbox"
                                               name="q_<?= (int)$q['id'] ?>[]"
                                               value="<?= (int)$opt['id'] ?>"
                                               <?= $isChecked ? 'checked' : '' ?>>
                                        <span><?= e($opt['option_text']) ?></span>
                                        <?php if ($isOther): ?>
                                            <input class="knowledge-other-input"
                                                   type="text"
                                                   data-option-id="<?= (int)$opt['id'] ?>"
                                                   value="<?= e($savedCustomTexts[(string)$opt['id']] ?? $savedCustomTexts[(int)$opt['id']] ?? '') ?>"
                                                   aria-label="<?= e($opt['option_text']) ?>">
                                        <?php endif; ?>
                                    </label>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </fieldset>
                    <?php endforeach; ?>
                </div>
            </section>
        <?php endif; ?>

        <?php if (!empty($quizEssays)): ?>
            <section class="knowledge-part knowledge-part--essay" aria-labelledby="essay-title">
                <h3 class="knowledge-part__title knowledge-part__title--essay" id="essay-title"><span>TỰ LUẬN</span></h3>
                <div class="knowledge-essay-list">
                    <?php
                    $essayCount = 1;
                    foreach ($quizEssays as $q):
                        $savedAns = null;
                        foreach ($quizSavedAnswers as $ans) {
                            if ((int)$ans['question_id'] === (int)$q['id']) {
                                $savedAns = $ans;
                                break;
                            }
                        }
                        ?>
                        <div class="knowledge-question-list" data-question-id="<?= (int)$q['id'] ?>" data-question-type="open_text" data-quiz-type="knowledge">
                            <label class="knowledge-essay">
                                <span class="knowledge-essay__heading">
                                    <strong><?= $essayCount++ ?>.</strong>
                                    <span><?= e($q['question_text']) ?></span>
                                </span>
                                <textarea
                                    name="q_<?= (int)$q['id'] ?>"
                                    aria-label="Câu trả lời tự luận số <?= $essayCount - 1 ?>"
                                    <?= $q['is_required'] ? 'required' : '' ?>><?= e($savedAns['answer_text'] ?? '') ?></textarea>
                            </label>
                        </div>
                    <?php endforeach; ?>
                </div>
            </section>
        <?php endif; ?>
    </form>

    <img class="knowledge-arrow" src="<?= assets('images/page-4/section-3/mui-ten.webp') ?>" alt="" />
</section>

<section class="open-question-section" aria-labelledby="open-question-title">
    <form class="open-question-form" id="open-question-form" action="#" method="post" novalidate>
        <h2 class="open-question-title" id="open-question-title">
            <span>CÂU HỎI MỞ</span>
        </h2>

        <div class="open-question-list">
            <?php if ($mainOpenId): ?>
                <?php
                $openCount = 1;
                foreach ($openQuestions as $q):
                    $savedAns = null;
                    foreach ($openSavedAnswers as $ans) {
                        if ((int)$ans['question_id'] === (int)$q['id']) {
                            $savedAns = $ans;
                            break;
                        }
                    }

                    $savedOptionIds = $savedAns['selected_option_ids'] ?? [];
                    $savedCustomTexts = $savedAns['option_custom_texts'] ?? [];
                    ?>
                    <div class="open-question" role="group" data-question-id="<?= (int)$q['id'] ?>" data-question-type="<?= e($q['type']) ?>" data-quiz-type="open" aria-labelledby="open-question-<?= (int)$q['id'] ?>">
                        <p class="open-question__heading" id="open-question-<?= (int)$q['id'] ?>">
                            <span class="open-question__number"><?= $openCount++ ?>.</span>
                            <span class="open-question__copy"><?= e($q['question_text']) ?></span>
                        </p>

                        <?php
                        if ($q['type'] === 'single_choice' || $q['type'] === 'true_false'):
                            foreach ($q['options'] ?? [] as $opt):
                                $isChecked = in_array((int)$opt['id'], $savedOptionIds, true);
                                $isOther = (bool)($opt['allow_custom_text'] ?? false);
                                ?>
                                <label class="open-question-choice <?= $isOther ? 'open-question-choice--other' : '' ?>">
                                    <input type="radio"
                                           name="q_<?= (int)$q['id'] ?>"
                                           value="<?= (int)$opt['id'] ?>"
                                           <?= $isChecked ? 'checked' : '' ?>
                                           <?= $q['is_required'] ? 'required' : '' ?>>
                                    <span><?= e($opt['option_text']) ?></span>
                                    <?php if ($isOther): ?>
                                        <input class="open-question-other-input"
                                               type="text"
                                               data-option-id="<?= (int)$opt['id'] ?>"
                                               value="<?= e($savedCustomTexts[(string)$opt['id']] ?? $savedCustomTexts[(int)$opt['id']] ?? '') ?>"
                                               aria-label="<?= e($opt['option_text']) ?>">
                                    <?php endif; ?>
                                </label>
                            <?php endforeach; ?>
                        <?php elseif ($q['type'] === 'multiple_choice'):
                            foreach ($q['options'] ?? [] as $opt):
                                $isChecked = in_array((int)$opt['id'], $savedOptionIds, true);
                                $isOther = (bool)($opt['allow_custom_text'] ?? false);
                                ?>
                                <label class="open-question-choice <?= $isOther ? 'open-question-choice--other' : '' ?>">
                                    <input type="checkbox"
                                           name="q_<?= (int)$q['id'] ?>[]"
                                           value="<?= (int)$opt['id'] ?>"
                                           <?= $isChecked ? 'checked' : '' ?>>
                                    <span><?= e($opt['option_text']) ?></span>
                                    <?php if ($isOther): ?>
                                        <input class="open-question-other-input"
                                               type="text"
                                               data-option-id="<?= (int)$opt['id'] ?>"
                                               value="<?= e($savedCustomTexts[(string)$opt['id']] ?? $savedCustomTexts[(int)$opt['id']] ?? '') ?>"
                                               aria-label="<?= e($opt['option_text']) ?>">
                                    <?php endif; ?>
                                </label>
                            <?php endforeach; ?>
                        <?php elseif ($q['type'] === 'open_text'): ?>
                            <textarea name="q_<?= (int)$q['id'] ?>"
                                      style="border: 1px solid #cbd5e1; border-radius: 8px; width: 100%; min-height: 120px; padding: 12px; font-family: inherit; font-size: 1rem;"
                                      <?= $q['is_required'] ? 'required' : '' ?>><?= e($savedAns['answer_text'] ?? '') ?></textarea>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <!-- STATIC FALLBACK OPEN QUESTIONS -->
                <div class="open-question" role="group" aria-labelledby="open-question-1">
                    <p class="open-question__heading" id="open-question-1">
                        <span class="open-question__number">1.</span>
                        <span class="open-question__copy">Bạn coi trọng điều gì nhất khi đi du lịch Nhật Bản?</span>
                    </p>

                    <label class="open-question-choice">
                        <input type="radio" name="open_question_1" value="nature" required>
                        <span>Thiên nhiên và phong cảnh đẹp</span>
                    </label>
                    <label class="open-question-choice">
                        <input type="radio" name="open_question_1" value="food">
                        <span>Ẩm thực và ăn uống</span>
                    </label>
                    <label class="open-question-choice">
                        <input type="radio" name="open_question_1" value="onsen">
                        <span>Tắm suối nước nóng và thư giãn</span>
                    </label>
                    <label class="open-question-choice">
                        <input type="radio" name="open_question_1" value="activities">
                        <span>Các hoạt động và trải nghiệm</span>
                    </label>
                </div>

                <div class="open-question" role="group" aria-labelledby="open-question-2">
                    <p class="open-question__heading" id="open-question-2">
                        <span class="open-question__number">2.</span>
                        <span class="open-question__copy">Phong cách du lịch nào phù hợp nhất với sở thích của bạn?</span>
                    </p>

                    <label class="open-question-choice">
                        <input type="radio" name="open_question_2" value="slow-travel" required>
                        <span>Nghỉ ngơi, thư giãn nhịp độ chậm</span>
                    </label>
                    <label class="open-question-choice">
                        <input type="radio" name="open_question_2" value="check-in">
                        <span>Đi check-in càng nhiều điểm nổi tiếng càng tốt</span>
                    </label>
                    <label class="open-question-choice">
                        <input type="radio" name="open_question_2" value="photography">
                        <span>Chụp thật nhiều ảnh</span>
                    </label>
                    <label class="open-question-choice">
                        <input type="radio" name="open_question_2" value="unique-experiences">
                        <span>Tận hưởng những trải nghiệm độc đáo, đáng nhớ</span>
                    </label>
                </div>
            <?php endif; ?>
        </div>
    </form>
</section>

<section class="page4-next-section" aria-label="Tiếp tục">
    <?php if ($mainQuizId || $mainOpenId): ?>
        <button class="page4-next-button" id="dynamic-next-button" type="button">
            <span>TIẾP THEO</span>
        </button>
    <?php else: ?>
        <button class="page4-next-button" type="submit" form="open-question-form">
            <span>TIẾP THEO</span>
        </button>
        <a href="/confirm-post" class="page4-next-button">Trang tiếp theo</a>
    <?php endif; ?>
</section>

<?php if ($mainQuizId || $mainOpenId): ?>
    <script>
    (function() {
        const quizAttemptId = <?= $quizAttempt ? (int)$quizAttempt['id'] : 'null' ?>;
        const openAttemptId = <?= $openAttempt ? (int)$openAttempt['id'] : 'null' ?>;

        async function saveAnswer(attemptId, questionId, selectedOptionIds, answerText, optionCustomTexts) {
            if (!attemptId) return;
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

        function updateCustomInputs() {
            const otherInputs = document.querySelectorAll('.knowledge-other-input, .open-question-other-input');
            otherInputs.forEach(input => {
                const choiceLabel = input.closest('.knowledge-choice, .open-question-choice');
                if (choiceLabel) {
                    const checkable = choiceLabel.querySelector('input[type="radio"], input[type="checkbox"]');
                    if (checkable) {
                        if (checkable.checked) {
                            input.required = true;
                        } else {
                            input.required = false;
                        }
                    }
                }
            });
        }

        const handleInputSave = debounce((element) => {
            triggerSave(element);
        }, 600);

        const containers = document.querySelectorAll('.knowledge-form, .open-question-form');
        containers.forEach(container => {
            container.addEventListener('input', function (event) {
                if (event.target.classList.contains('knowledge-other-input') ||
                    event.target.classList.contains('open-question-other-input') ||
                    event.target.tagName === 'TEXTAREA') {
                    handleInputSave(event.target);
                }
            });

            container.addEventListener('change', function (event) {
                if (event.target.type === 'radio' || event.target.type === 'checkbox') {
                    updateCustomInputs();
                    triggerSave(event.target);
                }
            });

            container.addEventListener('focusin', function (event) {
                if (event.target.classList.contains('knowledge-other-input') ||
                    event.target.classList.contains('open-question-other-input')) {
                    const choiceLabel = event.target.closest('.knowledge-choice, .open-question-choice');
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
        });

        function triggerSave(element) {
            const questionEl = element.closest('.knowledge-question-list, .knowledge-question, .open-question');
            if (!questionEl) return;

            const questionId = parseInt(questionEl.dataset.questionId);
            const type = questionEl.dataset.questionType;
            const quizType = questionEl.dataset.quizType;
            const attemptId = quizType === 'knowledge' ? quizAttemptId : openAttemptId;

            let selectedOptionIds = [];
            let answerText = null;
            let optionCustomTexts = {};

            if (type === 'single_choice' || type === 'true_false') {
                const checkedRadio = questionEl.querySelector('input[type="radio"]:checked');
                if (checkedRadio) {
                    selectedOptionIds.push(parseInt(checkedRadio.value));
                    const otherInput = checkedRadio.closest('.knowledge-choice, .open-question-choice').querySelector('.knowledge-other-input, .open-question-other-input');
                    if (otherInput) {
                        optionCustomTexts[checkedRadio.value] = otherInput.value;
                    }
                }
            } else if (type === 'multiple_choice') {
                questionEl.querySelectorAll('input[type="checkbox"]:checked').forEach(cb => {
                    selectedOptionIds.push(parseInt(cb.value));
                    const otherInput = cb.closest('.knowledge-choice, .open-question-choice').querySelector('.knowledge-other-input, .open-question-other-input');
                    if (otherInput) {
                        optionCustomTexts[cb.value] = otherInput.value;
                    }
                });
            } else if (type === 'open_text') {
                const textarea = questionEl.querySelector('textarea');
                if (textarea) {
                    answerText = textarea.value;
                }
            }

            saveAnswer(attemptId, questionId, selectedOptionIds, answerText, optionCustomTexts);
        }

        const nextBtn = document.getElementById('dynamic-next-button');
        if (nextBtn) {
            nextBtn.addEventListener('click', async function (event) {
                event.preventDefault();

                // Flush any pending save for the active element
                if (document.activeElement &&
                    (document.activeElement.classList.contains('knowledge-other-input') ||
                     document.activeElement.classList.contains('open-question-other-input') ||
                     document.activeElement.tagName === 'TEXTAREA')) {
                    triggerSave(document.activeElement);
                }

                let isValid = true;
                let firstInvalid = null;

                const forms = document.querySelectorAll('.knowledge-form, .open-question-form');
                forms.forEach(form => {
                    const requiredInputs = Array.from(form.querySelectorAll('[required]'));
                    requiredInputs.forEach(input => {
                        if (!input.checkValidity()) {
                            isValid = false;
                            if (!firstInvalid) {
                                firstInvalid = input;
                            }
                        }
                    });
                    form.classList.add('was-validated');
                });

                if (!isValid) {
                    if (firstInvalid) {
                        firstInvalid.reportValidity();
                    }
                    return;
                }

                nextBtn.disabled = true;
                const originalText = nextBtn.innerHTML;
                nextBtn.innerHTML = '<span>Đang gửi...</span>';

                try {
                    const submitPromises = [];
                    if (quizAttemptId) {
                        submitPromises.push(fetch(`/api/quiz-attempts/${quizAttemptId}/submit`, {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json' }
                        }));
                    }
                    if (openAttemptId) {
                        submitPromises.push(fetch(`/api/quiz-attempts/${openAttemptId}/submit`, {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json' }
                        }));
                    }

                    const responses = await Promise.all(submitPromises);
                    let allOk = true;
                    for (const res of responses) {
                        if (!res.ok) {
                            allOk = false;
                        }
                    }

                    if (allOk) {
                        window.location.href = '/confirm-post';
                    } else {
                        alert('Có lỗi xảy ra khi nộp bài. Vui lòng thử lại.');
                        nextBtn.disabled = false;
                        nextBtn.innerHTML = originalText;
                    }
                } catch (err) {
                    console.error(err);
                    alert('Có lỗi kết nối. Vui lòng thử lại.');
                    nextBtn.disabled = false;
                    nextBtn.innerHTML = originalText;
                }
            });
        }

        updateCustomInputs();
    })();
    </script>
<?php endif; ?>
