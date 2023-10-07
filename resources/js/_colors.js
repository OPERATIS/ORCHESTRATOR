const colors = {
    body: withOpacity('--color-body'),
    default: withOpacity('--color-default'),
    dark: withOpacity('--color-dark'),
    black: withOpacity('--color-black'),
    black_4: withOpacity('--color-black_4'),
    secondary_blue: withOpacity('--color-secondary_blue'),
    green_2: withOpacity('--color-green_2'),
    gray_1: withOpacity('--color-gray_1'),
    gray_4: withOpacity('--color-gray_4'),
    blue_1: withOpacity('--color-blue_1'),
    danger: withOpacity('--color-danger'),
}

// function generateColor(color, extended = false) {
//     return {
//         DEFAULT: withOpacity(`--color-${color}-default`),
//         dark: withOpacity(`--color-${color}-dark`),
//         light: withOpacity(`--color-${color}-light`)
//     }
// }

function withOpacity(variableName) {
    return ({opacityValue}) => {
        if (opacityValue !== undefined) {
            return `rgba(var(${variableName}), ${opacityValue})`
        }
        return `rgb(var(${variableName}))`
    }
}

module.exports = colors;
