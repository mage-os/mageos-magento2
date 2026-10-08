// @ts-check

import { Logger } from './Logger';

export function createLogger(context: string): Logger {
    return new Logger(context);
}
