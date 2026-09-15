/** @type {import('tailwindcss').Config} */
export default {
    content: [
        "./resources/**/*.blade.php",
        "./resources/**/*.js",
        "./resources/**/*.vue",
    ],
    theme: {
        extend: {
            colors: {
                // JMCL JEANS LTD brand palette — premium denim/fashion feel
                denim: {
                    50: "#eef4fb",
                    100: "#d9e6f4",
                    200: "#b3cce9",
                    300: "#84acd9",
                    400: "#5688c4",
                    500: "#3a6cab",
                    600: "#2c548b",
                    700: "#23436f",
                    800: "#1e3759",
                    900: "#1a2e4a",
                    950: "#101c30",
                },
                charcoal: {
                    50: "#f6f6f6",
                    100: "#e7e7e7",
                    200: "#d1d1d1",
                    300: "#b0b0b0",
                    400: "#888888",
                    500: "#6d6d6d",
                    600: "#5d5d5d",
                    700: "#4f4f4f",
                    800: "#454545",
                    900: "#1a1a1a",
                    950: "#0d0d0d",
                },
                gold: {
                    400: "#d4af37",
                    500: "#c19b2e",
                },
            },
            fontFamily: {
                display: ["'Playfair Display'", "serif"],
                sans: ["'Inter'", "ui-sans-serif", "system-ui"],
            },
            container: {
                center: true,
                padding: "1rem",
            },
        },
    },
    plugins: [
        require("@tailwindcss/forms"),
        require("@tailwindcss/aspect-ratio"),
        require("@tailwindcss/typography"),
    ],
};
