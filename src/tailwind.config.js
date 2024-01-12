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
                "scale-in-hor-left": {
                    "0%": {
                        transform: "scaleX(0)",
                        "transform-origin": "0% 0%",
                        opacity: "1"
                    },
                    to: {
                        transform: "scaleX(1)",
                        "transform-origin": "0% 0%",
                        opacity: "1"
                    }
                },
                "scale-in-top": {
                    "0%": {
                        transform: "scale(0)",
                        "transform-origin": "50% 0%",
                        opacity: "1"
                    },
                    to: {
                        transform: "scale(1)",
                        "transform-origin": "50% 0%",
                        opacity: "1"
                    }
                },
                "scale-in-center": {
                    "0%": {
                        transform: "scale(0)",
                        opacity: "1"
                    },
                    to: {
                        transform: "scale(1)",
                        opacity: "1"
                    }
                },
                "flip-in-diag-1-bl": {
                    "0%": {
                        transform: "rotate3d(1, 1, 0, 80deg)",
                        opacity: "0"
                    },
                    to: {
                        transform: "rotate3d(1, 1, 0, 0deg)",
                        opacity: "1"
                    }
                },
                "flip-out-diag-1-tr": {
                    "0%": {
                        transform: "rotate3d(1, 1, 0, 0deg)",
                        opacity: "1"
                    },
                    to: {
                        transform: "rotate3d(1, 1, 0, 70deg)",
                        opacity: "0"
                    }
                },
                "fade-in": {
                    "0%": {
                        opacity: "0"
                    },
                    to: {
                        opacity: "1"
                    }
                },
            },
            animation: {
                flash: "flashFade 3.0s forwards",
                "bounce-out-left": "bounce-out-left 1.25s ease 3s  both",
                "shake-horizontal": "shake-horizontal 0.8s cubic-bezier(0.455, 0.030, 0.515, 0.955)   both",
                "scale-in-hor-left": "scale-in-hor-left 0.3s cubic-bezier(0.250, 0.460, 0.450, 0.940)   both",
                "scale-in-top": "scale-in-top 0.5s cubic-bezier(0.250, 0.460, 0.450, 0.940)   both",
                "scale-in-center": "scale-in-center 0.5s cubic-bezier(0.250, 0.460, 0.450, 0.940)   both",
                "flip-in-diag-1-bl": "flip-in-diag-1-bl 0.5s cubic-bezier(0.250, 0.460, 0.450, 0.940)   both",
                "flip-out-diag-1-tr": "flip-out-diag-1-tr 0.45s cubic-bezier(0.550, 0.085, 0.680, 0.530)   both",
                "fade-in": "fade-in 1.2s cubic-bezier(0.390, 0.575, 0.565, 1.000)   both",
            },
        },
    },

    plugins: [
        require('@tailwindcss/forms'),
        require('preline/plugin'),
    ],
};
