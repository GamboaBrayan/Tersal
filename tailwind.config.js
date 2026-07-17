/** @type {import('tailwindcss').Config} */
export default {
    content: [
        "./resources/**/*.blade.php",
        "./resources/**/*.js",
        "./resources/**/*.vue",
    ],
    theme: {
        extend: {
            fontFamily: {
                sans: ['Poppins', 'sans-serif'],
            },
            colors: {
                primary: '#0A1BCC',    // Azure/Electric Blue
                action: '#D61A1A',     // Vibrant Red
                white: '#FFFFFF',      // Pure White
                background: '#F8F9FA'  // Soft Gray
            }
        },
    },
    plugins: [],
}
