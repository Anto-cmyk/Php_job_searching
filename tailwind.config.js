/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [
     "./*.php",
    "./**/*.php",
    "./src/**/*.{html,js}",
    "./public/**/*.{html,js}"

  ],
  theme: {
    extend: {

       colors: {
        primary: '#1D4ED8', // blue
        secondary: '#9333EA', // purple
        accent: '#F59E0B', // amber
      },
      fontFamily: {
        sans: ['Inter', 'sans-serif'],
        heading: ['Poppins', 'sans-serif'],
      },

    },
  },
  plugins: [],
}


