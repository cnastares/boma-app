/** @type {import('tailwindcss').Config} */
import defaultTheme from 'tailwindcss/defaultTheme';
import preset from './vendor/filament/support/tailwind.config.preset'
const plugin = require('tailwindcss/plugin')


export default {
  presets: [preset],
  content: [
    "./resources/**/*.blade.php",
    "./resources/**/*.js",
    './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
    './storage/framework/views/*.php',
    './resources/views/**/*.blade.php',
    './app/Filament/**/*.php',
    './resources/views/filament/**/*.blade.php',
    './vendor/filament/**/*.blade.php',
    './app-modules/**/src/Livewire/**/*.php',
    './app-modules/**/src/Filament/**/*.php',
    './app-modules/**/resources/views/**/*.php',
    '/vendor/awcodes/filament-tiptap-editor/resources/**/*.blade.php',
  ],
  theme: {
        screens: {
            'sm': '768px',
            'md': '1024px',
            'lg': '1280px',
            'xl': '1536px',
        },
        extend: {
            fontFamily: {
                sans: ["var(--font)", ...defaultTheme.fontFamily.sans],
            },
            colors: {
                'primary-600': 'var(--primary)',
                'gray-100': '#F4F4F0',
                'muted': 'rgba(0, 0, 0, 0.5)',
                'primary-400': 'rgba(254, 189, 105, 0.8)',
                'gray-300': '#D9D9D9',
                'secondary':{
                    '200': 'var(--secondary-200)',
                    '600': 'var(--secondary-600)',
                }
            },
            backgroundImage: (theme) => ({
                'price-gradient': `linear-gradient(to left, transparent 1em, ${theme('colors.primary-600')} 1em)`,
            }),
            boxShadow: {
                'custom': '3px 0 0 black, 0 3px 0 black'
            },
            gridTemplateColumns: {
                'auto-fill-100': 'repeat(auto-fill, minmax(100px, 1fr))',
                'auto-fill-150': 'repeat(auto-fill, minmax(150px, 1fr))',
                'auto-fill-200': 'repeat(auto-fill, minmax(200px, 1fr))',
                'auto-fill-250': 'repeat(auto-fill, minmax(250px, 1fr))',
                'auto-fill-300': 'repeat(auto-fill, minmax(300px, 1fr))',
                'auto-fit-250': 'repeat(auto-fit, minmax(250px, 1fr))',
              },
        },
  },
  plugins: [
    plugin(function ({ addVariant, e }) {
        addVariant('classic', ({ modifySelectors, separator }) => {
          modifySelectors(({ className }) => {
            return `.classic .${e(`classic${separator}${className}`)}`
          })
        });
    }),
    require('@tailwindcss/typography'),
    require('@tailwindcss/forms'),
  ],
}

