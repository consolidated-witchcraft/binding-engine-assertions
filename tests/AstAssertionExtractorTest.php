<?php

declare(strict_types=1);

use ConsolidatedWitchcraft\BindingEngine\Assertions\AstAssertionExtractor;
use ConsolidatedWitchcraft\BindingEngine\Assertions\SourceContext;
use ConsolidatedWitchcraft\BindingEngine\Parser\Parser;
use ConsolidatedWitchcraft\BindingEngine\Vocabulary\Enums\BindingPayloadShapeEnum;

function makeExtractorSourceContext(): SourceContext
{
    return new SourceContext(
        sourceId: 'worldbook',
        documentId: '01JV7M9K6J0V8V3V5S2N6X4M1Q',
        revisionId: '01JV7MB3H3H4X9R8K7C2W1F5ZP',
        vocabularyIdentifier: 'test-vocabulary',
        vocabularyVersion: '0.1.0',
    );
}

it('extracts an empty assertion set from plain text', function () {
    $parser = new Parser();
    $extractor = new AstAssertionExtractor();

    $parseResult = $parser->parse('Just some ordinary prose.');
    $assertionSet = $extractor->extract(
        document: $parseResult->getDocument(),
        sourceContext: makeExtractorSourceContext(),
    );

    expect($assertionSet->isEmpty())->toBeTrue()
        ->and($assertionSet->count())->toBe(0)
        ->and($assertionSet->getAssertions())->toBe([]);
});

it('extracts a shorthand assertion', function () {
    $parser = new Parser();
    $extractor = new AstAssertionExtractor();

    $source = '@person[jane-austen](Jane Austen)';
    $sourceContext = makeExtractorSourceContext();

    $parseResult = $parser->parse($source);
    $assertionSet = $extractor->extract(
        document: $parseResult->getDocument(),
        sourceContext: $sourceContext,
    );

    expect($assertionSet->count())->toBe(1);

    $assertion = $assertionSet->first();

    expect($assertion)->not->toBeNull()
        ->and($assertion->getBindingType())->toBe('person')
        ->and($assertion->getPayloadShape())->toBe(BindingPayloadShapeEnum::Shorthand)
        ->and($assertion->getShorthandValue())->toBe('jane-austen')
        ->and($assertion->getAttributes())->toBe([])
        ->and($assertion->getLabel())->toBe('Jane Austen')
        ->and($assertion->getRaw())->toBe($source)
        ->and($assertion->getSourceSpan()->extract($source))->toBe($source)
        ->and($assertion->getSourceContext())->toBe($sourceContext);
});

it('extracts an attribute-list assertion', function () {
    $parser = new Parser();
    $extractor = new AstAssertionExtractor();

    $source = '@event[type:birth, subject:jane-austen](birth of Jane Austen)';

    $parseResult = $parser->parse($source);
    $assertionSet = $extractor->extract(
        document: $parseResult->getDocument(),
        sourceContext: makeExtractorSourceContext(),
    );

    expect($assertionSet->count())->toBe(1);

    $assertion = $assertionSet->first();

    expect($assertion)->not->toBeNull()
        ->and($assertion->getBindingType())->toBe('event')
        ->and($assertion->getPayloadShape())->toBe(BindingPayloadShapeEnum::AttributeList)
        ->and($assertion->getShorthandValue())->toBeNull()
        ->and($assertion->getAttributes())->toBe([
            'type' => ['birth'],
            'subject' => ['jane-austen'],
        ])
        ->and($assertion->getLabel())->toBe('birth of Jane Austen')
        ->and($assertion->getRaw())->toBe($source)
        ->and($assertion->getSourceSpan()->extract($source))->toBe($source);
});

it('extracts multiple assertions from mixed prose and bindings', function () {
    $parser = new Parser();
    $extractor = new AstAssertionExtractor();

    $source = 'Written by @person[jane-austen](Jane Austen) about @location[bath](Bath).';

    $parseResult = $parser->parse($source);
    $assertionSet = $extractor->extract(
        document: $parseResult->getDocument(),
        sourceContext: makeExtractorSourceContext(),
    );

    expect($assertionSet->count())->toBe(2);

    $assertions = $assertionSet->getAssertions();

    expect($assertions[0]->getBindingType())->toBe('person')
        ->and($assertions[0]->getShorthandValue())->toBe('jane-austen')
        ->and($assertions[0]->getLabel())->toBe('Jane Austen')
        ->and($assertions[0]->getRaw())->toBe('@person[jane-austen](Jane Austen)')
        ->and($assertions[0]->getSourceSpan()->extract($source))->toBe('@person[jane-austen](Jane Austen)')
        ->and($assertions[1]->getBindingType())->toBe('location')
        ->and($assertions[1]->getShorthandValue())->toBe('bath')
        ->and($assertions[1]->getLabel())->toBe('Bath')
        ->and($assertions[1]->getRaw())->toBe('@location[bath](Bath)')
        ->and($assertions[1]->getSourceSpan()->extract($source))->toBe('@location[bath](Bath)');
});

it('groups duplicate attribute assignments into value lists', function () {
    $parser = new Parser();
    $extractor = new AstAssertionExtractor();

    $source = '@event[tag:war, tag:historic, tag:political]';

    $parseResult = $parser->parse($source);
    $assertionSet = $extractor->extract(
        document: $parseResult->getDocument(),
        sourceContext: makeExtractorSourceContext(),
    );

    expect($assertionSet->count())->toBe(1);

    $assertion = $assertionSet->first();

    expect($assertion)->not->toBeNull()
        ->and($assertion->getAttributes())->toBe([
            'tag' => ['war', 'historic', 'political'],
        ])
        ->and($assertion->getAttributeValues('tag'))->toBe(['war', 'historic', 'political'])
        ->and($assertion->getFirstAttributeValue('tag'))->toBe('war');
});

it('does not extract assertions from malformed bindings parsed as text', function () {
    $parser = new Parser();
    $extractor = new AstAssertionExtractor();

    $source = '@person[] and ordinary text';

    $parseResult = $parser->parse($source);

    $assertionSet = $extractor->extract(
        document: $parseResult->getDocument(),
        sourceContext: makeExtractorSourceContext(),
    );

    expect($assertionSet->isEmpty())->toBeTrue()
        ->and($assertionSet->count())->toBe(0);
});
