# AGENTS.md — BindingEngine Assertions Library

## README.md
The README.md contains valuable information about the structure of this project and should be consulted, read and followed.

## Purpose
This repository contains the assertion extraction layer for the Consolidated Witchcraft BindingEngine ecosystem.

The responsibility of this package is:

- extracting explicit semantic assertions from validated binding documents
- preserving provenance and source traceability
- producing immutable assertion structures suitable for downstream reasoning systems

This package MUST NOT:
- perform inference
- resolve canon
- reconcile contradictions
- perform entity resolution
- mutate source semantics
- project graph structures
- silently discard provenance information

The package exists to faithfully represent authored semantic claims.

---

# Architectural Principles

## Assertions Are Claims, Not Truth

Assertions represent:
> “This document claims X.”

They do NOT represent:
> “X is objectively true.”

Multiple conflicting assertions may coexist.

Conflict resolution belongs to downstream systems.

---

## Provenance Is Mandatory

Assertions MUST preserve provenance information.

This includes:
- source document identifiers
- source revision identifiers
- source spans
- vocabulary identifiers
- vocabulary versions

Downstream systems MUST be able to determine:
- where an assertion originated
- which vocabulary version produced it
- which source text created it

Loss of provenance is considered a serious architectural failure.

---

## Immutable Data Structures

Assertion objects SHOULD be immutable.

Mutation introduces ambiguity into provenance and downstream reasoning.

Prefer:
- readonly classes
- value objects
- constructor validation

Avoid:
- setters
- mutable collections
- hidden internal state

---

## Explicitness Over Implicit Behaviour

This package prioritises:
- deterministic extraction
- explicit semantics
- transparent data flow

Avoid:
- magical inference
- heuristic interpretation
- hidden transformations

If a semantic relationship is inferred rather than explicitly authored, it belongs in a downstream inference layer.

---

# Repository Standards

## PHP Standards

- `declare(strict_types=1);` is mandatory
- PHPStan MUST pass at maximum configured level
- `treatPhpDocTypesAsCertain: true` is enforced
- All public APIs MUST be fully typed
- Array shapes MUST be documented where appropriate
- Prefer small immutable value objects over associative arrays

---

## Exceptions

Exceptions MUST:
- be domain-specific
- carry meaningful contextual information
- preserve previous exceptions

Never throw:
- `\Exception`
- `\RuntimeException`
- `\Throwable`

except at application boundaries.

---

## Testing Standards

All behaviour MUST be covered by tests.

Tests SHOULD:
- validate successful construction paths
- validate failure paths
- validate edge cases
- validate provenance preservation
- validate deterministic output

Tests MUST:
- assert exact exception types
- assert exact diagnostic/error messages where stable
- avoid hidden coupling between test cases

Boundary tests are required for:
- identifier validation
- semantic version validation
- provenance handling
- duplicate detection
- malformed assertion structures

---

## Provenance Handling

Source provenance is first-class system data.

When introducing new assertion types or extraction paths:
- provenance MUST be preserved
- source spans MUST remain accurate
- vocabulary context MUST remain attached

Assertions without provenance are considered invalid architecture.

---

## Assertions vs Inference

Keep extraction and inference strictly separated.

This repository extracts:
- explicit authored semantic structures

It does NOT:
- derive new facts
- interpret causality
- reconcile conflicting information
- determine canonical truth

Do not introduce inference behaviour into extraction code.

---

## Vocabulary Compatibility

Assertions are generated against a specific vocabulary version.

Code MUST assume:
- vocabularies evolve over time
- assertion meaning may vary between versions
- downstream migration systems may exist

Never assume:
- vocabulary identifiers are globally stable without versions
- assertion semantics are timeless

Vocabulary version context MUST remain attached to assertions.

---

## Preferred Design Style

Prefer:
- composition over inheritance
- small focused services
- immutable DTOs/value objects
- explicit constructor validation
- deterministic transforms

Avoid:
- service locators
- hidden global state
- reflection-heavy behaviour
- runtime mutation
- implicit magic resolution

---

## Commit Standards

Commits MUST:
- pass the full test suite
- pass PHPStan
- preserve backwards compatibility unless intentionally breaking
- maintain provenance guarantees

Do not commit:
- failing tests
- partially implemented extraction logic
- dead code
- debugging artefacts

---

## Long-Term Direction

This package is intended to become:
- stable
- deterministic
- provenance-safe
- infrastructure-grade

Optimise for:
- correctness
- traceability
- maintainability
- semantic clarity

over:
- convenience
- hidden abstraction
- premature optimisation
- cleverness

## Coding Standards
Coding standards are contained within the `./codingstandards/` subdirectory, and MUST be followed.