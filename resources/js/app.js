import Alpine from 'alpinejs';
import collapse from '@alpinejs/collapse';

// Import CSS
import '../css/app.css';

// Import components
import modal from './components/modal.js';
import dropdown from './components/dropdown.js';
import tooltip from './components/tooltip.js';
import toastStore, { registerToastStore } from './components/toast.js';

// Register Alpine.js plugins
Alpine.plugin(collapse);

// Register Alpine data components
Alpine.data('modal', modal);
Alpine.data('dropdown', dropdown);

// Register Alpine stores
registerToastStore(Alpine);

// Start Alpine.js
Alpine.start();

// Make Alpine available globally for debugging
window.Alpine = Alpine;

// Re-initialize tooltips after Alpine starts
document.addEventListener('alpine:init', () => {
	// Tooltip initialization is handled in tooltip.js
});
