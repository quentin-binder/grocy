/** @type {import('tailwindcss').Config} */
export default {
  content: [
    './views/**/*.blade.php',
    './resources/**/*.js',
  ],

  darkMode: 'class',

  theme: {
    extend: {
      colors: {
        // Primary color palette (soft blue)
        primary: {
          50: '#f0f7ff',
          100: '#e0effe',
          200: '#b9e0fe',
          300: '#7cc8fd',
          400: '#36aaf8',
          500: '#0c8ce9',
          600: '#006fc7',
          700: '#0159a1',
          800: '#064b85',
          900: '#0b3f6e',
        },
        // Gray palette (zinc-based)
        gray: {
          50: '#fafafa',
          100: '#f4f4f5',
          200: '#e4e4e7',
          300: '#d4d4d8',
          400: '#a1a1aa',
          500: '#71717a',
          600: '#52525b',
          700: '#3f3f46',
          800: '#27272a',
          900: '#18181b',
          950: '#09090b',
        },
        // Semantic colors
        success: {
          light: '#ecfdf5',
          DEFAULT: '#10b981',
          dark: '#065f46',
        },
        warning: {
          light: '#fffbeb',
          DEFAULT: '#f59e0b',
          dark: '#92400e',
        },
        danger: {
          light: '#fef2f2',
          DEFAULT: '#ef4444',
          dark: '#991b1b',
        },
        info: {
          light: '#eff6ff',
          DEFAULT: '#3b82f6',
          dark: '#1e40af',
        },
      },

      fontFamily: {
        sans: ['Inter', 'system-ui', 'sans-serif'],
      },

      fontSize: {
        base: ['0.875rem', { lineHeight: '1.5rem' }],
      },

      borderRadius: {
        DEFAULT: '0.375rem',
      },

      boxShadow: {
        card: '0 1px 3px 0 rgb(0 0 0 / 0.05)',
        dropdown: '0 4px 6px -1px rgb(0 0 0 / 0.07)',
      },
    },
  },

  plugins: [
    require('@tailwindcss/forms'),
    require('@tailwindcss/typography'),
  ],
};
