# Security Culture Heatmap Portal

A culture analytics portal for visualizing maturity, norms, leadership signals, and risk-aware behaviors.

This repository provides a production-minded PHP 8.x and MySQL 8.0 portal aligned with themes from Musaab Hasan's book, [Artificial Intelligence for Security Culture Transformation](https://www.amazon.com/Artificial-Intelligence-Security-Culture-Transformation/dp/3639876954). It translates the book's security culture transformation concepts into a working application foundation that can be extended for institutional, enterprise, and professional development contexts.

## Book Alignment

- Primary reference: **Appendix A: Security Culture Assessment Tools**
- Transformation theme: Culture maturity, leadership modeling, social norms, assessment evidence, and heatmap-driven improvement.
- Broader framing: moving from compliance-centered activity to measurable security culture, adaptive learning, and organizational resilience.

## Core Capabilities

- Role or unit assessment intake with CSRF protection and server-side validation.
- Weighted scoring model with maturity bands.
- MySQL schema for assessments, dimension scores, initiatives, evidence, and audit records.
- Dashboard view with maturity score, assessment volume, initiative tracking, and dimension cards.
- Roadmap view for implementation workflows and improvement planning.
- JSON summary endpoint for integration with reporting tools.
- Docker-based local development environment.
- Lint and self-test scripts for maintainability.

## Assessment Dimensions

- **Leadership Modeling**: Measures visible secure behavior and sponsorship from leaders.
- **Shared Responsibility**: Tracks whether security is treated as everyone's role.
- **Psychological Safety**: Measures comfort reporting mistakes, concerns, and near misses.
- **Security Norms**: Assesses whether secure behavior is socially reinforced.
- **Learning Orientation**: Tracks post-incident learning and continuous improvement.
- **Maturity Progression**: Measures movement across security culture maturity levels.

## Operating Workflow

- Assess culture dimensions by department or stakeholder group.
- Generate heatmaps that highlight uneven maturity and hidden weak spots.
- Prioritize interventions by cultural risk and business criticality.
- Reassess quarterly and document movement with evidence.

## Quick Start

```bash
cp .env.example .env
docker compose up --build
```

Then open:

- Application: `http://localhost:8080`
- Health endpoint: `http://localhost:8080/health`
- JSON summary: `http://localhost:8080/api/summary`

## Local Checks

```bash
php bin/lint.php
php bin/test.php
```

## Repository Structure

```text
public/              Web entry point and assets
src/                 PHP application services, repository, and support classes
config/              Portal configuration and scoring dimensions
database/            MySQL migration and seed data
docs/                Architecture, security, testing, and extension documentation
bin/                 Developer and release checks
```

## Production Notes

- Store database credentials and application secrets outside source control.
- Enforce HTTPS at the reverse proxy or load balancer.
- Use least-privilege database users.
- Route logs and audit records to approved monitoring systems.
- Review assessment data retention rules before collecting identifiable responses.

## License

MIT License. See [LICENSE](LICENSE).
