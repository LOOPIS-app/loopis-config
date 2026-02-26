# 📋 Changelog for "LOOPIS Config"

## 0.85 (beta/staging)
- Broke out CHANGELOG from README, to match other repos.

## 0.84 (2025-12-12)
- Refactoring filenames
- Solved issues with install + troubleshooting
- Fixed some comments and syncing
- Removed obsolete functionality

## 0.83 (2025-12-12)
- Adjusted configuration of `pages, wp_options`
- Added `wp_loopis_config` and improved backend stucture
- Rewritten admin-page HTML
- Added a plugin options setter
- Added components installer and theme configurer

## 0.82 (2025-11-26)
- Adjusted configuration of `pages, cats, roles, wp_options`

## 0.81 (2025-11-25)
- Converted `root_files` from gitlink to regular folder
- Added template files for Maintenance Mode to `root_files`

## 0.8 (2025-11-16)
- LOOPIS `root_files` copied to WordPress root directory
- Adjusted configuration of `pages, roles, wp_options`

## 0.7 (2025-10-21)
- Insert of LOOPIS default users fixed and renamed to `loopis_insert_admins`
- Improved structure with dynamic inclusion of files in folders

## 0.6 (2025-10-06)
- Database reset tool moved to plugin `LOOPIS Develooper`
- Roles and capabilites page moved to plugin `LOOPIS Develooper`
- Separate page added for `Components`
- Custom error logging enabled

## 0.5 (2025-09-26)
- LOOPIS default page templates inserted in `wp_postmeta`
- LOOPIS pages for 'front', 'posts' and 'privacy-policy' set in `wp_options`
- WordPress admin default screen options set in `wp_usermeta`

## 0.4 (2025-09-25)
- LOOPIS default users inserted in `wp_users`
- LOOPIS default user roles set in `wp_options`
- LOOPIS default plugins installed in `Plugins`
- New section in WP Admin Area for viewing user roles capabilities

## 0.3 (2025-09-22)
- LOOPIS default categories inserted in `wp_terms`
- WordPress default content deleted in `wp_posts`
- WordPress default plugins deleted in `Plugins`
- New ajax buttons in WP Admin Area
- New folders for assets: `css, js, root_files`

## 0.2 (2025-09-18)
- User interface in WP Admin Area

## 0.1 (2025-09-12)
- LOOPIS custom table created: `loopis_lockers`
- LOOPIS custom table created: `loopis_settings`
- LOOPIS default settings inserted in `loopis_settings`
- LOOPIS default pages inserted in `wp_posts`
- LOOPIS default tags inserted in `wp_terms`
- WordPress default settings set in `wp_options`
- Database cleanup tool for development purposes

## 0.0 (2025-08-26)
- init commit with empty plugin file