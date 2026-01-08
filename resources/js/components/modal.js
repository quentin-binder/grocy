/**
 * Modal component for Alpine.js
 * Provides GrocyModal compatibility layer for bootbox replacement
 */

import { createFocusTrap } from 'focus-trap';

// Modal Alpine component
export function modal() {
	return {
		isOpen: false,
		title: '',
		body: '',
		size: 'md', // sm, md, lg, xl
		buttons: [],
		onClose: null,
		focusTrap: null,

		init() {
			// Handle ESC key
			this.$watch('isOpen', (value) => {
				if (value) {
					document.addEventListener('keydown', this.handleEscape.bind(this));
					this.$nextTick(() => this.setupFocusTrap());
				} else {
					document.removeEventListener('keydown', this.handleEscape.bind(this));
					if (this.focusTrap) {
						this.focusTrap.deactivate();
						this.focusTrap = null;
					}
				}
			});
		},

		handleEscape(e) {
			if (e.key === 'Escape') {
				this.close();
			}
		},

		setupFocusTrap() {
			const modalEl = this.$refs.modalContent;
			if (modalEl) {
				this.focusTrap = createFocusTrap(modalEl, {
					allowOutsideClick: true,
					escapeDeactivates: false,
					fallbackFocus: modalEl
				});
				this.focusTrap.activate();
			}
		},

		open(options = {}) {
			this.title = options.title || '';
			this.body = options.body || '';
			this.size = options.size || 'md';
			this.buttons = options.buttons || [];
			this.onClose = options.onClose || null;
			this.isOpen = true;
		},

		close(result = null) {
			this.isOpen = false;
			if (this.onClose) {
				this.onClose(result);
			}
			// Reset state
			this.$nextTick(() => {
				this.title = '';
				this.body = '';
				this.buttons = [];
				this.onClose = null;
			});
		},

		clickOutside() {
			this.close();
		},

		getSizeClass() {
			const sizes = {
				sm: 'max-w-sm',
				md: 'max-w-lg',
				lg: 'max-w-2xl',
				xl: 'max-w-4xl'
			};
			return sizes[this.size] || sizes.md;
		},

		handleButtonClick(button) {
			if (button.callback) {
				const result = button.callback();
				if (result !== false) {
					this.close(button.key || true);
				}
			} else {
				this.close(button.key || true);
			}
		}
	};
}

// GrocyModal compatibility layer (replaces bootbox)
class GrocyModalManager {
	constructor() {
		this.container = null;
		this.modalComponent = null;
	}

	init() {
		this.container = document.getElementById('modal-container');
	}

	getModalElement() {
		return document.querySelector('[x-data="modal()"]');
	}

	/**
	 * Show an alert dialog
	 * @param {Object|string} options - Options object or message string
	 */
	alert(options) {
		if (typeof options === 'string') {
			options = { message: options };
		}

		return new Promise((resolve) => {
			this.show({
				title: options.title || '',
				body: options.message || '',
				size: options.size || 'md',
				buttons: [
					{
						label: options.closeButton?.label || 'OK',
						className: 'btn-primary',
						key: 'ok'
					}
				],
				onClose: () => {
					if (options.callback) options.callback();
					resolve();
				}
			});
		});
	}

	/**
	 * Show a confirm dialog
	 * @param {Object|string} options - Options object or message string
	 */
	confirm(options) {
		if (typeof options === 'string') {
			options = { message: options };
		}

		return new Promise((resolve) => {
			this.show({
				title: options.title || '',
				body: options.message || '',
				size: options.size || 'md',
				buttons: [
					{
						label: options.buttons?.cancel?.label || 'Cancel',
						className: 'btn-secondary',
						key: 'cancel',
						callback: () => {
							if (options.callback) options.callback(false);
							resolve(false);
						}
					},
					{
						label: options.buttons?.confirm?.label || 'OK',
						className: options.buttons?.confirm?.className?.includes('danger') ? 'btn-danger' : 'btn-primary',
						key: 'ok',
						callback: () => {
							if (options.callback) options.callback(true);
							resolve(true);
						}
					}
				],
				onClose: () => {
					// Close without button click = cancel
					resolve(false);
				}
			});
		});
	}

	/**
	 * Show a custom dialog
	 * @param {Object} options - Dialog options
	 */
	dialog(options) {
		return new Promise((resolve) => {
			const buttons = [];

			if (options.buttons) {
				for (const [key, buttonConfig] of Object.entries(options.buttons)) {
					buttons.push({
						label: buttonConfig.label || key,
						className: buttonConfig.className || 'btn-secondary',
						key: key,
						callback: buttonConfig.callback
					});
				}
			}

			this.show({
				title: options.title || '',
				body: options.message || '',
				size: options.size || 'md',
				buttons: buttons,
				onClose: (result) => {
					if (options.onEscape) options.onEscape();
					resolve(result);
				}
			});
		});
	}

	/**
	 * Show modal with options
	 */
	show(options) {
		// Dispatch custom event to trigger modal
		window.dispatchEvent(new CustomEvent('grocy-modal:show', { detail: options }));
	}

	/**
	 * Hide the modal
	 */
	hide() {
		window.dispatchEvent(new CustomEvent('grocy-modal:hide'));
	}
}

// Create singleton instance
const grocyModalManager = new GrocyModalManager();

// Initialize on DOM ready
if (document.readyState === 'loading') {
	document.addEventListener('DOMContentLoaded', () => grocyModalManager.init());
} else {
	grocyModalManager.init();
}

// Export for use in app.js
export { grocyModalManager };

// Make GrocyModal available globally (bootbox replacement)
window.GrocyModal = grocyModalManager;

// Bootbox compatibility shim
window.bootbox = {
	alert: (options) => grocyModalManager.alert(options),
	confirm: (options) => grocyModalManager.confirm(options),
	dialog: (options) => grocyModalManager.dialog(options),
	hideAll: () => grocyModalManager.hide()
};

export default modal;
