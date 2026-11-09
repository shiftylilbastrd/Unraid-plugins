# User share array disk allocation

Adds an **Array disks** column immediately to the right of **Storage** in Unraid's existing **Shares → User Shares** table.

The column lists allowed array disk numbers, such as `1, 3, 5`, or shows `All` when every assigned array disk is eligible. It is blank when the share has no eligible array disks, including pool-only shares. The value is calculated from Unraid's global and per-share include/exclude disk rules, not from the share's current file locations. The storage placement rules and the included/excluded disk lists are separate parts of Unraid share configuration; this column reports the effective eligible array disks.

## Install

In **Plugins → Install Plugin**, enter:

`https://raw.githubusercontent.com/shiftylilbastrd/Unraid-plugins/main/user-share-array-disk-allocation/user-share-array-disk-allocation.plg`

Requires Unraid 7.3.0 or newer. The plugin patches the two WebGUI files that render the User Shares table, reapplies the patch at boot, and removes only its marked additions on uninstall. Since this extends Unraid's built-in table, a future WebGUI redesign may require a plugin update.
