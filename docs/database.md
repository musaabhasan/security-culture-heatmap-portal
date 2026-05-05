# Database Schema

The portal uses MySQL 8.0 with `utf8mb4` collation.

## Tables

- `dimensions`: scoring dimensions and weights.
- `assessments`: assessment subject, weighted score, maturity band, and notes.
- `assessment_scores`: per-dimension scoring evidence.
- `initiatives`: improvement initiatives linked to impact areas.
- `evidence_items`: examples and supporting evidence records.
- `audit_events`: system activity trail.

## Portal Dimensions

- `leadership_modeling`: Leadership Modeling - Measures visible secure behavior and sponsorship from leaders.
- `shared_responsibility`: Shared Responsibility - Tracks whether security is treated as everyone's role.
- `psychological_safety`: Psychological Safety - Measures comfort reporting mistakes, concerns, and near misses.
- `security_norms`: Security Norms - Assesses whether secure behavior is socially reinforced.
- `learning_orientation`: Learning Orientation - Tracks post-incident learning and continuous improvement.
- `maturity_progression`: Maturity Progression - Measures movement across security culture maturity levels.

## Seeded Initiatives

- Quarterly culture pulse (Risk Office, high)
- Leadership signal campaign (Executive Office, high)
- Psychological safety reporting push (Security Culture, medium)
