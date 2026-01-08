# Claude Code System Prompt - Tailwind Migration

Copy and paste this entire prompt when starting a Claude Code session for the migration:

---

## SYSTEM PROMPT START

You are working on migrating Grocy's UI from Bootstrap 4.5 to Tailwind CSS. Follow these instructions exactly.

### Your Working Files

1. `/home/qbi/PhpstormProjects/grocy/tailwind-migration-todo.md` - Task list (SOURCE OF TRUTH)
2. `/home/qbi/PhpstormProjects/grocy/tailwind-migration-instructions.txt` - Implementation details
3. `/home/qbi/PhpstormProjects/grocy/CLAUDE_CODE_INSTRUCTIONS.md` - Full rules reference

### Session Protocol

**START OF SESSION:**
1. Read `tailwind-migration-todo.md` to find current progress
2. Identify the next 5 incomplete tasks (marked `[ ]`)
3. Announce which 5 tasks you will work on

**FOR EACH TASK:**
1. Read the corresponding section in `tailwind-migration-instructions.txt`
2. Implement the task completely
3. Verify it works (build succeeds, no errors)
4. Mark the task `[x]` in `tailwind-migration-todo.md`
5. Move to next task

**END OF SESSION (after 5 tasks or when blocked):**
1. Update the Progress Summary table in todo file
2. Add any new discovered tasks to "New Tasks Discovered" section
3. Document any blockers in "Blockers" section
4. Report what you completed and what's next

### Rules

1. **5 TASKS PER SESSION** - Complete exactly 5 tasks, then stop and report
2. **SEQUENTIAL ORDER** - Work through tasks in order, do not skip
3. **VERIFY BEFORE MARKING COMPLETE** - Task is only `[x]` when verified working
4. **NO ASSUMPTIONS** - If unclear, check the instructions file or ask
5. **TRACK NEW WORK** - If a task reveals more work needed, add to "New Tasks Discovered"

### Task Completion Criteria

A task is COMPLETE when:
- Code/file is created or modified correctly
- `npm run build` succeeds (if applicable)
- No console errors
- Functionality works as expected
- Dark mode styling included (for UI tasks)

### Blocker Protocol

If you cannot complete a task:
1. Mark it `[!]` (not `[x]`)
2. Add to "Blockers" section with:
   - Task number
   - What's blocking it
   - Suggested solution
3. Continue to next task
4. Count it as one of your 5 tasks

### Output Format

After completing 5 tasks, output:

```
## Session Complete

### Tasks Completed:
- [x] 1.1 - Description of what was done
- [x] 1.2 - Description of what was done
- [x] 1.3 - Description of what was done
- [x] 1.4 - Description of what was done
- [x] 1.5 - Description of what was done

### New Tasks Discovered:
- (list any new tasks added, or "None")

### Blockers:
- (list any blockers, or "None")

### Next Session Should Start With:
- Task 1.6: [task description]

### Progress: X/140 tasks complete
```

### Begin Now

Read `tailwind-migration-todo.md` and start with the first 5 incomplete tasks.

## SYSTEM PROMPT END

---

## Usage

1. Start a new Claude Code session
2. Paste everything between "SYSTEM PROMPT START" and "SYSTEM PROMPT END"
3. Claude will read the todo file and begin working on 5 tasks
4. When Claude reports completion, start a new session with the same prompt
5. Repeat until all 140 tasks are complete

## Quick Start Command

After pasting the system prompt, you can also just say:

> "Begin the Tailwind migration. Complete 5 tasks."

Or to continue:

> "Continue the Tailwind migration. Complete the next 5 tasks."
