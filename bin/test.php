<?php

declare(strict_types=1);

use Portal\Service\ScoringService;
use Portal\Support\Json;

require __DIR__ . '/../src/bootstrap.php';

$config = Json::decode((string) file_get_contents(__DIR__ . '/../config/portal.json'), []);

function assertTrue(bool $condition, string $message): void
{
    if (!$condition) {
        throw new RuntimeException($message);
    }
}

$dimensions = $config['dimensions'] ?? [];
assertTrue(count($dimensions) >= 5, 'Portal should define at least five assessment dimensions.');

$keys = array_map(fn (array $dimension): string => (string) $dimension['key'], $dimensions);
assertTrue(count($keys) === count(array_unique($keys)), 'Dimension keys must be unique.');

foreach ($dimensions as $dimension) {
    assertTrue(preg_match('/^[a-z][a-z0-9_]{2,79}$/', (string) $dimension['key']) === 1, 'Dimension keys must be stable identifiers.');
    assertTrue((float) ($dimension['weight'] ?? 0) > 0, 'Dimension weights must be positive.');
}

$scoring = new ScoringService();
$leadingScores = array_fill_keys($keys, 5);
$initialScores = array_fill_keys($keys, 1);
$mixedScores = array_fill_keys($keys, 3);

assertTrue($scoring->weightedScore($dimensions, $leadingScores) === 100.0, 'All leading scores should produce 100%.');
assertTrue($scoring->maturityBand(100.0) === 'Leading', '100% should be Leading.');
assertTrue($scoring->weightedScore($dimensions, $initialScores) === 20.0, 'All initial scores should produce 20%.');
assertTrue($scoring->maturityBand(20.0) === 'Initial', '20% should be Initial.');
assertTrue($scoring->weightedScore($dimensions, $mixedScores) === 60.0, 'All developing scores should produce 60%.');
assertTrue($scoring->maturityBand(60.0) === 'Developing', '60% should be Developing.');

assertTrue(str_contains((string) ($config['book']['url'] ?? ''), 'amazon.com'), 'Book reference should include the public Amazon listing.');
assertTrue((string) ($config['book']['title'] ?? '') !== '', 'Book title is required.');

echo 'test-suite-ok' . PHP_EOL;
