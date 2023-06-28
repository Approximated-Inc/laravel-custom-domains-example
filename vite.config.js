import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import { execSync } from 'node:child_process';

let extendedViteDevServerOptions;

try {
	const gitpodPortUrl = execSync(`gp url ${5173}`).toString().trim();

	extendedViteDevServerOptions = {
		hmr: {
			protocol: 'wss',
			host: new URL(gitpodPortUrl).hostname,
			clientPort: 443
		},
        cors: {
            origin: "*",
            methods: "GET,HEAD,PUT,PATCH,POST,DELETE",
            preflightContinue: false,
            optionsSuccessStatus: 204
          }
	};
} catch {
	extendedViteDevServerOptions = {};
}

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/js/app.js',
            ],
            refresh: true,
        }),
    ],
    server: {
		...extendedViteDevServerOptions
	}
});
