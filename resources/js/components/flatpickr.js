/**
 * Flatpickr Initialization Helper
 * Provides consistent date/time picker initialization for Grocy
 * Replaces Tempus Dominus Bootstrap datepicker
 */

import flatpickr from 'flatpickr';

// Default options that match Grocy's design system
const defaultOptions = {
	allowInput: true,
	dateFormat: 'Y-m-d',
	disableMobile: false,
	static: false,
	animate: true
};

// Time picker defaults
const defaultTimeOptions = {
	...defaultOptions,
	enableTime: true,
	noCalendar: true,
	dateFormat: 'H:i',
	time_24hr: true
};

// DateTime picker defaults
const defaultDateTimeOptions = {
	...defaultOptions,
	enableTime: true,
	dateFormat: 'Y-m-d H:i',
	time_24hr: true
};

/**
 * Check if dark mode is active
 */
function isDarkMode() {
	return document.documentElement.classList.contains('dark');
}

/**
 * Get locale from Grocy settings if available
 */
function getLocale() {
	if (typeof Grocy !== 'undefined' && Grocy.UserSettings && Grocy.UserSettings.locale) {
		const locale = Grocy.UserSettings.locale.substring(0, 2);
		// Map common locales to flatpickr locale names
		const localeMap = {
			'en': null, // default
			'de': 'de',
			'fr': 'fr',
			'es': 'es',
			'it': 'it',
			'nl': 'nl',
			'pl': 'pl',
			'pt': 'pt',
			'ru': 'ru',
			'sv': 'sv',
			'da': 'da',
			'no': 'no',
			'fi': 'fi',
			'cs': 'cs',
			'hu': 'hu',
			'zh': 'zh',
			'ja': 'ja',
			'ko': 'ko'
		};
		return localeMap[locale] || null;
	}
	return null;
}

/**
 * Get date format from Grocy settings
 */
function getDateFormat() {
	if (typeof Grocy !== 'undefined' && Grocy.UserSettings) {
		// Map PHP date formats to flatpickr formats
		const phpFormat = Grocy.UserSettings.date_format || 'Y-m-d';
		const formatMap = {
			'Y-m-d': 'Y-m-d',
			'd.m.Y': 'd.m.Y',
			'd/m/Y': 'd/m/Y',
			'm/d/Y': 'm/d/Y',
			'Y/m/d': 'Y/m/d'
		};
		return formatMap[phpFormat] || 'Y-m-d';
	}
	return 'Y-m-d';
}

/**
 * Initialize a date picker
 * @param {string|Element} selector - CSS selector or DOM element
 * @param {Object} options - Flatpickr options to merge with defaults
 * @returns {flatpickr.Instance|flatpickr.Instance[]}
 */
export function initDatePicker(selector, options = {}) {
	const dateFormat = getDateFormat();
	const locale = getLocale();

	const mergedOptions = {
		...defaultOptions,
		dateFormat: dateFormat,
		...options
	};

	if (locale) {
		mergedOptions.locale = locale;
	}

	return flatpickr(selector, mergedOptions);
}

/**
 * Initialize a time picker
 * @param {string|Element} selector - CSS selector or DOM element
 * @param {Object} options - Flatpickr options to merge with defaults
 * @returns {flatpickr.Instance|flatpickr.Instance[]}
 */
export function initTimePicker(selector, options = {}) {
	const mergedOptions = {
		...defaultTimeOptions,
		...options
	};

	return flatpickr(selector, mergedOptions);
}

/**
 * Initialize a datetime picker
 * @param {string|Element} selector - CSS selector or DOM element
 * @param {Object} options - Flatpickr options to merge with defaults
 * @returns {flatpickr.Instance|flatpickr.Instance[]}
 */
export function initDateTimePicker(selector, options = {}) {
	const dateFormat = getDateFormat();
	const locale = getLocale();

	const mergedOptions = {
		...defaultDateTimeOptions,
		dateFormat: dateFormat + ' H:i',
		...options
	};

	if (locale) {
		mergedOptions.locale = locale;
	}

	return flatpickr(selector, mergedOptions);
}

/**
 * Initialize a date range picker
 * @param {string|Element} selector - CSS selector or DOM element
 * @param {Object} options - Flatpickr options to merge with defaults
 * @returns {flatpickr.Instance|flatpickr.Instance[]}
 */
export function initDateRangePicker(selector, options = {}) {
	const dateFormat = getDateFormat();
	const locale = getLocale();

	const mergedOptions = {
		...defaultOptions,
		mode: 'range',
		dateFormat: dateFormat,
		...options
	};

	if (locale) {
		mergedOptions.locale = locale;
	}

	return flatpickr(selector, mergedOptions);
}

/**
 * Initialize all date pickers in a container
 * Auto-detects picker type from data attributes:
 * - data-datepicker: date picker
 * - data-timepicker: time picker
 * - data-datetimepicker: datetime picker
 * - data-daterangepicker: date range picker
 *
 * @param {string|Element} container - Container selector or element (default: document)
 */
export function initAllPickers(container = document) {
	const root = typeof container === 'string'
		? document.querySelector(container)
		: container;

	if (!root) return;

	// Date pickers
	root.querySelectorAll('[data-datepicker]').forEach(el => {
		if (!el._flatpickr) {
			initDatePicker(el, parseDataOptions(el));
		}
	});

	// Time pickers
	root.querySelectorAll('[data-timepicker]').forEach(el => {
		if (!el._flatpickr) {
			initTimePicker(el, parseDataOptions(el));
		}
	});

	// DateTime pickers
	root.querySelectorAll('[data-datetimepicker]').forEach(el => {
		if (!el._flatpickr) {
			initDateTimePicker(el, parseDataOptions(el));
		}
	});

	// Date range pickers
	root.querySelectorAll('[data-daterangepicker]').forEach(el => {
		if (!el._flatpickr) {
			initDateRangePicker(el, parseDataOptions(el));
		}
	});
}

/**
 * Parse data attributes for flatpickr options
 * @param {Element} element - DOM element with data attributes
 * @returns {Object} Flatpickr options
 */
function parseDataOptions(element) {
	const options = {};

	// Common options from data attributes
	if (element.dataset.minDate) {
		options.minDate = element.dataset.minDate;
	}
	if (element.dataset.maxDate) {
		options.maxDate = element.dataset.maxDate;
	}
	if (element.dataset.defaultDate) {
		options.defaultDate = element.dataset.defaultDate;
	}
	if (element.dataset.enableTime !== undefined) {
		options.enableTime = element.dataset.enableTime !== 'false';
	}
	if (element.dataset.noCalendar !== undefined) {
		options.noCalendar = element.dataset.noCalendar !== 'false';
	}
	if (element.dataset.inline !== undefined) {
		options.inline = element.dataset.inline !== 'false';
	}
	if (element.dataset.allowInput !== undefined) {
		options.allowInput = element.dataset.allowInput !== 'false';
	}

	// Callback for when date changes (useful for forms)
	if (element.dataset.onchange) {
		const fnName = element.dataset.onchange;
		if (typeof window[fnName] === 'function') {
			options.onChange = window[fnName];
		}
	}

	return options;
}

/**
 * Destroy flatpickr instance on an element
 * @param {string|Element} selector - CSS selector or DOM element
 */
export function destroyPicker(selector) {
	const elements = typeof selector === 'string'
		? document.querySelectorAll(selector)
		: [selector];

	elements.forEach(el => {
		if (el._flatpickr) {
			el._flatpickr.destroy();
		}
	});
}

/**
 * Compatibility layer for existing Grocy code that uses Tempus Dominus
 * Maps common Tempus Dominus calls to Flatpickr equivalents
 */
export function setupTempusDominusCompat() {
	// jQuery plugin compatibility (if jQuery is present)
	if (typeof window.jQuery !== 'undefined') {
		window.jQuery.fn.datetimepicker = function(options) {
			const opts = options || {};

			// Map Tempus Dominus options to Flatpickr
			const flatpickrOptions = {};

			if (opts.format) {
				// Convert moment.js format to flatpickr format
				flatpickrOptions.dateFormat = convertMomentFormat(opts.format);
			}
			if (opts.minDate) {
				flatpickrOptions.minDate = opts.minDate;
			}
			if (opts.maxDate) {
				flatpickrOptions.maxDate = opts.maxDate;
			}
			if (opts.defaultDate) {
				flatpickrOptions.defaultDate = opts.defaultDate;
			}
			if (opts.stepping) {
				flatpickrOptions.minuteIncrement = opts.stepping;
			}

			// Determine picker type
			const hasTime = opts.format ? opts.format.includes('H') || opts.format.includes('h') : false;
			const hasDate = opts.format ? opts.format.includes('Y') || opts.format.includes('D') || opts.format.includes('M') : true;

			if (hasTime && !hasDate) {
				return this.each(function() {
					initTimePicker(this, flatpickrOptions);
				});
			} else if (hasTime && hasDate) {
				return this.each(function() {
					initDateTimePicker(this, flatpickrOptions);
				});
			} else {
				return this.each(function() {
					initDatePicker(this, flatpickrOptions);
				});
			}
		};
	}
}

/**
 * Convert moment.js format to flatpickr format
 * @param {string} momentFormat - Moment.js date format
 * @returns {string} Flatpickr date format
 */
function convertMomentFormat(momentFormat) {
	const conversions = {
		'YYYY': 'Y',
		'YY': 'y',
		'MM': 'm',
		'M': 'n',
		'DD': 'd',
		'D': 'j',
		'HH': 'H',
		'H': 'G',
		'hh': 'h',
		'h': 'g',
		'mm': 'i',
		'ss': 'S',
		'A': 'K',
		'a': 'K'
	};

	let result = momentFormat;
	Object.entries(conversions).forEach(([moment, fp]) => {
		result = result.replace(moment, fp);
	});

	return result;
}

// Export flatpickr itself for advanced usage
export { flatpickr };

// Auto-initialize on DOM ready
document.addEventListener('DOMContentLoaded', () => {
	initAllPickers();
});

// Re-initialize when Alpine components load (for dynamically added content)
document.addEventListener('alpine:initialized', () => {
	initAllPickers();
});

// Make functions available globally for non-module usage
window.GrocyFlatpickr = {
	initDatePicker,
	initTimePicker,
	initDateTimePicker,
	initDateRangePicker,
	initAllPickers,
	destroyPicker,
	setupTempusDominusCompat
};
