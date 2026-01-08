# Claude Code Instructions for Tailwind Migration

## Overview

This file provides instructions for Claude Code to systematically execute the Tailwind CSS migration for Grocy. Follow these rules strictly.

---

## Files You Need

1. **tailwind-migration-instructions.txt** - Detailed implementation guide for each phase
2. **tailwind-migration-todo.md** - Task tracking list (THE SOURCE OF TRUTH)

---

## Core Rules

### Rule 1: Sequential Execution
- Work through `tailwind-migration-todo.md` tasks IN ORDER
- Complete all tasks in Phase N before starting Phase N+1
- Within a phase, complete tasks in numerical order
- Exception: If blocked, note the blocker and proceed to next available task

### Rule 2: Task Completion Standards
A task is ONLY complete when:
- The code/file has been created or modified
- The change has been tested and verified working
- No errors appear in console or build output
- The task checkbox is marked [x] in the todo file

### Rule 3: Never Skip Verification
- After each phase, run through the verification checklist
- If any verification item fails, fix it before proceeding
- Update the todo file to mark verification items complete

### Rule 4: Track New Tasks
- If implementation reveals new required tasks, ADD them to "New Tasks Discovered" section
- Do not start new tasks until current task is complete
- New tasks should be added with a reference to which task revealed them

### Rule 5: Document Blockers
- If you cannot complete a task, add it to "Blockers" section with:
  - Task number
  - Description of the blocker
  - Potential solutions
- Mark the task with [!] instead of [x]

### Rule 6: Update Progress
- After completing tasks, update the "Progress Summary" table
- Update "Last Updated" date

---

## How to Process Each Task

### For Setup Tasks (Phase 1-2)
1. Read the corresponding section in `tailwind-migration-instructions.txt`
2. Execute the required commands or create the required files
3. Verify the expected outcome
4. Mark task complete in todo file

### For Component Tasks (Phase 3-4)
1. Read the instructions for the component
2. Create the file with the required functionality
3. Test that it works with existing code
4. Mark task complete in todo file

### For Page Migration Tasks (Phase 6)
1. Read the original template file
2. Identify all Bootstrap classes used
3. Map each Bootstrap class to Tailwind equivalent
4. Create the updated template
5. Test the page in browser (if possible) or verify build succeeds
6. Check dark mode styling
7. Check responsive behavior
8. Mark task complete in todo file

---

## Session Start Protocol

When starting a new session:

1. Read `tailwind-migration-todo.md` to see current progress
2. Identify the first incomplete task (marked [ ])
3. Read relevant section of `tailwind-migration-instructions.txt`
4. Begin work on that task
5. Continue until session ends or all tasks complete

---

## Session End Protocol

Before ending a session:

1. Mark all completed tasks in `tailwind-migration-todo.md`
2. Update the Progress Summary table
3. Add any notes about decisions made
4. Document any blockers encountered
5. Note which task to start with next

---

## Build and Test Commands

```bash
# Start development server
npm run dev

# Build for production
npm run build

# Preview production build
npm run preview
```

---

## Quality Standards

### Code Quality
- Use consistent indentation (tabs for Blade, 2 spaces for CSS/JS)
- Follow existing naming conventions in the codebase
- Add comments only when logic is not self-evident
- Keep files focused - one component per file

### Design Quality
- Match the minimal/clean design aesthetic
- Use the defined color palette - no arbitrary colors
- Maintain proper spacing and alignment
- Ensure dark mode works for every element

### Compatibility
- Maintain all existing functionality
- Existing JS code must continue to work
- Feature flags must continue to hide/show elements
- Permission checks must continue to work

---

## Common Patterns

### Button Classes
```html
<!-- Primary -->
<button class="btn-primary">Save</button>

<!-- Secondary -->
<button class="btn-secondary">Cancel</button>

<!-- Danger -->
<button class="btn-danger">Delete</button>

<!-- Ghost (minimal) -->
<button class="btn-ghost">More</button>
```

### Card Pattern
```html
<div class="card">
  <div class="card-header">Title</div>
  <div class="card-body">Content</div>
</div>
```

### Form Pattern
```html
<div class="space-y-4">
  <div>
    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
      Label
    </label>
    <input type="text" class="input">
  </div>
</div>
```

### Dark Mode Pattern
```html
<!-- Always include dark: variants for colors -->
<div class="bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100">
  Content
</div>
```

---

## Do NOT Do

- Do NOT skip tasks or work out of order
- Do NOT mark tasks complete without verification
- Do NOT introduce new dependencies without noting them
- Do NOT change existing functionality unless required
- Do NOT use Bootstrap classes in new code
- Do NOT use inline styles - use Tailwind classes
- Do NOT create new files outside the defined structure
- Do NOT commit code that doesn't build

---

## Success Criteria

The migration is complete when:
1. All tasks in `tailwind-migration-todo.md` are marked [x]
2. All verification checklists pass
3. The application runs without Bootstrap
4. CSS bundle is < 50KB after purge
5. No console errors
6. All features work as before
