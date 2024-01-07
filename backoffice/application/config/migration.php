
<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
|--------------------------------------------------------------------------
| Enable/Disable Migrations
|--------------------------------------------------------------------------
|
| Migrations are disabled by default for security reasons.
| You should enable migrations whenever you intend to do a schema migration
| and disable it back when you're done.
|
*/
$config['migration_enabled'] = TRUE;

/*
|--------------------------------------------------------------------------
| Migration Type
|--------------------------------------------------------------------------
|
| Migration file names may be based on a sequential identifier or on
| a timestamp. Options are:
|
|   'sequential' = Sequential migration naming (001_add_blog.php)
|   'timestamp'  = Timestamp migration naming (20121031104401_add_blog.php)
|                  Use timestamp format YYYYMMDDHHIISS.
|
| Note: If this configuration value is missing the Migration library
|       defaults to 'sequential' for backward compatibility with CI2.
|
*/
$config['migration_type'] = 'timestamp';

/*
|--------------------------------------------------------------------------
| Migrations table
|--------------------------------------------------------------------------
|
| This is the name of the table that will store the current migrations state.
| When migrations runs it will store in a database table which migration
| level the system is at. It then compares the migration level in this
| table to the $config['migration_version'] if they are not the same it
| will migrate up. This must be set.
|
*/
$config['migration_table'] = 'migrations';

/*
|--------------------------------------------------------------------------
| Auto Migrate To Latest
|--------------------------------------------------------------------------
|
| If this is set to TRUE when you load the migrations class and have
| $config['migration_enabled'] set to TRUE the system will auto migrate
| to your latest migration (whatever $config['migration_version'] is
| set to). This way you do not have to call migrations anywhere else
| in your code to have the latest migration.
|
*/
$config['migration_auto_latest'] = FALSE;

/*
|--------------------------------------------------------------------------
| Migrations version
|--------------------------------------------------------------------------
|
| This is used to set migration version that the file system should be on.
| If you run $this->migration->current() this is the version that schema will
| be upgraded / downgraded to.
|
*/

// $config['migration_version'] = 0;
// $config['migration_version'] = 20200208164832;
// $config['migration_version'] = 20200218111830; // sms contents
// $config['migration_version'] = 20200222095259; // subscription config
// $config['migration_version'] = 20200225100130; // password policy
// $config['migration_version'] = 20200228151659; // Tree Icon based On config
// $config['migration_version'] = 20200303103359; // custome add registration field
// $config['migration_version'] = 20200312130000; // user_dashboard_config
// $config['migration_version'] = 20200617165152; // pv_updation_history
// $config['migration_version'] = 20200617170435; // block_binary_pv
// $config['migration_version'] = 20200617172032; // username_length_range
// $config['migration_version'] = 20200618105336; // user_deletion
// $config['migration_version'] = 20200618151825; // closure_table
// $config['migration_version'] = 20200629183135; // adv_settings_subscr_report_menu
// $config['migration_version'] = 20200803114948; // backoffice_oc_language_sync
// $config['migration_version'] = 20200916120430; // bank_transfer_in_package_upgrade.
// $config['migration_version'] = 20200916174930; // bank_info_on_bank_payment.
// $config['migration_version'] = 20201105102030; // menu_changes.
// $config['migration_version'] = 20201111152323; // user_dashboard_items
// $config['migration_version'] = 20201112150001; // tree_updation_status
// $config['migration_version'] = 20201118122630; // menu_submenu_changes
// $config['migration_version'] = 20201204142525; // add tree_icon column in oc_product
// $config['migration_version'] = 20201211122626; // create user menus in new ui
// $config['migration_version'] = 20201215095454; // create user menus in new ui
// $config['migration_version'] = 20201215120202; // Disable Old Menus User(Ewallet, Payout, Epin)
// $config['migration_version'] = 20201215172929; // Disable Old Menus User(User Earnings)
// $config['migration_version'] = 20201217121515; // Delete old submenus
// $config['migration_version'] = 20210101172055; // add_unique_key_to_level_no_level_commission
// $config['migration_version'] = 20210108122700; // Create_package_upgrade_subs_renewal_menu
// $config['migration_version'] = 20210108171300; // Add_subscription_status_demo_column_in_module_status
// $config['migration_version'] = 20210111104130; // user profile menu
// $config['migration_version'] = 20210114111100; // Report Menu user side
// $config['migration_version'] = 20210121173000; // change_default_payout_method_user_details
// $config['migration_version'] = 20210121173000; // change_default_payout_method_user_details
// $config['migration_version'] = 20210121173000; // change_default_payout_method_user_details
// $config['migration_version'] = 20210123120145; // react_menu
// $config['migration_version'] = 20210201180545; // create_username_change_history_table
// $config['migration_version'] = 20210203103800; // remove report menu submenu user side
// $config['migration_version'] = 20210211173100; // opencart_default zone id
// $config['migration_version'] = 20210215175700; // Disable user earnigs menu user side
// $config['migration_version'] = 20210222124630; // Upgrade and renewal hidden menu
// $config['migration_version'] = 20210224115300; // remove product validity column module status
// $config['migration_version'] = 20210224143300; // remove package validity column package tbl
// $config['migration_version'] = 20210226120900; // menu permission subscription default 1 to all login type
// $config['migration_version'] = 20210226172930; // rank status demo
// $config['migration_version'] = 20210304190900; // delete ewallet old urls and menus
// $config['migration_version'] = 20210305163130; // remove payment methods table
// $config['migration_version'] = 20210309121330; // some profile submenus removal
// $config['migration_version'] = 20210310172600; // delete profile old urls and menus
$config['migration_version'] = 20210318163609; // delete forein keys in ewallet payment details
/*
|--------------------------------------------------------------------------
| Migrations Path
|--------------------------------------------------------------------------
|
| Path to your migrations folder.
| Typically, it will be within your application path.
| Also, writing permission is required within the migrations path.
|
*/
$config['migration_path'] = APPPATH.'migrations/';
