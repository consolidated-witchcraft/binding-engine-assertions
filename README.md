# Binding Engine Assertions

A provenance-aware assertion extraction layer for the Consolidated Witchcraft BindingEngine ecosystem.

Binding Assertions transforms parsed and validated binding documents into structured semantic assertions suitable for inference, conflict detection, graph projection and downstream processing pipelines.

## Purpose

The Binding Engine parser answers:
> “What syntactically exists in this document?”

The vocabulary layer answers:

> “Is this binding semantically valid under this vocabulary?”

Binding Assertions answers:
> “What claims does this document make?”

This package acts as the bridge between validated binding syntax and higher-level world knowledge systems.

## Status
Early development.

The API should be considered unstable until 1.0.0.


## Installation
``` bash
composer require consolidated-witchcraft/binding-engine-assertions
```

## Conceptual Overview

Given a validated binding document:

```text
@event[
type: birth,
subject: jane-austen,
date: 1775-12-16
](birth of Jane Austen)
```

The assertion layer produces structured assertions describing:
- the binding type
- extracted attributes
- payload shape
- source provenance
- source spans
- associated vocabulary/version context

Example conceptual output:
```text
Assertion
├── bindingType: event
├── attributes:
│   ├── type = birth
│   ├── subject = jane-austen
│   └── date = 1775-12-16
├── payloadShape: attribute_list
├── sourceDocumentId: document-123
├── sourceRevisionId: revision-456
├── vocabulary: core-worldbook@0.1.0
└── sourceSpan: [0, 82)
```
The assertion layer does not:

- infer new facts
- resolve canon
- detect contradictions
- build graph projections

Those concerns belong to downstream systems.

## Design Goals
### Provenance First

Assertions preserve:
- source document identity
- revision identity
- vocabulary identity/version
- source spans
- original binding structure

Downstream systems should always be able to answer:
> “Where did this claim come from?”

### Vocabulary-Aware
Assertions are generated against a specific validated vocabulary.

This allows downstream systems to safely reason about:
- attribute semantics
- payload meanings
- vocabulary evolution
- migration between vocabulary versions

### No Implicit Inference
This library intentionally avoids:
- inference
- graph projection
- entity resolution
- truth reconciliation

Its responsibility ends at faithfully extracting explicit semantic assertions.

## Example Workflow
```text
Markdown Document
↓
Parser
↓
AST
↓
Vocabulary Validator
↓
Validated Document
↓
Binding Assertions
↓
Assertion Set
↓
Inference / Projection / Conflict Detection
```
## Planned Components
### Assertion Extractor
Transforms validated AST documents into assertion sets.

### Assertion Set
Immutable collection of extracted assertions.
### Source Context
Carries provenance metadata such as:
- document ID
- revision ID
- source system
- vocabulary identifier
- vocabulary version

### Assertion Types

Structured assertion representations for:
- entities
- relationships
- events
- attributes
- references

## Philosophy
Binding Assertions treats authored semantic bindings as historical claims rather than immediate truth.

This distinction is important.

Multiple documents may assert conflicting information. The role of this package is to preserve and expose those assertions faithfully — not to decide which is canonical.

## Related Packages

| Package                           | Responsibility                                |
|-----------------------------------|-----------------------------------------------|
| binding-engine-parser             | Parses binding syntax into AST structures     |
| binding-engine-parser             | Parses binding syntax into AST structures     |
| binding-engine-vocabulary	        | Defines semantic vocabulary rules             |
| binding-engine-vocabulary-loader	 | Loads vocabularies from JSON definitions      |
| binding-assertions                | Extracts provenance-aware semantic assertions |

## License
Licensed under the GNU Affero General Public License v3.0 or later (AGPL-3.0-or-later).