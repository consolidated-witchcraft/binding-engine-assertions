<?php

declare(strict_types=1);

namespace ConsolidatedWitchcraft\BindingEngine\Assertions\Interfaces;

use ConsolidatedWitchcraft\BindingEngine\Assertions\AssertionSet;
use ConsolidatedWitchcraft\BindingEngine\Assertions\Exceptions\AssertionExtractionException;
use ConsolidatedWitchcraft\BindingEngine\Assertions\SourceContext;
use ConsolidatedWitchcraft\BindingEngine\Parser\Ast\Nodes\DocumentNode;

interface AssertionExtractorInterface
{
    /**
     * @throws AssertionExtractionException
     */
    public function extract(
        DocumentNode $document,
        SourceContext $sourceContext,
    ): AssertionSet;
}
