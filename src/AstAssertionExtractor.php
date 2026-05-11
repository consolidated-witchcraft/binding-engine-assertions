<?php

declare(strict_types=1);

namespace ConsolidatedWitchcraft\BindingEngine\Assertions;

use ConsolidatedWitchcraft\BindingEngine\Assertions\Exceptions\AssertionExtractionException;
use ConsolidatedWitchcraft\BindingEngine\Assertions\Exceptions\InvalidAssertionException;
use ConsolidatedWitchcraft\BindingEngine\Assertions\Exceptions\InvalidAssertionSetException;
use ConsolidatedWitchcraft\BindingEngine\Assertions\Interfaces\AssertionExtractorInterface;
use ConsolidatedWitchcraft\BindingEngine\Parser\Ast\Nodes\AttributeListPayloadNode;
use ConsolidatedWitchcraft\BindingEngine\Parser\Ast\Nodes\BindingNode;
use ConsolidatedWitchcraft\BindingEngine\Parser\Ast\Nodes\DocumentNode;
use ConsolidatedWitchcraft\BindingEngine\Parser\Ast\Nodes\ShorthandPayloadNode;
use ConsolidatedWitchcraft\BindingEngine\Vocabulary\Enums\BindingPayloadShapeEnum;

final readonly class AstAssertionExtractor implements AssertionExtractorInterface
{
    /**
     * @throws AssertionExtractionException
     */
    public function extract(
        DocumentNode $document,
        SourceContext $sourceContext,
    ): AssertionSet {
        $assertions = [];

        foreach ($document->getChildren() as $child) {
            if (!$child instanceof BindingNode) {
                continue;
            }

            $assertions[] = $this->extractBindingAssertion(
                bindingNode: $child,
                sourceContext: $sourceContext,
            );
        }

        try {
            return new AssertionSet($assertions);
        } catch (InvalidAssertionSetException $exception) {
            throw new AssertionExtractionException(
                message: sprintf(
                    'Failed to construct assertion set: %s',
                    $exception->getMessage(),
                ),
                previous: $exception,
            );
        }
    }

    /**
     * @throws AssertionExtractionException
     */
    private function extractBindingAssertion(
        BindingNode $bindingNode,
        SourceContext $sourceContext,
    ): Assertion {
        $payload = $bindingNode->getPayload();

        if ($payload instanceof ShorthandPayloadNode) {
            return $this->extractShorthandAssertion(
                bindingNode: $bindingNode,
                payload: $payload,
                sourceContext: $sourceContext,
            );
        }

        if ($payload instanceof AttributeListPayloadNode) {
            return $this->extractAttributeListAssertion(
                bindingNode: $bindingNode,
                payload: $payload,
                sourceContext: $sourceContext,
            );
        }

        throw new AssertionExtractionException(
            sprintf(
                'Unsupported binding payload type "%s".',
                $payload::class,
            ),
        );
    }

    /**
     * @throws AssertionExtractionException
     */
    private function extractShorthandAssertion(
        BindingNode $bindingNode,
        ShorthandPayloadNode $payload,
        SourceContext $sourceContext,
    ): Assertion {
        try {
            return new Assertion(
                bindingType: $bindingNode->getBindingType(),
                payloadShape: BindingPayloadShapeEnum::Shorthand,
                shorthandValue: $payload->getValue(),
                attributes: [],
                label: $bindingNode->getLabel(),
                raw: $bindingNode->getRaw(),
                sourceSpan: $bindingNode->getSpan(),
                sourceContext: $sourceContext,
            );
        } catch (InvalidAssertionException $exception) {
            throw new AssertionExtractionException(
                message: sprintf(
                    'Failed to extract shorthand assertion: %s',
                    $exception->getMessage(),
                ),
                previous: $exception,
            );
        }
    }

    /**
     * @throws AssertionExtractionException
     */
    private function extractAttributeListAssertion(
        BindingNode $bindingNode,
        AttributeListPayloadNode $payload,
        SourceContext $sourceContext,
    ): Assertion {
        $attributes = [];

        foreach ($payload->getAttributes() as $attributeAssignment) {
            $identifier = $attributeAssignment->getIdentifier();

            if (!array_key_exists($identifier, $attributes)) {
                $attributes[$identifier] = [];
            }

            $attributes[$identifier][] = $attributeAssignment->getValue();
        }

        /** @var array<string, list<string>> $attributes */

        try {
            return new Assertion(
                bindingType: $bindingNode->getBindingType(),
                payloadShape: BindingPayloadShapeEnum::AttributeList,
                shorthandValue: null,
                attributes: $attributes,
                label: $bindingNode->getLabel(),
                raw: $bindingNode->getRaw(),
                sourceSpan: $bindingNode->getSpan(),
                sourceContext: $sourceContext,
            );
        } catch (InvalidAssertionException $exception) {
            throw new AssertionExtractionException(
                message: sprintf(
                    'Failed to extract attribute-list assertion: %s',
                    $exception->getMessage(),
                ),
                previous: $exception,
            );
        }
    }
}
