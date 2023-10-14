const colors = {
    body: withOpacity('--color-body'),
    default: withOpacity('--color-default'),
    dark: withOpacity('--color-dark'),
    black: withOpacity('--color-black'),
    black_1: withOpacity('--color-black_1'),
    black_2: withOpacity('--color-black_2'),
    black_4: withOpacity('--color-black_4'),
    black_5: withOpacity('--color-black_5'),
    secondary_blue: withOpacity('--color-secondary_blue'),
    primary_light: withOpacity('--color-primary-light'),
    primary_purple: withOpacity('--color-primary-purple'),
    green_1: withOpacity('--color-green_1'),
    green_2: withOpacity('--color-green_2'),
    gray_1: withOpacity('--color-gray_1'),
    gray_4: withOpacity('--color-gray_4'),
    blue_1: withOpacity('--color-blue_1'),
    red: withOpacity('--color-red'),
    dangers: withOpacity('--color-danger'),
    primary: withOpacity('--color-primary'),
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
