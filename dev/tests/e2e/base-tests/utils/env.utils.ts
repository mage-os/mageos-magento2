// @ts-check

/**
 * Utility to retrieve required environment variables.
 * Throws an error when the variable is not set.
 */
export function requireEnv(varName: string): string {
  const value = process.env[varName];
  if (!value) {
    throw new Error(`${varName} is not defined in the .env file.`);
  }
  return value;
}

