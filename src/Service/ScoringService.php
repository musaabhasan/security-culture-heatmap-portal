<?php

declare(strict_types=1);

namespace Portal\Service;

final class ScoringService
{
    public function weightedScore(array $dimensions, array $scores): float
    {
        $weighted = 0.0;
        $totalWeight = 0.0;

        foreach ($dimensions as $dimension) {
            $key = (string) ($dimension['key'] ?? '');
            $weight = max(0.1, (float) ($dimension['weight'] ?? 1));
            $raw = max(1.0, min(5.0, (float) ($scores[$key] ?? 1)));
            $weighted += ($raw / 5.0) * 100.0 * $weight;
            $totalWeight += $weight;
        }

        return $totalWeight > 0 ? round($weighted / $totalWeight, 2) : 0.0;
    }

    public function maturityBand(float $score): string
    {
        return match (true) {
            $score >= 85 => 'Leading',
            $score >= 70 => 'Advancing',
            $score >= 55 => 'Developing',
            $score >= 40 => 'Foundational',
            default => 'Initial',
        };
    }
}
