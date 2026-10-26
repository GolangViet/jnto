<?php

declare(strict_types=1);

namespace App\Models;

use Core\Model;

final class Question extends Model
{
    public const STATUS_ACTIVE = 'active';
    public const STATUS_INACTIVE = 'inactive';
    public const STATUS_DELETE = 'delete';

    protected string $table = 'cms.questions';
}
