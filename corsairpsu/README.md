# Corsair PSU dashboard fix

A maintained fork for Unraid 6.12 and newer, including Unraid 7.x. It layers corrected dashboard files over the upstream Corsair PSU package.

Fixes:
- Uses Unraid's native tile header structure so the gear and expand/collapse controls appear in the upper-right corner.
- Maps the RMi/HXi output rails by the documented output order, so a measured 11.9V reading remains in the 12V field.
- Restores the RMi/HXi uptime and total-runtime row.
- Labels the 3.3V rail accurately.

Install from **Plugins → Install Plugin** with:

```text
https://raw.githubusercontent.com/shiftylilbastrd/Unraid-plugins/main/corsairpsu/corsairpsu.plg
```

The installer uses the upstream `corsairpsu-2025.11.23` package for the PSU drivers, then applies the maintained Dashboard and parser fixes in this folder. Upstream project: https://github.com/giganode/corsairpsu-unraid

This folder contains source for the two patched files and the install manifest. The upstream project’s GPL license is included.
