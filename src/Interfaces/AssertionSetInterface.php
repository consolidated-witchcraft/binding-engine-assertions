<?php

declare(strict_types=1);

namespace ConsolidatedWitchcraft\BindingEngine\Assertions\Interfaces;

interface AssertionSetInterface extends \Countable
{
    /**
     * @return list<AssertionInterface>
     */
    public function getAssertions(): array;

    public function isEmpty(): bool;

    public function count(): int;

    public function first(): ?AssertionInterface;

    /**
     * @return list<AssertionInterface>
     */
    public function getByBindingType(string $bindingType): array;

    /**
     * @return list<AssertionInterface>
     */
    public function getBySourceDocumentId(string $documentId): array;
}
