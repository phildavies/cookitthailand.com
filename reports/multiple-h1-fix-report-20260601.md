# Multiple H1 Fix Report

Date: 2026-06-01

Scope: Joomla article body content only. Article titles, menu items, Helix Ultimate overrides, and page heading settings were not changed.

Database: `cookitthailand_live`

Table: `parawa6_content`

Backup: `reports/article-h1-backup-20260601-134124.sql`

## Changes

| Article ID | URL | Article title H1 kept | Body heading before | Body heading after |
|---:|---|---|---|---|
| 1 | `http://localhost/cookitthailand.com/` | `Experience the Joy of Thai Cooking Classes Across Thailand` | `<h1>Thai Cooking Classes in Thailand</h1>` | `<h2>Thai Cooking Classes in Thailand</h2>` |
| 44 | `http://localhost/cookitthailand.com/locations/thai-cooking-class-samui` | `Join Our Koh Samui Thai Cooking Class for Authentic Flavors` | `<h1>Cooking Class Koh Samui</h1>` | `<h2>Cooking Class Koh Samui</h2>` |
| 50 | `http://localhost/cookitthailand.com/locations/cooking-class-koh-phangan` | `Explore the Delicious Culinary Offerings of Koh Phangan` | `<h1>Koh Phangan On Koh Phangan \| Discover Thai Culinary Delights</h1>` | `<h2>Koh Phangan On Koh Phangan \| Discover Thai Culinary Delights</h2>` |
| 52 | `http://localhost/cookitthailand.com/locations/koh-tao` | `Experience Authentic Thai Cuisine in Our Koh Tao Cooking Class` | `<h1 style="text-align: center;">Come and take a Thai Cooking Class with me on Koh Tao</h1>` | `<h2 style="text-align: center;">Come and take a Thai Cooking Class with me on Koh Tao</h2>` |

## Render Verification

| URL | H1 count after | Remaining H1 |
|---|---:|---|
| `http://localhost/cookitthailand.com/` | 1 | `Experience the Joy of Thai Cooking Classes Across Thailand` |
| `http://localhost/cookitthailand.com/locations/thai-cooking-class-samui` | 1 | `Join Our Koh Samui Thai Cooking Class for Authentic Flavors` |
| `http://localhost/cookitthailand.com/locations/cooking-class-koh-phangan` | 1 | `Explore the Delicious Culinary Offerings of Koh Phangan` |
| `http://localhost/cookitthailand.com/locations/koh-tao` | 1 | `Experience Authentic Thai Cuisine in Our Koh Tao Cooking Class` |

## SQL Applied

```sql
UPDATE parawa6_content
SET introtext = REPLACE(introtext, '<h1>Thai Cooking Classes in Thailand</h1>', '<h2>Thai Cooking Classes in Thailand</h2>'),
    modified = NOW(), modified_by = 0
WHERE id = 1;

UPDATE parawa6_content
SET introtext = REPLACE(introtext, '<h1>Cooking Class Koh Samui</h1>', '<h2>Cooking Class Koh Samui</h2>'),
    modified = NOW(), modified_by = 0
WHERE id = 44;

UPDATE parawa6_content
SET introtext = REPLACE(introtext, '<h1>Koh Phangan On Koh Phangan | Discover Thai Culinary Delights</h1>', '<h2>Koh Phangan On Koh Phangan | Discover Thai Culinary Delights</h2>'),
    modified = NOW(), modified_by = 0
WHERE id = 50;

UPDATE parawa6_content
SET introtext = REPLACE(introtext, '<h1 style="text-align: center;">Come and take a Thai Cooking Class with me on Koh Tao</h1>', '<h2 style="text-align: center;">Come and take a Thai Cooking Class with me on Koh Tao</h2>'),
    modified = NOW(), modified_by = 0
WHERE id = 52;
```
