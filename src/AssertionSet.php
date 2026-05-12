<?php

declare(strict_types=1);

namespace ConsolidatedWitchcraft\BindingEngine\Assertions;

use ConsolidatedWitchcraft\BindingEngine\Assertions\Interfaces\AssertionSetInterface;

readonly class AssertionSet implements AssertionSetInterface
{
    /**
     * @param list<Assertion> $assertions
     */
    public function __construct(
        private array $assertions,
    ) {
    }

    /**
     * @return list<Assertion>
     */
    public function getAssertions(): array
    {
        return $this->assertions;
    }

    public function isEmpty(): bool
    {
        return $this->assertions === [];
    }

    public function count(): int
    {
        return count($this->assertions);
    }

    public function first(): ?Assertion
    {
        return $this->assertions[0] ?? null;
    }

    /**
     * @return list<Assertion>
     */
    public function getByBindingType(string $bindingType): array
    {
        $matches = [];

        foreach ($this->assertions as $assertion) {
            if ($assertion->getBindingType() === $bindingType) {
                $matches[] = $assertion;
            }
        }

        return $matches;
    }

    /**
     * @return list<Assertion>
     */
    public function getBySourceDocumentId(string $documentId): array
    {
        $matches = [];

        foreach ($this->assertions as $assertion) {
            if ($assertion->getSourceContext()->getDocumentId() === $documentId) {
                $matches[] = $assertion;
            }
        }

        return $matches;
    }

}
