<?php

declare(strict_types=1);

namespace ConsolidatedWitchcraft\BindingEngine\Assertions\Interfaces;

use ConsolidatedWitchcraft\BindingEngine\Assertions\SourceContext;
use ConsolidatedWitchcraft\BindingEngine\Parser\Ast\Interfaces\SourceSpanInterface;
use ConsolidatedWitchcraft\BindingEngine\Vocabulary\Enums\BindingPayloadShapeEnum;

interface AssertionInterface
{
    public function getBindingType(): string;

    public function getPayloadShape(): BindingPayloadShapeEnum;

    public function getShorthandValue(): ?string;

    /**
     * @return array<string, list<string>>
     */
    public function getAttributes(): array;

    public function getLabel(): ?string;

    public function getRaw(): string;

    public function getSourceSpan(): SourceSpanInterface;

    public function getSourceContext(): SourceContext;

    public function hasAttribute(string $identifier): bool;

    /**
     * @return list<string>
     */
    public function getAttributeValues(string $identifier): array;

    public function getFirstAttributeValue(string $identifier): ?string;
}