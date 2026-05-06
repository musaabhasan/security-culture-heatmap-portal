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

<!-- portfolio:start -->
## Portfolio and Professional Profile

This repository is part of the professional portfolio of [Musaab Hasan](https://musaab.info), focused on cybersecurity, digital forensics, AI governance, EdTech, secure platforms, and research-driven digital transformation.

### Digital Forensics and Security Research Labs

- [Android Digital Forensics Lab](https://github.com/musaabhasan/android-forensics-lab) - Advanced Android forensics workbench for acquisition planning, anti-forensics evaluation, memory triage, evidence integrity, and case reconstruction.
- [Humanoid Robot Forensics Lab](https://github.com/musaabhasan/humanoid-robot-forensics-lab) - PHP/MySQL forensic casework platform for humanoid robot, companion app, and IoT evidence triage.
- [Smart Metering Security Lab](https://github.com/musaabhasan/smart-metering-security-lab) - Research portal based on smart metering security analysis for cyber-physical and smart-grid environments.
- [Drive-by Download ML Lab](https://github.com/musaabhasan/driveby-download-ml-lab) - Machine learning research portal for detecting drive-by download attacks and web-based malware delivery.
- [SQL Injection ML Detection Lab](https://github.com/musaabhasan/sqli-ml-detection-lab) - Research portal for SQL injection detection using machine learning and security telemetry.
- [IoT Board SSH Hardening Lab](https://github.com/musaabhasan/iot-board-ssh-hardening-lab) - SSH exposure assessment and hardening portal for IoT development boards and embedded Linux systems.
- [ZigBee WHAS Design Lab](https://github.com/musaabhasan/zigbee-whas-design-lab) - Research portal for designing and evaluating ZigBee wireless home automation systems.
- [Mammogram Fourier Analysis Lab](https://github.com/musaabhasan/mammogram-fourier-analysis-lab) - Medical image-processing research portal based on Fourier transform analysis for mammography.

### Security Culture and Transformation Platforms

- [Human Factors Risk Profiler](https://github.com/musaabhasan/human-factors-risk-profiler) - Human-centered security risk profiling portal for targeted interventions and behavior-aware controls.
- [Security Champion Network Portal](https://github.com/musaabhasan/security-champion-network-portal) - Platform for managing security champion networks, missions, recognition, and measurable impact.
- [Crisis Simulation Command Portal](https://github.com/musaabhasan/crisis-simulation-command-portal) - Cyber crisis simulation planning, scoring, and improvement platform for resilience exercises.
- [Behavioral Security Metrics Portal](https://github.com/musaabhasan/behavioral-security-metrics-portal) - Evidence-based security awareness metrics portal focused on behavior, culture, and intervention outcomes.
- [Security Culture Heatmap Portal](https://github.com/musaabhasan/security-culture-heatmap-portal) - Security culture maturity heatmap for norms, leadership signals, and organizational readiness.
- [Emerging Technology Security Culture Portal](https://github.com/musaabhasan/emerging-technology-security-culture-portal) - Adoption-readiness portal for emerging technology, governance, and security culture alignment.
- [AI Use Case Evaluation Portal](https://github.com/musaabhasan/ai-use-case-evaluation-portal) - Evaluation platform for AI use cases across value, feasibility, data readiness, privacy, ethics, and governance.
- [Transformation Roadmap Portal](https://github.com/musaabhasan/transformation-roadmap-portal) - Roadmap platform for moving security culture programs from compliance orientation to resilience and measurable change.

### Governance, Education, and Secure Enablement

- [Professional Development Registration System Framework](https://github.com/musaabhasan/pdrs-framework) - Secure registration and Moodle enrollment automation framework for professional development programs.
- [Multilingual Certificate Issuer](https://github.com/musaabhasan/multilingual-certificate-issuer) - Arabic/English certificate design, PDF generation, and throttled SMTP distribution platform.
- [AI Security Governance Toolkit](https://github.com/musaabhasan/ai-security-governance-toolkit) - Practical AI security governance controls, templates, evidence registers, playbooks, and policy-as-code examples.

Professional profile and research portfolio: [https://musaab.info](https://musaab.info)
<!-- portfolio:end -->
