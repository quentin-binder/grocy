/**
 * Tooltip initialization with Tippy.js
 * Auto-initializes tooltips on [data-tooltip] and [title] elements
 */

import tippy from 'tippy.js';
import 'tippy.js/dist/tippy.css';

// Default theme for light/dark mode
const defaultProps = {
	animation: 'fade',
	duration: [200, 150],
	delay: [200, 0],
	arrow: true,
	theme: 'grocy',
	allowHTML: true
};

/**
 * Initialize tooltips on elements matching selector
 */
export function initTooltips(container = document) {
	// Elements with data-tooltip attribute
	const dataTooltipElements = container.querySelectorAll('[data-tooltip]:not([data-tippy-initialized])');
	dataTooltipElements.forEach((el) => {
		const content = el.getAttribute('data-tooltip');
		const placement = el.getAttribute('data-tooltip-placement') || 'top';

		tippy(el, {
			...defaultProps,
			content: content,
			placement: placement
		});

		el.setAttribute('data-tippy-initialized', 'true');
	});

	// Elements with title attribute (Bootstrap tooltip compatibility)
	const titleElements = container.querySelectorAll('[title]:not([data-tippy-initialized]):not([data-tooltip])');
	titleElements.forEach((el) => {
		const content = el.getAttribute('title');
		if (!content) return;

		// Store and remove title to prevent native tooltip
		el.setAttribute('data-original-title', content);
		el.removeAttribute('title');

		const placement = el.getAttribute('data-placement') || el.getAttribute('data-bs-placement') || 'top';

		tippy(el, {
			...defaultProps,
			content: content,
			placement: placement
		});

		el.setAttribute('data-tippy-initialized', 'true');
	});
}

/**
 * Destroy tooltips on elements
 */
export function destroyTooltips(container = document) {
	const elements = container.querySelectorAll('[data-tippy-initialized]');
	elements.forEach((el) => {
		if (el._tippy) {
			el._tippy.destroy();
		}
		el.removeAttribute('data-tippy-initialized');
	});
}

/**
 * Show tooltip programmatically
 */
export function showTooltip(element, content, options = {}) {
	if (element._tippy) {
		element._tippy.setContent(content);
		element._tippy.show();
		return element._tippy;
	}

	const instance = tippy(element, {
		...defaultProps,
		content: content,
		trigger: 'manual',
		...options
	});
	instance.show();
	return instance;
}

/**
 * Hide tooltip programmatically
 */
export function hideTooltip(element) {
	if (element._tippy) {
		element._tippy.hide();
	}
}

// Add custom theme CSS
const style = document.createElement('style');
style.textContent = `
	.tippy-box[data-theme~='grocy'] {
		background-color: #18181b;
		color: #fafafa;
		font-size: 0.8125rem;
		border-radius: 0.375rem;
		box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1), 0 2px 4px -2px rgb(0 0 0 / 0.1);
	}

	.tippy-box[data-theme~='grocy'][data-placement^='top'] > .tippy-arrow::before {
		border-top-color: #18181b;
	}

	.tippy-box[data-theme~='grocy'][data-placement^='bottom'] > .tippy-arrow::before {
		border-bottom-color: #18181b;
	}

	.tippy-box[data-theme~='grocy'][data-placement^='left'] > .tippy-arrow::before {
		border-left-color: #18181b;
	}

	.tippy-box[data-theme~='grocy'][data-placement^='right'] > .tippy-arrow::before {
		border-right-color: #18181b;
	}

	/* Dark mode - invert colors */
	.dark .tippy-box[data-theme~='grocy'] {
		background-color: #f4f4f5;
		color: #18181b;
	}

	.dark .tippy-box[data-theme~='grocy'][data-placement^='top'] > .tippy-arrow::before {
		border-top-color: #f4f4f5;
	}

	.dark .tippy-box[data-theme~='grocy'][data-placement^='bottom'] > .tippy-arrow::before {
		border-bottom-color: #f4f4f5;
	}

	.dark .tippy-box[data-theme~='grocy'][data-placement^='left'] > .tippy-arrow::before {
		border-left-color: #f4f4f5;
	}

	.dark .tippy-box[data-theme~='grocy'][data-placement^='right'] > .tippy-arrow::before {
		border-right-color: #f4f4f5;
	}
`;
document.head.appendChild(style);

// Auto-initialize on DOM ready
if (document.readyState === 'loading') {
	document.addEventListener('DOMContentLoaded', () => initTooltips());
} else {
	initTooltips();
}

// Re-initialize after Alpine components update
document.addEventListener('alpine:initialized', () => {
	// Re-check for new elements after Alpine renders
	setTimeout(() => initTooltips(), 100);
});

// Observe DOM for dynamically added elements
const observer = new MutationObserver((mutations) => {
	mutations.forEach((mutation) => {
		mutation.addedNodes.forEach((node) => {
			if (node.nodeType === 1) { // Element node
				initTooltips(node);
				// Also check children
				if (node.querySelectorAll) {
					const children = node.querySelectorAll('[data-tooltip], [title]');
					if (children.length > 0) {
						initTooltips(node);
					}
				}
			}
		});
	});
});

// Start observing when DOM is ready
if (document.readyState === 'loading') {
	document.addEventListener('DOMContentLoaded', () => {
		observer.observe(document.body, { childList: true, subtree: true });
	});
} else {
	observer.observe(document.body, { childList: true, subtree: true });
}

export default {
	initTooltips,
	destroyTooltips,
	showTooltip,
	hideTooltip
};
