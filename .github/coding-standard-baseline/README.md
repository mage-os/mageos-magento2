# Magento 2 Coding Standard Action

A Github Action that runs the Magento Coding Standard.

## Inputs

See the [action.yml](./action.yml)

## Usage

```yml
name: Coding Standard

on:
  push:
    branches:
    - main
  pull_request:
    branches:
    - main

jobs:
  coding-standard:
    runs-on: ubuntu-latest
    steps:
    - uses: graycoreio/github-actions-magento2/coding-standard@main
      with:
        version: 25 # Optional, will use the latest if omitted.
        path: app/code # Optional, will be used when event is not a pull request.
```
