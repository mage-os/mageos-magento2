// @ts-check

import fs from 'fs';
import path from 'path';

function deepMerge(target: any, source: any): any {
  for (const key in source) {
	if (source[key] instanceof Object && key in target) {
	  Object.assign(source[key], deepMerge(target[key], source[key]));
	}
  }
  // Combine the two objects
  return { ...target, ...source };
}

function loadAndMergeConfig(fileName: string) {
  const fallbackPath = path.resolve(__dirname, fileName);
  const currentPath = path.resolve(__dirname, '../../tests/config/', fileName);

  let currentConfig = {};
  let fallbackConfig = {};

  if (fs.existsSync(currentPath)) {
	currentConfig = JSON.parse(fs.readFileSync(currentPath, 'utf-8'));
  }

  if (fs.existsSync(fallbackPath)) {
	fallbackConfig = JSON.parse(fs.readFileSync(fallbackPath, 'utf-8'));
  }

  // Use deepMerge instead of shallow merge
  return deepMerge(fallbackConfig, currentConfig);
}

export const UIReference = loadAndMergeConfig('element-identifiers.json');
export const outcomeMarker = loadAndMergeConfig('outcome-markers.json');
export const inputValues = loadAndMergeConfig('input-values.json');
export const slugs = loadAndMergeConfig('slugs.json');
export const toggles = loadAndMergeConfig('test-toggles.json');