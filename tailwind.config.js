module.exports = {
  darkMode: 'class',
  content: [
    './**/*.html',
    './**/*.php',
    './scripts/**/*.js'
  ],
  theme: {
    extend: {
      colors: {
        primary: {
          light: '#4f46e5',
          dark: '#6366f1'
        },
        secondary: {
          light: '#10b981',
          dark: '#34d399'
        },
        danger: {
          light: '#ef4444',
          dark: '#f87171'
        },
        warning: {
          light: '#f59e0b',
          dark: '#fbbf24'
        },
        background: {
          light: '#f9fafb',
          dark: '#111827'
        },
        card: {
          light: '#ffffff',
          dark: '#1f2937'
        }
      },
      fontFamily: {
        fa: ['Font Awesome 6 Free'],
        sans: ['Sour', 'sans-serif']
      },
      animation: {
        'fade-in': 'fadeIn 0.3s ease-in',
        'slide-up': 'slideUp 0.3s ease-out',
        'slideDown': 'slideDown 0.3s ease-out'
      },
      keyframes: {
        fadeIn: {
          '0%': { opacity: '0' },
          '100%': { opacity: '1' }
        },
        slideUp: {
          '0%': { transform: 'translateY(20px)', opacity: '0' },
          '100%': { transform: 'translateY(0)', opacity: '1' }
        },
        slideDown: {
          '0%': { transform: 'translateY(-100%)', opacity: '0' },
          '100%': { transform: 'translateY(0)', opacity: '1' }
        },
        slideLeft: {
          '0%': { transform: 'translateY(-100%)', opacity: '0' },
          '100%': { transform: 'translateY(0)', opacity: '1' }
        }
      }
    }
  },
  plugins: [
  ]
}