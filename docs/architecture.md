# Architecture

Security Culture Heatmap Portal is a PHP 8.x and MySQL 8.0 portal built around a small service-and-repository architecture.

## Layers

- `public/index.php` handles routing, security headers, rendering, and form submission.
- `src/Repository/PortalRepository.php` contains all database access through PDO prepared statements.
- `src/Service/ScoringService.php` calculates weighted maturity scores and maturity bands.
- `config/portal.json` defines the portal identity, book alignment, assessment dimensions, workflows, and seed initiatives.
- `database/migrations` and `database/seeders` provide repeatable MySQL setup.

## Book Alignment

This portal operationalizes **Appendix A: Security Culture Assessment Tools** from [Artificial Intelligence for Security Culture Transformation](https://www.amazon.com/Artificial-Intelligence-Security-Culture-Transformation/dp/3639876954) by converting the chapter theme into a measurable assessment and improvement workflow.

## Core Flow

1. Review dashboard dimensions and seeded improvement portfolio.
2. Capture a scored evidence record for a team, department, scenario, use case, or program.
3. Store dimension scores and weighted maturity result in MySQL.
4. Use the roadmap view to convert scores into owners, actions, and follow-up evidence.

## Extension Points

- Add additional dimensions in `config/portal.json` and seed them in the database.
- Add administrative roles before using the portal with sensitive operational data.
- Add exports, scheduled reminders, and workflow approvals as the program matures.
