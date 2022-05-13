import {type Config} from "tailwindcss"
import tailwindContainerQueries from "@tailwindcss/container-queries"
import decanter from "decanter"

module.exports = {
  content: ["templates/**/*.html.twig", "stanford_profile_theme.theme"],
  presets: [decanter],
  theme: {
    extend: {
      aria: {
        current: "current='true'",
        "current-page": "current='page'",
      },
      data: {
        intrail: "intrail='true'",
      },
      containers: {
        "8xl": "80rem",
        "9xl": "90rem",
        "10xl": "100rem",
        "11xl": "110rem",
        "12xl": "120rem",
        "13xl": "130rem",
        "14xl": "140rem",
        "15xl": "150rem",
      },
      scale: {
        "-100": "-1",
      },
      listStyleType: {
        "lower-alpha": "lower-alpha",
        "upper-alpha": "upper-alpha",
        "lower-roman": "lower-roman",
        "upper-roman": "upper-roman",
      },
    },
  },
  plugins: [
    ...decanter.plugins,
    tailwindContainerQueries
  ],
} satisfies Config
