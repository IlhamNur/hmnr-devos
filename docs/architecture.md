# HMNR DevOS Architecture

## Overview
HMNR DevOS is built as a monolithic Laravel application using a modern TALL stack (Tailwind, Alpine, Laravel, Livewire) approach, but heavily relies on standard Blade components and Alpine.js for interactivity.

## Core Principles
- **Developer Memory:** All entities (Problems, Solutions, Snippets, Knowledge, Projects) are interconnected. Global search unifies these domains so the user never solves the same problem twice.
- **Server-Side Rendering:** HTML is rendered server-side with Blade, enhanced with Alpine.js for minimal client-side state.
- **Microservice Extension:** While mostly monolithic, untrusted operations (like MVEL evaluation) are offloaded to isolated microservices to preserve the main application's integrity.

## Key Modules
1. **Projects:** The top-level taxonomy.
2. **Problems & Solutions:** A structured debugging log and case study system.
3. **Snippets & Knowledge:** A code vault and markdown-based reference library.
4. **Labs:** Interactive tools (API request builder, MVEL playground) that plug back into the memory system.

