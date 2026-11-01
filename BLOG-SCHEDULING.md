# Blog Publishing Schedule

## Write a Weekend Post

Create a Markdown file in `_posts/` using this filename format:

```text
_posts/YYYY-MM-DD-post-slug.md
```

Example:

```text
_posts/2026-11-05-wordpress-performance-roi.md
```

## Post Template

```markdown
---
layout: post
title: "WordPress Performance Optimization for Higher Revenue"
description: "How technical WordPress performance improvements protect conversion rates and reduce acquisition costs."
---

## Business Problem

Short introduction focused on the client's business outcome.

## Recommended Solution

Explain the technical approach and its ROI.

## Common Risks

List risks and why they matter.

## Next Steps

Summarize the action a business owner should take.
```

## Scheduling Rules

- Use the publication date in the filename and front matter.
- Dates are evaluated in UTC by GitHub Actions.
- Jekyll hides future-dated posts until the scheduled build runs after their date.
- The daily workflow runs at 09:00 UTC and rebuilds the site.
- GitHub scheduled workflows may start late or be delayed.

## Recommended Workflow

Write multiple posts on the weekend and commit them together, but use different filenames for each publication date:

```text
_posts/2026-11-03-wordpress-performance-roi.md
_posts/2026-11-05-shopify-development-services.md
_posts/2026-11-08-laravel-enterprise-development.md
```

The first post with a past or current date may publish during the next build, while future-dated posts wait for the scheduled build.
