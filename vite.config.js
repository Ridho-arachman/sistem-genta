import tailwindcss from "@tailwindcss/vite";
import laravel from "laravel-vite-plugin";
import { bunny } from "laravel-vite-plugin/fonts";
import { defineConfig, loadEnv } from "vite";

const env = loadEnv(process.env.NODE_ENV ?? "development", process.cwd(), "");

const hostName = (value, fallback) => {
    try {
        return new URL(value.includes("://") ? value : `http://${value}`).hostname || fallback;
    } catch {
        return fallback;
    }
};

// Host LAN diambil dari APP_URL (.env) supaya ganti IP cukup satu tempat.
const devHost = hostName(env.APP_URL ?? "", "localhost");

export default defineConfig({
    plugins: [
        laravel({
            input: ["resources/css/app.css", "resources/js/app.js"],
            refresh: true,
            fonts: [
                bunny("Instrument Sans", {
                    weights: [400, 500, 600],
                }),
            ],
        }),
        tailwindcss(),
    ],
    server: {
        host: "0.0.0.0",
        port: 5173,
        cors: true,
        origin: `http://${devHost}:5173`,
        watch: {
            usePolling: true,
        },
    },
});
