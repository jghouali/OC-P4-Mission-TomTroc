### TomTroc
first commit

## GitHub Actions

A CI run is triggered for pull requests to `dev` and `main`.

It checks PHP syntax using:

```bash
php -l
```
## pre-commit hook

A pre-commit hook can be enabled locally to check PHP syntax before each commit.

```bash
git config core.hooksPath .githooks
chmod +x .githooks/pre-commit
```

This hook is a local aid. The official check remains the GitHub Actions CI.
