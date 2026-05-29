# X3P0 Skills

Shared AI skills for WordPress themes and plugins, distributed as a Composer
package. Skills are installed into consuming projects via `Installer.php`.

---

## What this package is

Each skill is a `SKILL.md` file that provides Claude with context, conventions,
and rules for a specific task. Skills live under `.claude/skills/` and are
discovered automatically by Claude Code.

This package provides general-purpose skills shared across multiple projects.
Project-specific skills live in each project's own `.claude/skills/` directory
and are never added here.

---

## Skill naming conventions

Skills use a prefix that reflects their scope:

| Prefix | Scope |
|---|---|
| `x3p0-` | General purpose — applies to both themes and plugins |
| `x3p0-theme-` | WordPress theme specific |
| `x3p0-plugin-` | WordPress plugin specific |

The folder name is the skill name. Examples:
- `.claude/skills/x3p0-js-style/` — general JS conventions
- `.claude/skills/x3p0-theme-patterns/` — WordPress theme pattern conventions
- `.claude/skills/x3p0-plugin-settings/` — WordPress plugin settings conventions

---

## Adding a new skill

1. Create a folder under `.claude/skills/` following the naming convention
2. Add a `SKILL.md` file inside it with a frontmatter header and content:

```
---
name: x3p0-{name}
description: >
  One or two sentences describing what this skill covers and when to use it.
  Include specific trigger phrases.
---

# Skill Title

Content here.
```

3. The skill will be installed into consuming projects on their next
   `composer install` or `composer update`.

---

## The installer

`src/Installer.php` copies skills from `.claude/skills/` in this package into
`.claude/skills/` in the consuming project. It runs via Composer's
`post-install-cmd` and `post-update-cmd` hooks.

The installer is destructive — existing skill folders with matching names are
overwritten on each run. This is intentional.

Consuming projects may configure the destination path via `composer.json`:

```json
"extra": {
    "x3p0": {
        "skills": {
            "path": ".claude/skills"
        }
    }
}
```

And call the installer in their scripts:

```json
"scripts": {
    "post-install-cmd": ["X3P0\\Skills\\Installer::install"],
    "post-update-cmd": ["X3P0\\Skills\\Installer::install"]
}
```

Only update `Installer.php` if the installation behaviour needs to change —
not for adding or modifying skills.
