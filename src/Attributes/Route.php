<?php

namespace TicTacToe\Attributes;

use Attribute;

#[Attribute(Attribute::TARGET_METHOD)]
readonly class Route
{
    public function __construct(
        public string $method,
        public string $path
    ) {
    }
}
