# Developer Memory Search

The global search functionality is the cornerstone of "Don't solve the same problem twice."

## Implementation
- **Provider:** Currently utilizes standard SQL `ILIKE` clauses combined with `UNION ALL` across the five core entities (Projects, Problems, Solutions, Snippets, Knowledge).
- **UI:** A unified Command Palette triggered globally via `Ctrl/Cmd + K`.

## Evolution Path
While currently using traditional text matching for speed and simplicity, the architecture allows for future migration to full-text search (PostgreSQL `tsvector`) or vector-based embeddings (e.g., pgvector) without disrupting the UI.

