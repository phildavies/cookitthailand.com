# COOK IT THAILAND DEPLOYMENT RULES

## Before Any Deployment

1. Identify whether the target is:
   - LOCAL (XAMPP)
   - LIVE (InMotion)

2. Run:

   git diff --name-only origin/master..HEAD

   and display every file that will be deployed.

3. If any of the following files appear in the deployment list:
   - .htaccess
   - .cpanel.yml
   - configuration.php
   - configuration.bak.php
   - .env
   - .env.\*

   STOP and request approval before proceeding.

4. Never commit or deploy .htaccess unless the task specifically requires modifying .htaccess.

5. Before modifying any protected file, show a unified diff and request approval.

## Protected Files

The following files require approval before modification or deployment:

- .htaccess
- .cpanel.yml
- configuration.php
- configuration.bak.php
- .env files

## Joomla Rules

1. Use template overrides whenever possible.

2. Template overrides belong under:

   templates/<template>/html/

3. Do not modify Joomla core files directly.

4. Do not overwrite Joomla core files unless explicitly instructed.

## Deployment Script Verification

Before deployment, review .cpanel.yml and confirm the following exclusions are present:

- .htaccess
- configuration.php
- configuration.bak.php
- .env
- .env.\*

## Permissions Verification

After deployment verify:

Folders:
755

Files:
644

.htaccess:
644

## Deployment Verification

Never assume deployment succeeded.

After deployment verify:

1. Homepage loads.
2. Joomla administrator loads.
3. At least one SEF URL loads.
4. No 403 errors are present.
5. No 500 errors are present.
6. The specific feature or fix being deployed works correctly.
7. The live site has been verified before declaring success.

## High Risk Changes

If a change affects any of the following:

- Routing
- URL rewriting
- .htaccess
- .cpanel.yml
- configuration.php
- File permissions
- Joomla deployment process

STOP and request approval before deployment.

## Local vs Live Safety

Never copy a local .htaccess file to the live server.

Never deploy a file containing:

RewriteBase /cookitthailand.com/

to the production server.

Always verify whether the target environment is LOCAL or LIVE before deployment.
