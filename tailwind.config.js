const colors = require('./resources/js/_colors.js');

module.exports = {
    content: [
        './resources/views/**/*.blade.php',
        './resources/js/components/**/*.vue',
        './storage/framework/views/*.php',
        "./resources/**/*.js",
    ],
    theme: {
        extend: {
            colors: colors,
            fontFamily: {
                inter: ['"Inter"', 'Helvetica']
            },
        },
    },
    plugins: [
        require('@tailwindcss/forms'),
        require('@tailwindcss/typography'),
        require('@tailwindcss/line-clamp')
    ],
}
