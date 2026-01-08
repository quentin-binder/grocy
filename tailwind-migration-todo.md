# Tailwind Migration Todo List

## Instructions for Claude Code

**IMPORTANT RULES:**
1. Work through tasks IN ORDER - do not skip ahead
2. Mark a task [x] ONLY when it is FULLY COMPLETE and VERIFIED
3. If a task reveals new sub-tasks, ADD them to this list under "New Tasks Discovered"
4. Before starting any task, read the corresponding section in `tailwind-migration-instructions.txt`
5. After completing each phase, STOP and verify all items before proceeding
6. If you encounter a blocker, add it to the "Blockers" section and continue with next available task

**Status Legend:**
- [ ] Not started
- [~] In progress
- [x] Complete and verified
- [!] Blocked

---

## Phase 1: Build System Setup

- [x] 1.1 Install Vite, Tailwind, PostCSS, Autoprefixer as dev dependencies
- [x] 1.2 Install Alpine.js, @alpinejs/collapse, Tippy.js, Flatpickr, focus-trap as dependencies
- [x] 1.3 Create /vite.config.js with PHP-compatible configuration
- [x] 1.4 Create /tailwind.config.js with content paths and dark mode class strategy
- [x] 1.5 Create /postcss.config.js with Tailwind and Autoprefixer plugins
- [x] 1.6 Create directory structure: /resources/css/, /resources/css/vendors/, /resources/js/, /resources/js/components/
- [x] 1.7 Create /resources/css/app.css with Tailwind directives
- [x] 1.8 Create /resources/js/app.js with Alpine.js setup
- [x] 1.9 Create /helpers/ViteHelper.php with asset loading function
- [x] 1.10 Add "dev", "build", "preview" scripts to package.json
- [x] 1.11 Add GROCY_USE_TAILWIND setting to /config-dist.php
- [x] 1.12 Update .gitignore to exclude /public/build/ and /node_modules/

### Phase 1 Verification Checklist
- [x] `npm run dev` starts Vite server without errors
- [x] `npm run build` creates /public/build/manifest.json
- [x] No console errors

---

## Phase 2: Design System

- [x] 2.1 Add primary color palette (blue) to tailwind.config.js
- [x] 2.2 Add gray color palette (zinc-based) to tailwind.config.js
- [x] 2.3 Add semantic colors (success, warning, danger, info) to tailwind.config.js
- [x] 2.4 Add font family (Inter) to tailwind.config.js
- [x] 2.5 Add custom font sizes to tailwind.config.js
- [x] 2.6 Add custom border radius and box shadows to tailwind.config.js
- [x] 2.7 Create button component classes in /resources/css/app.css
- [x] 2.8 Create form input component classes in /resources/css/app.css
- [x] 2.9 Create card component classes in /resources/css/app.css
- [x] 2.10 Create badge component classes in /resources/css/app.css

### Phase 2 Verification Checklist
- [x] Build completes without errors
- [x] Component classes are in output CSS

---

## Phase 3: Layout & Navigation

- [x] 3.1 Create /views/layout/default-tailwind.blade.php base structure
- [x] 3.2 Implement sidebar HTML with Alpine.js collapse functionality
- [x] 3.3 Port all navigation items from original layout (feature flags, permissions)
- [x] 3.4 Implement header with mobile menu button and page title
- [x] 3.5 Create /views/layout/partials/sidebar-nav.blade.php
- [x] 3.6 Create /views/layout/partials/header-actions.blade.php
- [x] 3.7 Add conditional include to original /views/layout/default.blade.php
- [x] 3.8 Port Grocy JS object configuration to new layout
- [x] 3.9 Implement dark mode class toggle on html element
- [x] 3.10 Test and fix responsive behavior

### Phase 3 Verification Checklist
- [x] Application loads with Tailwind layout when flag enabled
- [x] Sidebar expands/collapses on desktop
- [x] Mobile menu opens/closes
- [x] All navigation links work
- [x] Feature flags hide/show correct menu items
- [x] Dark mode toggles correctly
- [x] Clock displays correctly

---

## Phase 4: Alpine.js Components

- [ ] 4.1 Create /resources/js/components/modal.js with GrocyModal compatibility
- [ ] 4.2 Create /resources/js/components/dropdown.js
- [ ] 4.3 Create /resources/js/components/tooltip.js with Tippy.js
- [ ] 4.4 Create /resources/js/components/toast.js with toastr compatibility
- [ ] 4.5 Register @alpinejs/collapse plugin in app.js
- [ ] 4.6 Import and register all components in app.js
- [ ] 4.7 Create modal HTML template in layout
- [ ] 4.8 Create toast container HTML in layout

### Phase 4 Verification Checklist
- [ ] Modals open/close with transitions
- [ ] Dropdowns toggle correctly
- [ ] Tooltips appear on hover
- [ ] Toasts show and auto-dismiss
- [ ] Existing JS code using toastr/bootbox still works

---

## Phase 5: Third-Party Library Styling

- [ ] 5.1 Create /resources/css/vendors/datatables.css with Tailwind styling
- [ ] 5.2 Create /resources/css/vendors/flatpickr.css with Tailwind styling
- [ ] 5.3 Create /resources/css/vendors/fullcalendar.css with Tailwind styling
- [ ] 5.4 Create /resources/css/vendors/summernote.css with Tailwind styling
- [ ] 5.5 Import all vendor CSS files in /resources/css/app.css
- [ ] 5.6 Create Flatpickr initialization helper in JS

### Phase 5 Verification Checklist
- [ ] DataTables renders with correct styling
- [ ] Date picker matches design system
- [ ] Dark mode works for all third-party components

---

## Phase 6: Page Migration - Priority 1 (Core Pages)

- [ ] 6.1 Migrate stockoverview.blade.php
- [ ] 6.2 Migrate purchase.blade.php
- [ ] 6.3 Migrate consume.blade.php
- [ ] 6.4 Migrate shoppinglist.blade.php
- [ ] 6.5 Migrate transfer.blade.php
- [ ] 6.6 Migrate inventory.blade.php

---

## Phase 6: Page Migration - Priority 2 (Components)

- [ ] 6.7 Migrate components/productpicker.blade.php
- [ ] 6.8 Migrate components/datetimepicker.blade.php (convert to Flatpickr)
- [ ] 6.9 Migrate components/datetimepicker2.blade.php
- [ ] 6.10 Migrate components/numberpicker.blade.php
- [ ] 6.11 Migrate components/productamountpicker.blade.php
- [ ] 6.12 Migrate components/productcard.blade.php
- [ ] 6.13 Migrate components/locationpicker.blade.php
- [ ] 6.14 Migrate components/userpicker.blade.php
- [ ] 6.15 Migrate components/recipepicker.blade.php
- [ ] 6.16 Migrate components/shoppinglocationpicker.blade.php
- [ ] 6.17 Migrate components/userfieldsform.blade.php
- [ ] 6.18 Migrate components/userfields_thead.blade.php
- [ ] 6.19 Migrate components/userfields_tbody.blade.php
- [ ] 6.20 Migrate components/calendarcard.blade.php
- [ ] 6.21 Migrate components/chorecard.blade.php
- [ ] 6.22 Migrate components/batterycard.blade.php
- [ ] 6.23 Migrate components/camerabarcodescanner.blade.php
- [ ] 6.24 Migrate components/userpermission_select.blade.php

---

## Phase 6: Page Migration - Priority 3 (Secondary Pages)

- [ ] 6.25 Migrate recipes.blade.php
- [ ] 6.26 Migrate recipeform.blade.php
- [ ] 6.27 Migrate recipesettings.blade.php
- [ ] 6.28 Migrate mealplan.blade.php
- [ ] 6.29 Migrate mealplansections.blade.php
- [ ] 6.30 Migrate products.blade.php
- [ ] 6.31 Migrate productform.blade.php
- [ ] 6.32 Migrate choresoverview.blade.php
- [ ] 6.33 Migrate choretracking.blade.php
- [ ] 6.34 Migrate tasks.blade.php
- [ ] 6.35 Migrate taskform.blade.php
- [ ] 6.36 Migrate batteriesoverview.blade.php
- [ ] 6.37 Migrate batterytracking.blade.php
- [ ] 6.38 Migrate equipment.blade.php
- [ ] 6.39 Migrate equipmentform.blade.php
- [ ] 6.40 Migrate calendar.blade.php

---

## Phase 6: Page Migration - Priority 4 (Master Data)

- [ ] 6.41 Migrate locations.blade.php
- [ ] 6.42 Migrate locationform.blade.php
- [ ] 6.43 Migrate quantityunits.blade.php
- [ ] 6.44 Migrate quantityunitform.blade.php
- [ ] 6.45 Migrate quantityunitconversionform.blade.php
- [ ] 6.46 Migrate productgroups.blade.php
- [ ] 6.47 Migrate productgroupform.blade.php
- [ ] 6.48 Migrate shoppinglocations.blade.php
- [ ] 6.49 Migrate shoppinglocationform.blade.php
- [ ] 6.50 Migrate chores.blade.php
- [ ] 6.51 Migrate choreform.blade.php
- [ ] 6.52 Migrate batteries.blade.php
- [ ] 6.53 Migrate batteryform.blade.php
- [ ] 6.54 Migrate taskcategories.blade.php
- [ ] 6.55 Migrate taskcategoryform.blade.php
- [ ] 6.56 Migrate userentities.blade.php
- [ ] 6.57 Migrate userentityform.blade.php
- [ ] 6.58 Migrate userfields.blade.php
- [ ] 6.59 Migrate userfieldform.blade.php
- [ ] 6.60 Migrate userobjects.blade.php
- [ ] 6.61 Migrate userobjectform.blade.php

---

## Phase 6: Page Migration - Priority 5 (Journals/Reports)

- [ ] 6.62 Migrate stockjournal.blade.php
- [ ] 6.63 Migrate stockentries.blade.php
- [ ] 6.64 Migrate choresjournal.blade.php
- [ ] 6.65 Migrate batteriesjournal.blade.php
- [ ] 6.66 Migrate locationcontentsheet.blade.php
- [ ] 6.67 Migrate quantityunitpluraltesting.blade.php
- [ ] 6.68 Migrate print/shoppinglist.blade.php
- [ ] 6.69 Migrate print/recipes.blade.php

---

## Phase 6: Page Migration - Priority 6 (Settings/Admin)

- [ ] 6.70 Migrate stocksettings.blade.php
- [ ] 6.71 Migrate recipessettings.blade.php
- [ ] 6.72 Migrate choressettings.blade.php
- [ ] 6.73 Migrate batteriessettings.blade.php
- [ ] 6.74 Migrate taskssettings.blade.php
- [ ] 6.75 Migrate usersettings.blade.php
- [ ] 6.76 Migrate users.blade.php
- [ ] 6.77 Migrate userform.blade.php
- [ ] 6.78 Migrate manageapikeys.blade.php
- [ ] 6.79 Migrate apikeyform.blade.php
- [ ] 6.80 Migrate about.blade.php

---

## Phase 6: Page Migration - Priority 7 (Auth/Errors)

- [ ] 6.81 Migrate login.blade.php
- [ ] 6.82 Migrate errors/base.blade.php
- [ ] 6.83 Migrate errors/403.blade.php
- [ ] 6.84 Migrate errors/404.blade.php
- [ ] 6.85 Migrate errors/500.blade.php

---

## Phase 7: Cleanup & Optimization

- [ ] 7.1 Remove Bootstrap CSS include from layout (after all pages migrated)
- [ ] 7.2 Remove Bootstrap JS dependencies
- [ ] 7.3 Remove bootbox dependency (if fully replaced)
- [ ] 7.4 Archive old CSS files (grocy.css, grocy_menu_layout.css, grocy_night_mode.css)
- [ ] 7.5 Verify PurgeCSS is working - check output CSS size
- [ ] 7.6 Set GROCY_USE_TAILWIND default to true
- [ ] 7.7 Remove conditional logic in layout (make Tailwind the only option)
- [ ] 7.8 Update package.json to remove unused Bootstrap packages
- [ ] 7.9 Final full application test

### Final Verification Checklist
- [ ] All pages render correctly
- [ ] All CRUD operations work
- [ ] All interactive elements work
- [ ] Dark mode works throughout
- [ ] Mobile responsive at all breakpoints
- [ ] CSS bundle size < 50KB
- [ ] No console errors
- [ ] No Bootstrap classes remaining in templates

---

## New Tasks Discovered
<!-- Add any new tasks that arise during implementation here -->


---

## Blockers
<!-- Add any blockers that prevent task completion here -->


---

## Notes
<!-- Add any important notes, decisions, or observations here -->

- **Session 2026-01-08**: Fixed postcss.config.js to use `@tailwindcss/postcss` instead of `tailwindcss` directly (required for Tailwind v4). Added `"type": "module"` to package.json.
- **Session 2026-01-08 (Phase 2)**: Completed all Design System tasks. Used plain CSS instead of `@apply` directives for component classes due to Tailwind v4 compatibility. CSS now uses `@import "tailwindcss"` instead of three separate `@tailwind` directives. All button, form input, card, and badge component classes implemented with dark mode support.
- **Session 2026-01-08 (Phase 3)**: Completed Layout & Navigation. Created Tailwind layout with Alpine.js-powered sidebar (collapsible on desktop, slide-out on mobile). Ported all navigation items with feature flags and permission classes. Header includes clock, view settings dropdown, user dropdown, and settings menu. Navigation styles moved to main CSS file. Build verified: CSS 30.35KB (gzip 6.33KB), JS 46.70KB (gzip 16.68KB).


---

## Progress Summary

| Phase | Total | Complete | Remaining |
|-------|-------|----------|-----------|
| 1. Build System | 12 | 12 | 0 |
| 2. Design System | 10 | 10 | 0 |
| 3. Layout & Navigation | 10 | 10 | 0 |
| 4. Alpine.js Components | 8 | 0 | 8 |
| 5. Third-Party Styling | 6 | 0 | 6 |
| 6. Page Migration | 85 | 0 | 85 |
| 7. Cleanup | 9 | 0 | 9 |
| **TOTAL** | **140** | **32** | **108** |

Last Updated: 2026-01-08
