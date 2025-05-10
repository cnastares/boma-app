import preset from '../../../../vendor/filament/filament/tailwind.config.preset'
const plugin = require('tailwindcss/plugin');
export default {
  presets: [preset],

  content: [
    './app/Filament/**/*.php',
    './resources/views/filament/**/*.blade.php',
    './vendor/filament/**/*.blade.php',
    './app-modules/**/src/Livewire/**/*.php',
    './app-modules/**/src/Filament/**/*.php',
    './app-modules/**/resources/views/**/*.php',
    './resources/views/**/*.blade.php',
  ],
  theme: {
    extend: {
      // Add custom styles here if needed
      boxShadow: {
        'custom': '3px 0 0 black, 0 3px 0 black'
      },
      colors: {
        'price-600': 'var(--primary)',
      },
      backgroundImage: (theme) => ({
        'price-gradient': `linear-gradient(to left, transparent 1em, ${theme('colors.primary-600')} 1em)`,
      }),
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
