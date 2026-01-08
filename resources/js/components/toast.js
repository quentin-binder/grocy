/**
 * Toast notifications component
 * Alpine.js store-based implementation with toastr compatibility
 */

// Default options
const defaultOptions = {
	duration: 5000,
	position: 'bottom-right',
	closable: true
};

// Toast type configurations
const toastTypes = {
	success: {
		icon: 'fa-check-circle',
		bgClass: 'bg-success dark:bg-success',
		textClass: 'text-white',
		iconClass: 'text-white'
	},
	error: {
		icon: 'fa-exclamation-circle',
		bgClass: 'bg-danger dark:bg-danger',
		textClass: 'text-white',
		iconClass: 'text-white'
	},
	warning: {
		icon: 'fa-exclamation-triangle',
		bgClass: 'bg-warning dark:bg-warning',
		textClass: 'text-gray-900',
		iconClass: 'text-gray-900'
	},
	info: {
		icon: 'fa-info-circle',
		bgClass: 'bg-info dark:bg-info',
		textClass: 'text-white',
		iconClass: 'text-white'
	}
};

// Toast store for Alpine.js
export const toastStore = {
	toasts: [],
	nextId: 1,

	add(type, message, title = '', options = {}) {
		const id = this.nextId++;
		const config = toastTypes[type] || toastTypes.info;
		const mergedOptions = { ...defaultOptions, ...options };

		const toast = {
			id,
			type,
			message,
			title,
			...config,
			...mergedOptions,
			visible: false
		};

		this.toasts.push(toast);

		// Trigger entrance animation
		setTimeout(() => {
			const t = this.toasts.find((t) => t.id === id);
			if (t) t.visible = true;
		}, 10);

		// Auto-dismiss
		if (mergedOptions.duration > 0) {
			setTimeout(() => {
				this.remove(id);
			}, mergedOptions.duration);
		}

		return id;
	},

	remove(id) {
		const toast = this.toasts.find((t) => t.id === id);
		if (toast) {
			toast.visible = false;
			// Wait for exit animation
			setTimeout(() => {
				this.toasts = this.toasts.filter((t) => t.id !== id);
			}, 300);
		}
	},

	clear() {
		this.toasts.forEach((t) => (t.visible = false));
		setTimeout(() => {
			this.toasts = [];
		}, 300);
	},

	success(message, title = '', options = {}) {
		return this.add('success', message, title, options);
	},

	error(message, title = '', options = {}) {
		return this.add('error', message, title, options);
	},

	warning(message, title = '', options = {}) {
		return this.add('warning', message, title, options);
	},

	info(message, title = '', options = {}) {
		return this.add('info', message, title, options);
	}
};

// Toastr compatibility layer
class ToastrCompat {
	constructor() {
		this.options = {
			closeButton: true,
			progressBar: false,
			positionClass: 'toast-bottom-right',
			timeOut: 5000,
			extendedTimeOut: 1000
		};
	}

	success(message, title = '', options = {}) {
		return toastStore.success(message, title, this._mapOptions(options));
	}

	error(message, title = '', options = {}) {
		return toastStore.error(message, title, this._mapOptions(options));
	}

	warning(message, title = '', options = {}) {
		return toastStore.warning(message, title, this._mapOptions(options));
	}

	info(message, title = '', options = {}) {
		return toastStore.info(message, title, this._mapOptions(options));
	}

	clear() {
		toastStore.clear();
	}

	remove(toastId) {
		toastStore.remove(toastId);
	}

	_mapOptions(options) {
		return {
			duration: options.timeOut || this.options.timeOut,
			closable: options.closeButton !== undefined ? options.closeButton : this.options.closeButton
		};
	}
}

// Create compatibility instance
const toastrCompat = new ToastrCompat();

// Toast component HTML generator
export function createToastHTML(toast) {
	return `
		<div
			x-data="{ show: false }"
			x-init="$nextTick(() => show = true)"
			x-show="show"
			x-transition:enter="transform ease-out duration-300 transition"
			x-transition:enter-start="translate-y-2 opacity-0"
			x-transition:enter-end="translate-y-0 opacity-100"
			x-transition:leave="transition ease-in duration-200"
			x-transition:leave-start="opacity-100"
			x-transition:leave-end="opacity-0"
			class="pointer-events-auto w-full max-w-sm overflow-hidden rounded-lg shadow-lg ring-1 ring-black ring-opacity-5 ${toast.bgClass}"
		>
			<div class="p-4">
				<div class="flex items-start">
					<div class="flex-shrink-0">
						<i class="fas ${toast.icon} ${toast.iconClass}"></i>
					</div>
					<div class="ml-3 w-0 flex-1">
						${toast.title ? `<p class="text-sm font-medium ${toast.textClass}">${toast.title}</p>` : ''}
						<p class="mt-1 text-sm ${toast.textClass}">${toast.message}</p>
					</div>
					${
						toast.closable
							? `
						<div class="ml-4 flex flex-shrink-0">
							<button
								type="button"
								@click="show = false; setTimeout(() => $el.parentElement.parentElement.parentElement.remove(), 200)"
								class="inline-flex rounded-md ${toast.textClass} hover:opacity-75 focus:outline-none focus:ring-2 focus:ring-white focus:ring-offset-2"
							>
								<span class="sr-only">Close</span>
								<i class="fas fa-times"></i>
							</button>
						</div>
					`
							: ''
					}
				</div>
			</div>
		</div>
	`;
}

// Alpine store registration
export function registerToastStore(Alpine) {
	Alpine.store('toasts', toastStore);
}

// Initialize - make toastr available globally
// Only replace if real toastr isn't loaded
if (typeof window.toastr === 'undefined') {
	window.toastr = toastrCompat;
} else {
	// Real toastr is loaded, keep using it
	// But also expose our Alpine-based version
	window.grocyToast = toastrCompat;
}

export { toastrCompat };
export default toastStore;
