<?php $current = $post ?? []; ?>
<label>Title</label><input name="title" value="<?= e((string) old('title', $current['title'] ?? '')) ?>" required>
<label>Slug</label><input name="slug" value="<?= e((string) old('slug', $current['slug'] ?? '')) ?>">
<label>Content</label><textarea name="content" rows="8" required><?= e((string) old('content', $current['content'] ?? '')) ?></textarea>
<label>Status</label><select name="status"><option value="draft" <?= old('status', $current['status'] ?? '') === 'draft' ? 'selected' : '' ?>>Draft</option><option value="published" <?= old('status', $current['status'] ?? '') === 'published' ? 'selected' : '' ?>>Published</option></select>
