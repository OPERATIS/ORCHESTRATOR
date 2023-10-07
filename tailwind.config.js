const colors = require('./resources/js/_colors.js');

module.exports = {
    content: [
        './resources/views/**/*.blade.php',
        './storage/framework/views/*.php',
        "./resources/**/*.js",
    ],
    theme: {
        extend: {
            colors: colors
        },
    },
    plugins: [
        require('@tailwindcss/forms'),
        require('@tailwindcss/typography'),
        require('@tailwindcss/line-clamp')
    ],
}
