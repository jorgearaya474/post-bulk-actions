=== Post Bulk Actions ===
Contributors: jorgearaya  
Donate link: https://jorgearaya.com/donate  
Tags: bulk actions, posts, pages, editor, tabs, admin, productivity  
Requires at least: 5.0  
Tested up to: 6.4  
Requires PHP: 7.4  
Stable tag: 1.0.0  
License: GPLv2 or later  
License URI: https://www.gnu.org/licenses/gpl-2.0.html  

Open multiple selected posts/pages in new editor tabs for efficient bulk editing.

== Description ==

Post Bulk Actions enhances the WordPress admin experience by adding new bulk actions that allow you to open multiple posts or pages simultaneously in new browser tabs.

**Features:**

* **Bulk Edit Tabs** – Select multiple posts/pages and open them all in separate editor tabs  
* **Bulk View Tabs** – Select multiple posts/pages and open them all in separate view tabs  
* **Smart Popup Detection** – Detects when browser blocks popups and alerts the user  
* **Permission Checks** – Respects user capabilities and only processes posts the user can edit  
* **Clean Interface** – Integrates seamlessly with the existing WordPress bulk actions dropdown  
* **Follows WP Standards** – JavaScript code follows WordPress coding standards

**Perfect for:**

* Content managers who need to edit multiple posts quickly  
* Developers working on multiple pages simultaneously  
* Anyone who wants to streamline their WordPress editing workflow

**How to Use:**

1. Go to Posts or Pages in your WordPress admin  
2. Select the posts/pages you want to work with using the checkboxes  
3. Choose "Open in Edit Tabs" or "Open in View Tabs" from the bulk actions dropdown  
4. Click "Apply"  
5. All selected items will open in new browser tabs

**Browser Compatibility:**

Works with all modern browsers. If your browser blocks popups, the plugin will detect this and show you an alert with instructions.

== Installation ==

1. Upload the `post-bulk-actions` folder to the `/wp-content/plugins/` directory  
2. Activate the plugin through the 'Plugins' menu in WordPress  
3. Go to Posts or Pages and you'll see the new bulk actions available

== Frequently Asked Questions ==

= Does this work with custom post types? =  
Currently, the plugin works with Posts and Pages. Support for custom post types may be added in future versions.

= What happens if my browser blocks popups? =  
The plugin detects when popups are blocked and will show you an alert. You'll need to allow popups for your WordPress site to use this feature.

= Can I open more than 10 tabs at once? =  
Yes, but the plugin will ask for confirmation when opening more than 3 tabs to prevent accidental overload of your browser.

= Does this work with WordPress multisite? =  
The plugin works on individual sites within a multisite network, but it's not specifically designed for network-wide activation.

== Screenshots ==

1. Bulk actions dropdown showing the new options  
2. Multiple editor tabs opened after using bulk edit action  
3. Confirmation dialog for opening multiple tabs  
4. Popup blocked alert notification

== Upgrade Notice ==

= 1.0.0 =  
Initial release of Post Bulk Actions plugin.

== Changelog ==

= 1.0.0 =
* Initial release  
* Added bulk edit tabs functionality  
* Added bulk view tabs functionality  
* Added popup detection and user notifications  
* Added permission checks and validation  
* JavaScript written following WordPress coding standards  
* Support for Posts and Pages only

== Developer Notes ==

**Action Hooks:**
- Uses core WordPress bulk action filters  
- No custom actions introduced

**Filters:**
- `bulk_actions-edit-post` and `bulk_actions-edit-page` to add bulk options  
- `handle_bulk_actions-edit-post` and `handle_bulk_actions-edit-page` to handle actions

**JavaScript:**
- Enqueues a single jQuery-based file  
- Handles tab opening with popup detection and validation  
- Compliant with WordPress JavaScript coding standards  

**Security:**
- All inputs sanitized  
- Capability and nonce checks applied to bulk actions  

**Performance:**
- Loads only on `edit.php` pages for posts/pages  
- No persistent data stored or queried
