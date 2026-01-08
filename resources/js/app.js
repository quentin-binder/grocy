import Alpine from 'alpinejs';
import collapse from '@alpinejs/collapse';

// Register Alpine.js plugins
Alpine.plugin(collapse);

// Import CSS
import '../css/app.css';

// Component imports will be added in Phase 4
// import modal from './components/modal.js';
// import dropdown from './components/dropdown.js';
// import tooltip from './components/tooltip.js';
// import toast from './components/toast.js';

// Register components
// Alpine.data('modal', modal);
// Alpine.data('dropdown', dropdown);

// Start Alpine.js
Alpine.start();

// Make Alpine available globally for debugging
window.Alpine = Alpine;
