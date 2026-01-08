/**
 * Dropdown component for Alpine.js
 * Click-toggle dropdown with outside click detection
 */

export function dropdown() {
	return {
		open: false,

		init() {
			// Handle click outside
			this.$watch('open', (value) => {
				if (value) {
					// Delay to avoid immediate close from toggle click
					setTimeout(() => {
						document.addEventListener('click', this.handleClickOutside.bind(this));
					}, 0);
				} else {
					document.removeEventListener('click', this.handleClickOutside.bind(this));
				}
			});
		},

		handleClickOutside(event) {
			if (!this.$el.contains(event.target)) {
				this.open = false;
			}
		},

		toggle() {
			this.open = !this.open;
		},

		close() {
			this.open = false;
		},

		// Keyboard navigation
		handleKeydown(event) {
			switch (event.key) {
				case 'Escape':
					this.open = false;
					break;
				case 'ArrowDown':
					event.preventDefault();
					this.focusNext();
					break;
				case 'ArrowUp':
					event.preventDefault();
					this.focusPrevious();
					break;
			}
		},

		focusNext() {
			const items = this.$el.querySelectorAll('[role="menuitem"]');
			const currentIndex = Array.from(items).indexOf(document.activeElement);
			const nextIndex = currentIndex < items.length - 1 ? currentIndex + 1 : 0;
			items[nextIndex]?.focus();
		},

		focusPrevious() {
			const items = this.$el.querySelectorAll('[role="menuitem"]');
			const currentIndex = Array.from(items).indexOf(document.activeElement);
			const prevIndex = currentIndex > 0 ? currentIndex - 1 : items.length - 1;
			items[prevIndex]?.focus();
		}
	};
}

export default dropdown;
