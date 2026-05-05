<?php

declare(strict_types=1);

namespace Portal\Repository;

use PDO;
use Portal\Support\Uuid;

final class PortalRepository
{
    public function __construct(private readonly ?PDO $db)
    {
    }

    public function connected(): bool
    {
        return $this->db instanceof PDO;
    }

    public function summary(): array
    {
        if (!$this->connected()) {
            return [
                'connected' => false,
                'assessment_count' => 0,
                'initiative_count' => 0,
                'average_score' => null,
                'latest_assessment_at' => null,
            ];
        }

        return [
            'connected' => true,
            'assessment_count' => (int) $this->db->query('SELECT COUNT(*) FROM assessments')->fetchColumn(),
            'initiative_count' => (int) $this->db->query('SELECT COUNT(*) FROM initiatives')->fetchColumn(),
            'average_score' => $this->nullableFloat($this->db->query('SELECT AVG(weighted_score) FROM assessments')->fetchColumn()),
            'latest_assessment_at' => $this->db->query('SELECT MAX(created_at) FROM assessments')->fetchColumn() ?: null,
        ];
    }

    public function recentAssessments(int $limit = 5): array
    {
        if (!$this->connected()) {
            return [];
        }

        $stmt = $this->db->prepare('SELECT * FROM assessments ORDER BY created_at DESC LIMIT :limit');
        $stmt->bindValue('limit', $limit, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    public function dimensionAverages(): array
    {
        if (!$this->connected()) {
            return [];
        }

        $rows = $this->db->query('SELECT dimension_key, AVG(score) AS average_score FROM assessment_scores GROUP BY dimension_key')->fetchAll();
        $averages = [];
        foreach ($rows as $row) {
            $averages[$row['dimension_key']] = round((float) $row['average_score'], 2);
        }

        return $averages;
    }

    public function initiatives(): array
    {
        if (!$this->connected()) {
            return [];
        }

        return $this->db->query('SELECT * FROM initiatives ORDER BY FIELD(priority, "high", "medium", "low"), title')->fetchAll();
    }

    public function evidenceItems(): array
    {
        if (!$this->connected()) {
            return [];
        }

        return $this->db->query('SELECT * FROM evidence_items ORDER BY created_at DESC LIMIT 6')->fetchAll();
    }

    public function createAssessment(string $subjectName, string $subjectType, array $scores, string $notes, float $weightedScore): string
    {
        if (!$this->connected()) {
            throw new \RuntimeException('Database connection is not available.');
        }

        $uuid = Uuid::v4();
        $this->db->beginTransaction();
        try {
            $stmt = $this->db->prepare(
                'INSERT INTO assessments (uuid, subject_name, subject_type, weighted_score, maturity_band, notes, created_at)
                 VALUES (:uuid, :subject_name, :subject_type, :weighted_score, :maturity_band, :notes, UTC_TIMESTAMP())'
            );
            $stmt->execute([
                'uuid' => $uuid,
                'subject_name' => $subjectName,
                'subject_type' => $subjectType,
                'weighted_score' => $weightedScore,
                'maturity_band' => $this->band($weightedScore),
                'notes' => $notes,
            ]);

            $assessmentId = (int) $this->db->lastInsertId();
            $scoreStmt = $this->db->prepare(
                'INSERT INTO assessment_scores (assessment_id, dimension_key, score, evidence, created_at)
                 VALUES (:assessment_id, :dimension_key, :score, :evidence, UTC_TIMESTAMP())'
            );
            foreach ($scores as $key => $score) {
                $scoreStmt->execute([
                    'assessment_id' => $assessmentId,
                    'dimension_key' => (string) $key,
                    'score' => (float) $score,
                    'evidence' => '',
                ]);
            }

            $audit = $this->db->prepare('INSERT INTO audit_events (action, actor, payload_json, created_at) VALUES (:action, :actor, :payload_json, UTC_TIMESTAMP())');
            $audit->execute([
                'action' => 'assessment.created',
                'actor' => 'portal-user',
                'payload_json' => json_encode(['uuid' => $uuid, 'subject_name' => $subjectName], JSON_UNESCAPED_SLASHES),
            ]);

            $this->db->commit();
            return $uuid;
        } catch (\Throwable $exception) {
            $this->db->rollBack();
            throw $exception;
        }
    }

    private function nullableFloat(mixed $value): ?float
    {
        return $value === null ? null : round((float) $value, 2);
    }

    private function band(float $score): string
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
