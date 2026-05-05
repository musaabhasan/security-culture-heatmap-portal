# Extension Guide

## Add a Dimension

Add a dimension object to `config/portal.json`, then add a matching seed row in `database/seeders/001_seed_portal.sql`.

## Add a Workflow

Add a new workflow step to the `workflows` array in `config/portal.json`. It will appear automatically in `/roadmap`.

## Add Approval or Review

Add authenticated admin routes and a review table when assessments require formal sign-off.

## Add Reporting

Use `/api/summary` as the integration base for dashboards, BI tools, or scheduled reporting jobs.
