module.exports = {
  purge: [
    './assets/**/*.js',
    './templates/**/*.html.twig',
  ],
  darkMode: false, // or 'media' or 'class'
  theme: {
    extend: {
      colors: {
        'blue-facebook': '#1877f2',
        'blue-linkedin': '#0a66c2',
        'blue-twitter': '#1da1f2',
        'gray-github': '#181717',
        'green-spotify': '#1ed760',
        'red-instagram': '#e4405f',
        'red-pixine': '#ff625d',
        'yellow-imdb': '#f5c518',
      },
      zIndex: {
        '1': '1'
      }
    },
  },
  variants: {
    extend: {},
  },
  plugins: [],
}
