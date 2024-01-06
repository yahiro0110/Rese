const defaultTheme = require('tailwindcss/defaultTheme');

/** @type {import('tailwindcss').Config} */
module.exports = {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './resources/js/**/*.vue',
        'node_modules/preline/dist/*.js',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },
            keyframes: {
                flashFade: {
                    "0%": { transform: "translateX(180px)", opacity: 0 },
                    "20%": { transform: "translateX(0)", opacity: 1 },
                    "80%": { transform: "translateX(0)", opacity: 1 },
                    "100%": { transform: "translateX(180px)", opacity: 0 },
                },
                "bounce-out-left": {
                    "0%,15%,38%,70%": {
                        transform: "translateX(0)",
                        "animation-timing-function": "ease-out"
                    },
                    "5%": {
                        transform: "translateX(-30px)",
                        "animation-timing-function": "ease-in"
                    },
                    "25%": {
                        transform: "translateX(-38px)",
                        "animation-timing-function": "ease-in"
                    },
                    "52%": {
                        transform: "translateX(-80px)",
                        "animation-timing-function": "ease-in"
                    },
                    "85%": {
                        opacity: "1"
                    },
                    to: {
                        transform: "translateX(-1000px)",
                        opacity: "0"
                    }
                },
                "shake-horizontal": {
                    "0%,to": {
                        transform: "translateX(0)"
                    },
                    "10%,30%,50%,70%": {
                        transform: "translateX(-10px)"
                    },
                    "20%,40%,60%": {
                        transform: "translateX(10px)"
                    },
                    "80%": {
                        transform: "translateX(8px)"
                    },
                    "90%": {
                        transform: "translateX(-8px)"
                    }
                },
            },
            animation: {
                flash: "flashFade 3.0s forwards",
                "bounce-out-left": "bounce-out-left 1.25s ease 3s  both",
                "shake-horizontal": "shake-horizontal 0.8s cubic-bezier(0.455, 0.030, 0.515, 0.955)   both",
            },
        },
    },

    plugins: [
        require('@tailwindcss/forms'),
        require('preline/plugin'),
    ],
};
