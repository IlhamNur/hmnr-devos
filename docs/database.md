# Database Schema

## Core Tables

### Users
Standard Laravel users table handling authentication.

### Projects
- `id`, `user_id`, `name`, `slug`, `status`
- Taxonomical root for issues.

### Problems
- `id`, `user_id`, `project_id`, `title`, `slug`, `severity`, `status`
- Foreign keys set to cascade on delete. 
- Represents a discrete issue encountered.

### Solutions
- `id`, `user_id`, `problem_id`, `content`, `solution_type`
- Belongs to a Problem.

### Snippets & Knowledge
- Both serve as Developer Memory vaults. Snippets target raw code with `language` tags, while Knowledge targets Markdown content (`type` = reference, documentation, etc).

### API Requests & MVEL Executions
- History tables for the Developer Labs. Used to rapidly resume prior testing states.
- Auth tokens and secrets are redacted from `api_requests` before insertion.

