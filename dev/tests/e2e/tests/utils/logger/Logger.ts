// @ts-check

export class Logger {
    private readonly context: string;
    private readonly isDebug: boolean;

    constructor(context: string) {
        this.context = context;
        this.isDebug = process.env.DEBUG === 'true' || process.env.NODE_ENV === 'development';
    }

    private formatMessage(level: string, args: unknown[]): string {
        const prefix = `[${this.context}] [${level.toUpperCase()}]`;
        const message = args.map(arg => {
            try {
                return typeof arg === 'string' ? arg : JSON.stringify(arg);
            } catch {
                return '[Unserializable]';
            }
        }).join(' ');
        return `${prefix} ${message}\n`;
    }

    private write(level: string, args: unknown[]): void {
        if (!this.isDebug && level === 'log') {
            return;
        }
        const msg = this.formatMessage(level, args);
        if (typeof process !== 'undefined' && process.stdout?.write) {
            if (level === 'error') {
                process.stderr.write(msg);
            } else {
                process.stdout.write(msg);
            }
        } else {
            // eslint-disable-next-line no-console
            switch (level) {
                case 'log':
                case 'info':
                    console.info(msg.trim());
                    break;
                case 'warn':
                    console.warn(msg.trim());
                    break;
                case 'error':
                    console.error(msg.trim());
                    break;
            }
        }
    }

    public log(...args: unknown[]): void {
        this.write('log', args);
    }

    public info(...args: unknown[]): void {
        this.write('info', args);
    }

    public warn(...args: unknown[]): void {
        this.write('warn', args);
    }

    public error(...args: unknown[]): void {
        this.write('error', args);
    }
}
