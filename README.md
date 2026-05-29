# X3P0 Skills

Shared AI skills for WordPress themes and plugins, distributed as a Composer
package and installed via a post-install script.

---

## Requirements

- PHP 8.1+
- Composer

---

## Installation

Add the package to your project's `require-dev` and configure the installer
in your `composer.json`:

```json
{
	"scripts": {
		"post-install-cmd": [
			"@@x3p0-skills"
		],
		"post-update-cmd": [
			"@x3p0-skills"
		],
		"x3p0-skills": "X3P0\\Skills\\Installer::install"
	},
	"require-dev": {
		"x3p0-dev/x3p0-skills": "dev-master"
	},
	"extra": {
		"x3p0": {
			"skills": {
				"path": ".claude/skills"
			}
		}
	}
}
```

Then run:

```bash
composer require --dev x3p0-dev/x3p0-skills
```

Skills are installed into `.claude/skills/` by default. The `path` key under
`extra.x3p0.skills` is optional — omit it to use the default.

---

## Skills

### General

| Skill | Description |
|---|---|
| `x3p0-code-style-js` | JavaScript coding standards and style conventions |
| `x3p0-code-style-php` | PHP coding standards and style conventions |

### Theme

| Skill | Description |
|---|---|
| `x3p0-theme-patterns` | WordPress block pattern conventions for themes |
| `x3p0-theme-block-style-variations` | Block style variation conventions for themes |

---

## Gitignore

Add the installed skill folders to your project's `.gitignore`:

```
.claude/skills/x3p0-*/
```

---

## License

GPL-3.0-or-later
