<?php

declare(strict_types=1);

namespace ConsolidatedWitchcraft\BindingEngine\Assertions;

use ConsolidatedWitchcraft\BindingEngine\Assertions\Exceptions\InvalidSourceContextException;
use ConsolidatedWitchcraft\BindingEngine\Vocabulary\Exceptions\InvalidVocabularyException;
use ConsolidatedWitchcraft\BindingEngine\Vocabulary\Vocabulary;

readonly class SourceContext
{
    /**
     * @throws InvalidSourceContextException|InvalidVocabularyException
     */
    public function __construct(
        private string $sourceId,
        private string $documentId,
        private string $revisionId,
        private string $vocabularyIdentifier,
        private string $vocabularyVersion,
    ) {
        $this->guard();
    }

    public function getSourceId(): string
    {
        return $this->sourceId;
    }

    public function getDocumentId(): string
    {
        return $this->documentId;
    }

    public function getRevisionId(): string
    {
        return $this->revisionId;
    }

    public function getVocabularyIdentifier(): string
    {
        return $this->vocabularyIdentifier;
    }

    public function getVocabularyVersion(): string
    {
        return $this->vocabularyVersion;
    }

    /**
     * @throws InvalidSourceContextException
     */
    private function guard(): void
    {
        $this->guardRequiredIdentifier(
            value: $this->sourceId,
            fieldName: 'sourceId',
        );

        $this->guardRequiredIdentifier(
            value: $this->documentId,
            fieldName: 'documentId',
        );

        $this->guardRequiredIdentifier(
            value: $this->revisionId,
            fieldName: 'revisionId',
        );

        try {
            Vocabulary::assertValidIdentifier($this->vocabularyIdentifier);
            Vocabulary::assertValidVocabularyVersion($this->vocabularyVersion);
        } catch (InvalidVocabularyException $exception) {
            throw new InvalidSourceContextException(
                message: sprintf(
                    'Invalid vocabulary context: %s',
                    $exception->getMessage(),
                ),
                previous: $exception,
            );
        }
    }

    /**
     * @throws InvalidSourceContextException
     */
    private function guardRequiredIdentifier(string $value, string $fieldName): void
    {
        if (trim($value) === '') {
            throw new InvalidSourceContextException(
                sprintf('Source context field "%s" must not be empty.', $fieldName),
            );
        }
    }
}
