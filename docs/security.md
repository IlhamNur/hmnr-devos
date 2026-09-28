# Security Posture

## Authorization (IDOR Prevention)
Every core controller (Project, Problem, Snippet, Knowledge, ApiLab, MvelLab) validates resource ownership using Laravel Policies (`Gate::authorize()`). Users cannot view, modify, or delete resources belonging to other users.

## API Lab SSRF Protection
The API Lab acts as a server-side proxy. To prevent Server-Side Request Forgery:
- DNS resolution is performed locally in PHP via `gethostbyname()`.
- The resolved IP is checked against restricted IPv4 ranges (localhost, 10.x, 172.16.x, 192.168.x, 169.254.x).
- Guzzle uses `CURLOPT_RESOLVE` to force the request to use the validated IP, mitigating DNS Rebinding attacks.
- Redirects are limited to 3, and a custom `on_redirect` middleware verifies that redirect targets do not point to restricted internal IPs.
- Response size is capped at 10MB via streaming headers to prevent DoS.

## MVEL Sandboxing
Arbitrary code execution is mitigated by entirely externalizing the MVEL evaluation process to a remote executor service (`MVEL_EXECUTOR_URL`). Timeouts are enforced on the PHP side.

## Secret Redaction
The API Lab scrubs `Authorization` headers before logging request history to the database.

