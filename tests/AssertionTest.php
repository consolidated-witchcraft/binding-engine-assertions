<?php

declare(strict_types=1);

use ConsolidatedWitchcraft\BindingEngine\Assertions\Assertion;
use ConsolidatedWitchcraft\BindingEngine\Assertions\Exceptions\InvalidAssertionException;
use ConsolidatedWitchcraft\BindingEngine\Assertions\SourceContext;
use ConsolidatedWitchcraft\BindingEngine\Parser\Ast\SourceSpan;
use ConsolidatedWitchcraft\BindingEngine\Vocabulary\Enums\BindingPayloadShapeEnum;

function makeSourceContext(): SourceContext
{
    return new SourceContext(
        sourceId: 'worldbook',
        documentId: '01JV7M9K6J0V8V3V5S2N6X4M1Q',
        revisionId: '01JV7MB3H3H4X9R8K7C2W1F5ZP',
        vocabularyIdentifier: 'test-vocabulary',
        vocabularyVersion: '0.1.0',
    );
}

it('constructs correctly for a shorthand assertion', function () {
    $sourceSpan = new SourceSpan(0, 27);
    $sourceContext = makeSourceContext();

    $assertion = new Assertion(
        bindingType: 'person',
        payloadShape: BindingPayloadShapeEnum::Shorthand,
        shorthandValue: 'jane-austen',
        attributes: [],
        label: 'Jane Austen',
        raw: '@person[jane-austen](Jane Austen)',
        sourceSpan: $sourceSpan,
        sourceContext: $sourceContext,
    );

    expect($assertion->getBindingType())->toBe('person')
        ->and($assertion->getPayloadShape())->toBe(BindingPayloadShapeEnum::Shorthand)
        ->and($assertion->getShorthandValue())->toBe('jane-austen')
        ->and($assertion->getAttributes())->toBe([])
        ->and($assertion->getLabel())->toBe('Jane Austen')
        ->and($assertion->getRaw())->toBe('@person[jane-austen](Jane Austen)')
        ->and($assertion->getSourceSpan())->toBe($sourceSpan)
        ->and($assertion->getSourceContext())->toBe($sourceContext);
});

it('constructs correctly for an attribute-list assertion', function () {
    $sourceSpan = new SourceSpan(0, 45);
    $sourceContext = makeSourceContext();

    $attributes = [
        'type' => ['birth'],
        'subject' => ['jane-austen'],
    ];

    $assertion = new Assertion(
        bindingType: 'event',
        payloadShape: BindingPayloadShapeEnum::AttributeList,
        shorthandValue: null,
        attributes: $attributes,
        label: 'birth of Jane Austen',
        raw: '@event[type:birth, subject:jane-austen]',
        sourceSpan: $sourceSpan,
        sourceContext: $sourceContext,
    );

    expect($assertion->getBindingType())->toBe('event')
        ->and($assertion->getPayloadShape())->toBe(BindingPayloadShapeEnum::AttributeList)
        ->and($assertion->getShorthandValue())->toBeNull()
        ->and($assertion->getAttributes())->toBe($attributes)
        ->and($assertion->hasAttribute('type'))->toBeTrue()
        ->and($assertion->hasAttribute('missing'))->toBeFalse()
        ->and($assertion->getAttributeValues('type'))->toBe(['birth'])
        ->and($assertion->getFirstAttributeValue('type'))->toBe('birth')
        ->and($assertion->getAttributeValues('missing'))->toBe([])
        ->and($assertion->getFirstAttributeValue('missing'))->toBeNull()
        ->and($assertion->getLabel())->toBe('birth of Jane Austen')
        ->and($assertion->getRaw())->toBe('@event[type:birth, subject:jane-austen]')
        ->and($assertion->getSourceSpan())->toBe($sourceSpan)
        ->and($assertion->getSourceContext())->toBe($sourceContext);
});

it('rejects an empty binding type', function () {
    expect(
        fn () => new Assertion(
            bindingType: '   ',
            payloadShape: BindingPayloadShapeEnum::Shorthand,
            shorthandValue: 'jane-austen',
            attributes: [],
            label: null,
            raw: '@person[jane-austen]',
            sourceSpan: new SourceSpan(0, 20),
            sourceContext: makeSourceContext(),
        )
    )->toThrow(
        InvalidAssertionException::class,
        'Assertion binding type must not be empty.',
    );
});

it('rejects an empty raw source', function () {
    expect(
        fn () => new Assertion(
            bindingType: 'person',
            payloadShape: BindingPayloadShapeEnum::Shorthand,
            shorthandValue: 'jane-austen',
            attributes: [],
            label: null,
            raw: '',
            sourceSpan: new SourceSpan(0, 20),
            sourceContext: makeSourceContext(),
        )
    )->toThrow(
        InvalidAssertionException::class,
        'Assertion raw source must not be empty.',
    );
});

it('rejects an empty label when provided', function () {
    expect(
        fn () => new Assertion(
            bindingType: 'person',
            payloadShape: BindingPayloadShapeEnum::Shorthand,
            shorthandValue: 'jane-austen',
            attributes: [],
            label: '   ',
            raw: '@person[jane-austen]',
            sourceSpan: new SourceSpan(0, 20),
            sourceContext: makeSourceContext(),
        )
    )->toThrow(
        InvalidAssertionException::class,
        'Assertion label must not be empty when provided.',
    );
});

it('rejects shorthand assertions without a shorthand value', function (?string $value) {
    expect(
        fn () => new Assertion(
            bindingType: 'person',
            payloadShape: BindingPayloadShapeEnum::Shorthand,
            shorthandValue: $value,
            attributes: [],
            label: null,
            raw: '@person[]',
            sourceSpan: new SourceSpan(0, 9),
            sourceContext: makeSourceContext(),
        )
    )->toThrow(
        InvalidAssertionException::class,
        'Shorthand assertions must provide a non-empty shorthand value.',
    );
})->with(function (): iterable {
    yield 'null' => null;
    yield 'empty string' => '';
    yield 'whitespace' => '   ';
});

it('rejects shorthand assertions with attributes', function () {
    expect(
        fn () => new Assertion(
            bindingType: 'person',
            payloadShape: BindingPayloadShapeEnum::Shorthand,
            shorthandValue: 'jane-austen',
            attributes: [
                'subject' => ['jane-austen'],
            ],
            label: null,
            raw: '@person[jane-austen]',
            sourceSpan: new SourceSpan(0, 20),
            sourceContext: makeSourceContext(),
        )
    )->toThrow(
        InvalidAssertionException::class,
        'Shorthand assertions must not provide attributes.',
    );
});

it('rejects attribute-list assertions with a shorthand value', function () {
    expect(
        fn () => new Assertion(
            bindingType: 'event',
            payloadShape: BindingPayloadShapeEnum::AttributeList,
            shorthandValue: 'jane-austen',
            attributes: [
                'type' => ['birth'],
            ],
            label: null,
            raw: '@event[type:birth]',
            sourceSpan: new SourceSpan(0, 18),
            sourceContext: makeSourceContext(),
        )
    )->toThrow(
        InvalidAssertionException::class,
        'Attribute-list assertions must not provide a shorthand value.',
    );
});

it('rejects attribute-list assertions without attributes', function () {
    expect(
        fn () => new Assertion(
            bindingType: 'event',
            payloadShape: BindingPayloadShapeEnum::AttributeList,
            shorthandValue: null,
            attributes: [],
            label: null,
            raw: '@event[]',
            sourceSpan: new SourceSpan(0, 8),
            sourceContext: makeSourceContext(),
        )
    )->toThrow(
        InvalidAssertionException::class,
        'Attribute-list assertions must provide at least one attribute.',
    );
});

it('rejects empty attribute identifiers', function () {
    expect(
        fn () => new Assertion(
            bindingType: 'event',
            payloadShape: BindingPayloadShapeEnum::AttributeList,
            shorthandValue: null,
            attributes: [
                '' => ['birth'],
            ],
            label: null,
            raw: '@event[type:birth]',
            sourceSpan: new SourceSpan(0, 18),
            sourceContext: makeSourceContext(),
        )
    )->toThrow(
        InvalidAssertionException::class,
        'Assertion attribute identifiers must be non-empty strings.',
    );
});

it('rejects attributes with no values', function () {
    expect(
        fn () => new Assertion(
            bindingType: 'event',
            payloadShape: BindingPayloadShapeEnum::AttributeList,
            shorthandValue: null,
            attributes: [
                'type' => [],
            ],
            label: null,
            raw: '@event[type:]',
            sourceSpan: new SourceSpan(0, 14),
            sourceContext: makeSourceContext(),
        )
    )->toThrow(
        InvalidAssertionException::class,
        'Assertion attribute "type" must contain at least one value.',
    );
});

it('rejects attributes with empty values', function () {
    expect(
        fn () => new Assertion(
            bindingType: 'event',
            payloadShape: BindingPayloadShapeEnum::AttributeList,
            shorthandValue: null,
            attributes: [
                'type' => ['   '],
            ],
            label: null,
            raw: '@event[type: ]',
            sourceSpan: new SourceSpan(0, 15),
            sourceContext: makeSourceContext(),
        )
    )->toThrow(
        InvalidAssertionException::class,
        'Assertion attribute "type" must not contain empty values.',
    );
});

it('preserves provenance fields through an assertion', function () {
    $sourceSpan = new SourceSpan(10, 42);

    $sourceContext = new SourceContext(
        sourceId: 'worldbook',
        documentId: '01JV7M9K6J0V8V3V5S2N6X4M1Q',
        revisionId: '01JV7MB3H3H4X9R8K7C2W1F5ZP',
        vocabularyIdentifier: 'test-vocabulary',
        vocabularyVersion: '0.1.0',
    );

    $raw = '@person[jane-austen](Jane Austen)';

    $assertion = new Assertion(
        bindingType: 'person',
        payloadShape: BindingPayloadShapeEnum::Shorthand,
        shorthandValue: 'jane-austen',
        attributes: [],
        label: 'Jane Austen',
        raw: $raw,
        sourceSpan: $sourceSpan,
        sourceContext: $sourceContext,
    );

    expect($assertion->getSourceContext()->getSourceId())->toBe('worldbook')
        ->and($assertion->getSourceContext()->getDocumentId())->toBe('01JV7M9K6J0V8V3V5S2N6X4M1Q')
        ->and($assertion->getSourceContext()->getRevisionId())->toBe('01JV7MB3H3H4X9R8K7C2W1F5ZP')
        ->and($assertion->getSourceContext()->getVocabularyIdentifier())->toBe('test-vocabulary')
        ->and($assertion->getSourceContext()->getVocabularyVersion())->toBe('0.1.0')
        ->and($assertion->getRaw())->toBe($raw)
        ->and($assertion->getSourceSpan())->toBe($sourceSpan);
});
