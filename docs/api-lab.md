# API Lab

The API Lab provides an in-platform HTTP client for testing external APIs, integrated directly into the developer's memory workflow.

## Features
- Full request customization (Method, URL, Params, Headers, Body)
- Raw and Pretty-printed JSON response viewers
- Comprehensive History tracking
- **Save to Knowledge:** Instantly documents a successful API request and its response payload into the Knowledge Base as Markdown.

## Security Constraints
To safely allow server-side request execution:
1. Internal network IPs are strictly blocked.
2. DNS rebinding is protected via `CURLOPT_RESOLVE`.
3. Secrets in the `Authorization` header are scrubbed prior to history insertion.

