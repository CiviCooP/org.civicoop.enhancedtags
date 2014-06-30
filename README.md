org.civicoop.enhancedtags
=========================

Generic CiviCRM extension to enhance tag functionality with start and end dates and coordinator

This extension adds the possibility to add coordinator data to a CiviCRM tag. It enables you to select a coordinator and set a start and end date. Please note that in this configuration, coordinators need to be of the contact subtype 'Expert'.

<strong><em>This extension is still being developed.</em></strong>

<h2>Functional demand</h2>
The functional demand at the original customer funding this development is that they need to classify customers in sectors, for example the logistics sector or the agrarian sector. Within these sectors they should be able to have subsector, like logistics/frozen transport or agrarian/milk production. CiviCRM tags deliver just that solution.
However, they also want to be able to set a coordinator for that specific sector (or subsector), much like you can set a relationship. There will only be one coordinator active at the time, but we want to be able to see if someone has been a coordinator in the past when they leave the organization.

<h2>Solution</h2>
We have introduced a new table called civicrm_tag_enhanced with the fields id, tag_id, coordinator_id, start_date, end_date and is_active.

<h3>Adding and editing</h3>
When adding or editing a tag, the user can select a coordinator from a select list (all contacts with contact sub type Expert). When a new coordinator is introduced, a new record is added to the file civicrm_tag_enhanced.
When the user in edit mode changes the coordinator, the record of the old tag/coordinator combination is set to is_active = 0 and the end_date is set to either (start_date new coordinator - 1 day) or today - 1 day if there is no start date for the new coordinator. This way the history is kept.
<h3>Scheduled job for optimization</h3>
This could lead to records where the end_date is actually earlier than the start date. These records are removed with the scheduled job TagEnhanced.Optimize. This scheduled job is set to run always when cron jobs are run.
<h3>Merging</h3>
When merging tags, all enhanced tags of the 'old' tag are moved to the new tag, unless the new tag/coordinator combination already exists. If the latter is the case, the 'old' record is deleted.
<h3>Contact summary</h3>
On the contact summary of the current or old tag coordinator a field is added showing that the contact has been or still is a tag coordinator with the start and end date.
<h3>Api</H3>
In the extension there also is an API called TagEnhanced Get that will help you to retrieve the coordinator for a tag.



