<?php

declare(strict_types=1);

use Core\Database;

return new class {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $db = Database::connection();

        // 1. Insert Quiz
        $quizStmt = $db->prepare("
            INSERT INTO cms.quizzes (title, description, status, duration_minutes, pass_score, show_result, show_correct_answer, allow_resume)
            VALUES (:title, :description, :status, :duration_minutes, :pass_score, :show_result, :show_correct_answer, :allow_resume)
            RETURNING id
        ");
        $quizStmt->execute([
            'title' => 'Khám phá du lịch Nhật Bản (JNTO)',
            'description' => 'Bài kiểm tra kiến thức về văn hóa, lễ hội, địa lý và phong cảnh nổi tiếng tại Nhật Bản theo gợi ý của website JNTO.',
            'status' => 'published',
            'duration_minutes' => 15,
            'pass_score' => 60.0,
            'show_result' => true,
            'show_correct_answer' => true,
            'allow_resume' => true,
        ]);
        $quizId = (int) $quizStmt->fetchColumn();

        // 2. Insert Question & Options
        $qStmt = $db->prepare("
            INSERT INTO cms.questions (quiz_id, type, question_text, explanation, score, is_required, display_order)
            VALUES (:quiz_id, :type, :question_text, :explanation, :score, :is_required, :display_order)
            RETURNING id
        ");
        $optStmt = $db->prepare("
            INSERT INTO cms.question_options (question_id, option_key, option_text, is_correct, display_order)
            VALUES (:question_id, :option_key, :option_text, :is_correct, :display_order)
        ");
        $ansStmt = $db->prepare("
            INSERT INTO cms.question_accepted_answers (question_id, answer_text, normalized_answer, match_type)
            VALUES (:question_id, :answer_text, :normalized_answer, :match_type)
        ");

        // Question 1 (Gifu fishing)
        $qStmt->execute([
            'quiz_id' => $quizId,
            'type' => 'single_choice',
            'question_text' => 'Tỉnh Gifu nổi tiếng với trải nghiệm độc đáo nào diễn ra trên dòng sông Nagara?',
            'explanation' => 'Nghệ thuật đánh cá bằng chim cốc truyền thống (Ukai) trên sông Nagara ở Gifu có lịch sử hơn 1300 năm.',
            'score' => 2.0,
            'is_required' => true,
            'display_order' => 1,
        ]);
        $q1Id = (int) $qStmt->fetchColumn();
        $optStmt->execute(['question_id' => $q1Id, 'option_key' => 'A', 'option_text' => 'Múa rồng lửa trên thuyền gỗ vượt thác nước khoáng', 'is_correct' => 0, 'display_order' => 1]);
        $optStmt->execute(['question_id' => $q1Id, 'option_key' => 'B', 'option_text' => 'Thả đèn hoa đăng bằng đá ngầm phát sáng', 'is_correct' => 0, 'display_order' => 2]);
        $optStmt->execute(['question_id' => $q1Id, 'option_key' => 'C', 'option_text' => 'Nghệ thuật đánh cá bằng chim cốc truyền thống', 'is_correct' => 1, 'display_order' => 3]);

        // Question 2 (Fukuoka festival)
        $qStmt->execute([
            'quiz_id' => $quizId,
            'type' => 'single_choice',
            'question_text' => 'Nếu bạn ghé thăm tỉnh Fukuoka vào mùa hè, lễ hội nào không nên bỏ lỡ?',
            'explanation' => 'Lễ hội Hakata Gion Yamakasa diễn ra vào tháng 7 hàng năm tại đền Kushida, Fukuoka.',
            'score' => 2.0,
            'is_required' => true,
            'display_order' => 2,
        ]);
        $q2Id = (int) $qStmt->fetchColumn();
        $optStmt->execute(['question_id' => $q2Id, 'option_key' => 'A', 'option_text' => 'Lễ hội Hakata Gion Yamakasa', 'is_correct' => 1, 'display_order' => 1]);
        $optStmt->execute(['question_id' => $q2Id, 'option_key' => 'B', 'option_text' => 'Lễ hội đèn lồng Nagasaki', 'is_correct' => 0, 'display_order' => 2]);
        $optStmt->execute(['question_id' => $q2Id, 'option_key' => 'C', 'option_text' => 'Lễ hội Thành Kumamoto', 'is_correct' => 0, 'display_order' => 3]);

        // Question 3 (Kyushu volcano)
        $qStmt->execute([
            'quiz_id' => $quizId,
            'type' => 'single_choice',
            'question_text' => 'Địa hình đặc trưng nào của vùng Kyushu tạo nên cảnh quan độc nhất vô nhị?',
            'explanation' => 'Vùng Kyushu nổi tiếng với các hoạt động núi lửa sôi sục, các suối nước nóng (onsen) và địa hình địa nhiệt phun khói đặc trưng.',
            'score' => 2.0,
            'is_required' => true,
            'display_order' => 3,
        ]);
        $q3Id = (int) $qStmt->fetchColumn();
        $optStmt->execute(['question_id' => $q3Id, 'option_key' => 'A', 'option_text' => 'Hệ thống hang động băng vĩnh cửu', 'is_correct' => 0, 'display_order' => 1]);
        $optStmt->execute(['question_id' => $q3Id, 'option_key' => 'B', 'option_text' => 'Địa hình núi lửa sôi sục phun khói', 'is_correct' => 1, 'display_order' => 2]);
        $optStmt->execute(['question_id' => $q3Id, 'option_key' => 'C', 'option_text' => 'Những cánh đồng sa mạc', 'is_correct' => 0, 'display_order' => 3]);

        // Question 4 (Ishikawa foliage)
        $qStmt->execute([
            'quiz_id' => $quizId,
            'type' => 'open_text',
            'question_text' => 'Theo gợi ý trên website JNTO, đâu là nơi lý tưởng nhất để ngắm phong cảnh tán lá đổi màu tại tỉnh Ishikawa?',
            'explanation' => 'Vườn Kenrokuen (một trong ba khu vườn kiệt tác của Nhật Bản) và hẻm núi Kakusenkei ở Ishikawa là các địa điểm ngắm lá đỏ mùa thu tuyệt vời.',
            'score' => 2.0,
            'is_required' => true,
            'display_order' => 4,
        ]);
        $q4Id = (int) $qStmt->fetchColumn();
        $ansStmt->execute(['question_id' => $q4Id, 'answer_text' => 'Vườn Kenrokuen và Hẻm núi Kakusenkei', 'normalized_answer' => \App\Helpers\TextNormalizer::normalize('Vườn Kenrokuen và Hẻm núi Kakusenkei'), 'match_type' => 'exact']);
        $ansStmt->execute(['question_id' => $q4Id, 'answer_text' => 'Kenrokuen và Kakusenkei', 'normalized_answer' => \App\Helpers\TextNormalizer::normalize('Kenrokuen và Kakusenkei'), 'match_type' => 'exact']);

        // Question 5 (Saga foliage)
        $qStmt->execute([
            'quiz_id' => $quizId,
            'type' => 'open_text',
            'question_text' => 'Theo gợi ý trên website JNTO, ngôi chùa nào là địa điểm lý tưởng để ngắm lá đỏ mùa thu tại tỉnh Saga?',
            'explanation' => 'Chùa Daikozenji ở Saga là một điểm ngắm hoa đỗ quyên mùa xuân và lá đỏ mùa thu nổi tiếng.',
            'score' => 2.0,
            'is_required' => true,
            'display_order' => 5,
        ]);
        $q5Id = (int) $qStmt->fetchColumn();
        $ansStmt->execute(['question_id' => $q5Id, 'answer_text' => 'Chùa Daikozenji', 'normalized_answer' => \App\Helpers\TextNormalizer::normalize('Chùa Daikozenji'), 'match_type' => 'exact']);
        $ansStmt->execute(['question_id' => $q5Id, 'answer_text' => 'Daikozenji', 'normalized_answer' => \App\Helpers\TextNormalizer::normalize('Daikozenji'), 'match_type' => 'exact']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $db = Database::connection();
        $db->exec("DELETE FROM cms.quizzes WHERE title = 'Khám phá du lịch Nhật Bản (JNTO)'");
    }
};
