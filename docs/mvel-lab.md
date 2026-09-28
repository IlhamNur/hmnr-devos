# MVEL Lab

The MVEL Lab provides an isolated playground to execute and evaluate Java MVEL expressions against JSON variable contexts.

## Architecture
Because MVEL evaluation requires a Java environment and can be prone to arbitrary execution vectors, the PHP application **does not** evaluate expressions locally.

Instead, the `MvelExecutorService` passes the expression and JSON variables to an isolated microservice (configured via `MVEL_EXECUTOR_URL`). 

## Developer Integration
- Maintains execution history for rapid iteration.
- Allows saving successful scripts directly to the Snippet vault for future re-use.

