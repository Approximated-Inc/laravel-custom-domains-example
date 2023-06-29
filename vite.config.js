import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import { execSync } from 'node:child_process';

let extendedViteDevServerOptions;

try {
	const gitpodPortUrl = execSync(`gp url ${5173}`).toString().trim();

    if(gitpodPortUrl){
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
    }else{
        extendedViteDevServerOptions = {};
    }
	
} catch {
	extendedViteDevServerOptions = {};
}

console.log(extendedViteDevServerOptions)

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
