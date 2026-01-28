<?php

declare(strict_types=1);

namespace Larexsetch\LaravelTransactional\Attributes;

use Attribute;

#[Attribute(Attribute::TARGET_METHOD)]
final class Transactional
{
    public function __construct(
        public ?string $connection = null
    ) {
    }
}
