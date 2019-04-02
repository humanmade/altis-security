# Audit Log

All change activity made in the CMS is tracked in the Audit Log. This provides a historical account of who changed what, when. The Audit Log is "always on" and tracks changes across all sites.

Audit logging base functionality is provided by the [Stream](https://github.com/xwp/stream/) plugin.

The Audit Log is tamper resistent. Once entries have been added to the Audit Log, they can not be removed. This is to preserve knowledge of historical changes for auditing and compliance purposes. The Cloud infrastructure application layer has no permissions to delete or modify records, therefore it's even resilient to modification from rogue custom code.

The Audit Log will also be persistent across site restores, overrides and imports.

The Audit Log is only available to users who have access to the Network Admin, which by default is `super-admin` users.

## Recorded Actions

The Audit Log records create, update and delete actions for the following content types:

- Posts
- Pages
- Custom Post Types
- Users
- Themes
- Plugins
- Tags
- Categories
- Custom Taxonomies
- Settings
- Custom Backgrounds
- Custom Headers
- Menus
- Media Library
- Widgets
- Comments

## Custom Action Recording

Any custom functionality or data types that are built on CMS primitives such as Custom Post Types, Custom Taxonomies, Post Meta or similar will already be tracked by default in the Audit Log. There are situations where you may want to insert your own custom records for reporting / compliance purposes. For example, you have built a feature with a custom database table, and want to track changes made to those entities.

In this scenario, you are responsible for also triggering the necessary API calls to the Audit Log in the application code. See the detailed documentation on [creating custom Connectors](https://github.com/xwp/stream/wiki/Creating-a-Custom-Connector) via the Stream plugin documentation.

Once registered, your custom Stream Connector's records will be part of the Audit Log with the same data integrity guarantees.
