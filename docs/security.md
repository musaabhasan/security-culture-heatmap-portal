# Security Notes

This application is intentionally small, but it includes baseline controls expected in a professional PHP/MySQL portal.

- PDO prepared statements for database writes and reads.
- CSRF protection on assessment submission.
- Security headers for framing, MIME sniffing, referrer policy, and content security policy.
- Environment-based configuration for credentials.
- MySQL least-privilege user in Docker development.
- Server-side validation for subject names and score values.
- Audit event creation when assessments are saved.

## Data Handling

Treat assessment notes and evidence as potentially sensitive. Before production use, define retention, access control, export, and anonymization rules.

## Production Recommendations

- Enforce HTTPS.
- Add authentication and role-based authorization.
- Store secrets in a managed secret store.
- Restrict database access to the application network.
- Log security events to centralized monitoring.
