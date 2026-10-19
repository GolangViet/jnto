<?php
/**
 * Reusable dropdown component.
 *
 * @var string $id Unique identifier for the dropdown
 * @var string $buttonHtml HTML/Text content for the button
 * @var string $buttonStyle Inline styles for the button
 * @var array $items Array of items:
 *   - 'type' => 'link'|'button'|'form'|'divider'
 *   - 'text' => string (display text)
 *   - 'url' => string (for links)
 *   - 'onclick' => string (for button clicks)
 *   - 'action' => string (for forms)
 *   - 'method' => string (for forms, e.g. DELETE)
 *   - 'confirm' => string (for form submit confirmation)
 *   - 'style' => string (for custom style, e.g. color: red)
 *   - 'target' => string (target for links, e.g. _blank)
 */
?>
<div class="dropdown" style="position: relative; display: inline-block;">
    <button onclick="toggleDropdown(event, '<?= e($id) ?>')" class="btn" style="<?= $buttonStyle ?? '' ?>">
        <?= $buttonHtml ?>
    </button>
    <div id="dropdown-<?= e($id) ?>" class="dropdown-menu" style="display: none; position: absolute; right: 0; margin-top: 4px; background: #ffffff; border: 1px solid #e5e7eb; border-radius: 8px; box-shadow: 0 10px 15px -3px rgba(0,0,0,0.1), 0 4px 6px -2px rgba(0,0,0,0.05); min-width: 160px; z-index: 50; padding: 4px 0; text-align: left;">
        <?php foreach ($items as $item): ?>
            <?php if (($item['type'] ?? 'link') === 'divider'): ?>
                <div style="border-top: 1px solid #e5e7eb; margin: 4px 0;"></div>
            <?php elseif (($item['type'] ?? 'link') === 'link'): ?>
                <a href="<?= e($item['url']) ?>" 
                   target="<?= e($item['target'] ?? '_self') ?>"
                   style="display: block; padding: 8px 16px; color: #374151; text-decoration: none; font-size: 0.85rem; font-weight: 500; transition: background 0.15s; <?= $item['style'] ?? '' ?>" 
                   onmouseover="this.style.background='#f3f4f6'" 
                   onmouseout="this.style.background='transparent'">
                    <?= e($item['text']) ?>
                </a>
            <?php elseif (($item['type'] ?? 'link') === 'button'): ?>
                <button onclick="<?= $item['onclick'] ?>" 
                        id="<?= e($item['id'] ?? '') ?>"
                        style="display: block; width: 100%; text-align: left; background: none; border: none; padding: 8px 16px; color: #374151; font-size: 0.85rem; font-weight: 500; cursor: pointer; transition: background 0.15s; <?= $item['style'] ?? '' ?>" 
                        onmouseover="this.style.background='#f3f4f6'" 
                        onmouseout="this.style.background='transparent'">
                    <?= e($item['text']) ?>
                </button>
            <?php elseif (($item['type'] ?? 'link') === 'form'): ?>
                <form action="<?= e($item['action']) ?>" method="post" style="display: block; margin: 0;" <?= !empty($item['confirm']) ? 'onsubmit="return confirm(\'' . e($item['confirm']) . '\');"' : '' ?>>
                    <?= csrf_field() ?>
                    <?php if (!empty($item['method'])): ?>
                        <input type="hidden" name="_method" value="<?= e($item['method']) ?>">
                    <?php endif; ?>
                    <button type="submit" 
                            style="display: block; width: 100%; text-align: left; background: none; border: none; padding: 8px 16px; color: #374151; font-size: 0.85rem; font-weight: 500; cursor: pointer; transition: background 0.15s; <?= $item['style'] ?? '' ?>" 
                            onmouseover="this.style.background='#f3f4f6'" 
                            onmouseout="this.style.background='transparent'">
                        <?= e($item['text']) ?>
                    </button>
                </form>
            <?php endif; ?>
        <?php endforeach; ?>
    </div>
</div>
