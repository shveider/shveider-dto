<?php declare(strict_types=1);

namespace ShveiderDto\Attributes;

use Attribute;

#[Attribute(Attribute::TARGET_PROPERTY)]
readonly class Alias
{
    public function __construct(public string $name) {
    }
}

