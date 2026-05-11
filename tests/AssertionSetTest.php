<?php

declare(strict_types=1);

use ConsolidatedWitchcraft\BindingEngine\Assertions\Assertion;
use ConsolidatedWitchcraft\BindingEngine\Assertions\AssertionSet;
use ConsolidatedWitchcraft\BindingEngine\Assertions\SourceContext;
use ConsolidatedWitchcraft\BindingEngine\Parser\Ast\SourceSpan;
use ConsolidatedWitchcraft\BindingEngine\Vocabulary\Enums\BindingPayloadShapeEnum;

function makeAssertionSetSourceContext(string $documentId = '01JV7M9K6J0V8V3V5S2N6X4M1Q'): SourceContext
{
    return new SourceContext(
        sourceId: 'worldbook',
        documentId: $documentId,
        revisionId: '01JV7MB3H3H4X9R8K7C2W1F5ZP',
        vocabularyIdentifier: 'test-vocabulary',
        vocabularyVersion: '0.1.0',
    );
}

function makeAssertionForSet(
    string $bindingType,
    SourceContext $sourceContext,
): Assertion {
    return new Assertion(
        bindingType: $bindingType,
        payloadShape: BindingPayloadShapeEnum::Shorthand,
        shorthandValue: 'test-value',
        attributes: [],
        label: null,
        raw: sprintf('@%s[test-value]', $bindingType),
        sourceSpan: new SourceSpan(0, 20),
        sourceContext: $sourceContext,
    );
}

it('constructs correctly with no assertions', function () {
    $assertionSet = new AssertionSet([]);

    expect($assertionSet)->toBeInstanceOf(AssertionSet::class)
        ->and($assertionSet->getAssertions())->toBe([])
        ->and($assertionSet->isEmpty())->toBeTrue()
        ->and($assertionSet->count())->toBe(0)
        ->and($assertionSet->first())->toBeNull();
});

it('constructs correctly with assertions', function () {
    $sourceContext = makeAssertionSetSourceContext();

    $personAssertion = makeAssertionForSet('person', $sourceContext);
    $eventAssertion = makeAssertionForSet('event', $sourceContext);

    $assertions = [
        $personAssertion,
        $eventAssertion,
    ];

    $assertionSet = new AssertionSet($assertions);

    expect($assertionSet->getAssertions())->toBe($assertions)
        ->and($assertionSet->isEmpty())->toBeFalse()
        ->and($assertionSet->count())->toBe(2)
        ->and($assertionSet->first())->toBe($personAssertion);
});

it('filters assertions by binding type', function () {
    $sourceContext = makeAssertionSetSourceContext();

    $personAssertion = makeAssertionForSet('person', $sourceContext);
    $eventAssertion = makeAssertionForSet('event', $sourceContext);
    $secondPersonAssertion = makeAssertionForSet('person', $sourceContext);

    $assertionSet = new AssertionSet([
        $personAssertion,
        $eventAssertion,
        $secondPersonAssertion,
    ]);

    expect($assertionSet->getByBindingType('person'))->toBe([
        $personAssertion,
        $secondPersonAssertion,
    ])
        ->and($assertionSet->getByBindingType('event'))->toBe([
            $eventAssertion,
        ])
        ->and($assertionSet->getByBindingType('missing'))->toBe([]);
});

it('filters assertions by source document id', function () {
    $firstSourceContext = makeAssertionSetSourceContext();

    $secondSourceContext = makeAssertionSetSourceContext(
        documentId: '01JV7N0R9N1P8D4S6M7B8C9D0E',
    );

    $firstAssertion = makeAssertionForSet('person', $firstSourceContext);
    $secondAssertion = makeAssertionForSet('event', $secondSourceContext);

    $assertionSet = new AssertionSet([
        $firstAssertion,
        $secondAssertion,
    ]);

    expect($assertionSet->getBySourceDocumentId('01JV7M9K6J0V8V3V5S2N6X4M1Q'))->toBe([
        $firstAssertion,
    ])
        ->and($assertionSet->getBySourceDocumentId('01JV7N0R9N1P8D4S6M7B8C9D0E'))->toBe([
            $secondAssertion,
        ])
        ->and($assertionSet->getBySourceDocumentId('missing'))->toBe([]);
});
