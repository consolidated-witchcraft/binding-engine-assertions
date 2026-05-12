<?php

declare(strict_types=1);

namespace ConsolidatedWitchcraft\BindingEngine\Assertions;

use ConsolidatedWitchcraft\BindingEngine\Assertions\Exceptions\InvalidAssertionException;
use ConsolidatedWitchcraft\BindingEngine\Assertions\Interfaces\AssertionInterface;
use ConsolidatedWitchcraft\BindingEngine\Parser\Ast\Interfaces\SourceSpanInterface;
use ConsolidatedWitchcraft\BindingEngine\Vocabulary\Enums\BindingPayloadShapeEnum;

readonly class Assertion implements AssertionInterface
{
    /**
     * @param array<string, list<string>> $attributes
     *
     * @throws InvalidAssertionException
     */
    public function __construct(
        private string $bindingType,
        private BindingPayloadShapeEnum $payloadShape,
        private ?string $shorthandValue,
        private array $attributes,
        private ?string $label,
        private string $raw,
        private SourceSpanInterface $sourceSpan,
        private SourceContext $sourceContext,
    ) {
        $this->guard();
    }

    public function getBindingType(): string
    {
        return $this->bindingType;
    }

    public function getPayloadShape(): BindingPayloadShapeEnum
    {
        return $this->payloadShape;
    }

    public function getShorthandValue(): ?string
    {
        return $this->shorthandValue;
    }

    /**
     * @return array<string, list<string>>
     */
    public function getAttributes(): array
    {
        return $this->attributes;
    }

    public function getLabel(): ?string
    {
        return $this->label;
    }

    public function getRaw(): string
    {
        return $this->raw;
    }

    public function getSourceSpan(): SourceSpanInterface
    {
        return $this->sourceSpan;
    }

    public function getSourceContext(): SourceContext
    {
        return $this->sourceContext;
    }

    public function hasAttribute(string $identifier): bool
    {
        return array_key_exists($identifier, $this->attributes);
    }

    /**
     * @return list<string>
     */
    public function getAttributeValues(string $identifier): array
    {
        return $this->attributes[$identifier] ?? [];
    }

    public function getFirstAttributeValue(string $identifier): ?string
    {
        return $this->attributes[$identifier][0] ?? null;
    }

    /**
     * @throws InvalidAssertionException
     */
    private function guard(): void
    {
        if (trim($this->bindingType) === '') {
            throw new InvalidAssertionException('Assertion binding type must not be empty.');
        }

        if ($this->raw === '') {
            throw new InvalidAssertionException('Assertion raw source must not be empty.');
        }

        if ($this->label !== null && trim($this->label) === '') {
            throw new InvalidAssertionException('Assertion label must not be empty when provided.');
        }

        $this->guardPayloadShape();
        $this->guardAttributes();
    }

    /**
     * @throws InvalidAssertionException
     */
    private function guardPayloadShape(): void
    {
        if ($this->payloadShape === BindingPayloadShapeEnum::Shorthand) {
            if ($this->shorthandValue === null || trim($this->shorthandValue) === '') {
                throw new InvalidAssertionException(
                    'Shorthand assertions must provide a non-empty shorthand value.',
                );
            }

            if ($this->attributes !== []) {
                throw new InvalidAssertionException(
                    'Shorthand assertions must not provide attributes.',
                );
            }

            return;
        }

        if ($this->shorthandValue !== null) {
            throw new InvalidAssertionException(
                'Attribute-list assertions must not provide a shorthand value.',
            );
        }

        if ($this->attributes === []) {
            throw new InvalidAssertionException(
                'Attribute-list assertions must provide at least one attribute.',
            );
        }
    }

    /**
     * @throws InvalidAssertionException
     */
    private function guardAttributes(): void
    {
        foreach ($this->attributes as $identifier => $values) {
            if (trim($identifier) === '') {
                throw new InvalidAssertionException(
                    'Assertion attribute identifiers must be non-empty strings.',
                );
            }

            if ($values === []) {
                throw new InvalidAssertionException(
                    sprintf(
                        'Assertion attribute "%s" must contain at least one value.',
                        $identifier,
                    ),
                );
            }

            foreach ($values as $value) {
                if (trim($value) === '') {
                    throw new InvalidAssertionException(
                        sprintf(
                            'Assertion attribute "%s" must not contain empty values.',
                            $identifier,
                        ),
                    );
                }
            }
        }
    }
}
