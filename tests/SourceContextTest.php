<?php

declare(strict_types=1);

use ConsolidatedWitchcraft\BindingEngine\Assertions\Exceptions\InvalidSourceContextException;
use ConsolidatedWitchcraft\BindingEngine\Assertions\SourceContext;

it('constructs correctly', function () {
    $sourceId = 'worldbook';
    $documentId = '01JV7M9K6J0V8V3V5S2N6X4M1Q';
    $revisionId = '01JV7MB3H3H4X9R8K7C2W1F5ZP';
    $vocabularyIdentifier = 'test-vocabulary';
    $vocabularyVersion = '0.1.0';

    $sourceContext = new SourceContext(
        sourceId: $sourceId,
        documentId: $documentId,
        revisionId: $revisionId,
        vocabularyIdentifier: $vocabularyIdentifier,
        vocabularyVersion: $vocabularyVersion,
    );

    expect($sourceContext)->toBeInstanceOf(SourceContext::class)
        ->and($sourceContext->getSourceId())->toBe($sourceId)
        ->and($sourceContext->getDocumentId())->toBe($documentId)
        ->and($sourceContext->getRevisionId())->toBe($revisionId)
        ->and($sourceContext->getVocabularyIdentifier())->toBe($vocabularyIdentifier)
        ->and($sourceContext->getVocabularyVersion())->toBe($vocabularyVersion);
});

it(
    'rejects empty source identifiers',
    function (string $field, string $value) {
        $data = [
            'sourceId' => 'worldbook',
            'documentId' => '01JV7M9K6J0V8V3V5S2N6X4M1Q',
            'revisionId' => '01JV7MB3H3H4X9R8K7C2W1F5ZP',
            'vocabularyIdentifier' => 'test-vocabulary',
            'vocabularyVersion' => '0.1.0',
        ];

        $data[$field] = $value;

        expect(
            fn () => new SourceContext(
                sourceId: $data['sourceId'],
                documentId: $data['documentId'],
                revisionId: $data['revisionId'],
                vocabularyIdentifier: $data['vocabularyIdentifier'],
                vocabularyVersion: $data['vocabularyVersion'],
            )
        )->toThrow(
            InvalidSourceContextException::class,
            sprintf('Source context field "%s" must not be empty.', $field),
        );
    }
)->with(function (): iterable {
    yield 'empty sourceId' => ['sourceId', ''];
    yield 'whitespace sourceId' => ['sourceId', '   '];

    yield 'empty documentId' => ['documentId', ''];
    yield 'whitespace documentId' => ['documentId', '   '];

    yield 'empty revisionId' => ['revisionId', ''];
    yield 'whitespace revisionId' => ['revisionId', '   '];
});

it('wraps invalid vocabulary identifier validation failures', function () {
    expect(
        fn () => new SourceContext(
            sourceId: 'worldbook',
            documentId: '01JV7M9K6J0V8V3V5S2N6X4M1Q',
            revisionId: '01JV7MB3H3H4X9R8K7C2W1F5ZP',
            vocabularyIdentifier: 'INVALID IDENTIFIER',
            vocabularyVersion: '0.1.0',
        )
    )->toThrow(
        InvalidSourceContextException::class,
        'Invalid vocabulary context:',
    );
});

it('wraps invalid vocabulary version validation failures', function () {
    expect(
        fn () => new SourceContext(
            sourceId: 'worldbook',
            documentId: '01JV7M9K6J0V8V3V5S2N6X4M1Q',
            revisionId: '01JV7MB3H3H4X9R8K7C2W1F5ZP',
            vocabularyIdentifier: 'test-vocabulary',
            vocabularyVersion: '1.0',
        )
    )->toThrow(
        InvalidSourceContextException::class,
        'Invalid vocabulary context:',
    );
});
