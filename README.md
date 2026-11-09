# CPU Tile Summary for Unraid

For Unraid 7.3.4. This plugin changes the **existing Processor tile description**: when that tile is collapsed, the CPU model description is replaced by total power, temperature, and overall load with a small load bar. Expanding the tile restores the CPU model description. It does not create a tile or add a separate row.

The plugin reuses the live values already rendered by Unraid in the expanded Processor tile, so units and sensor support stay consistent with Unraid. If a value is unavailable in the native tile, the compact description displays a dash.

## Install from GitHub

In Unraid, open **Plugins → Install Plugin**, paste this URL, and click **Install**:

```text
https://raw.githubusercontent.com/shiftylilbastrd/Unraid-Dashboard/main/cpu-tile-summary.plg
```

Hard-refresh the Dashboard with **Ctrl+F5** after installation. The manifest is self-contained and embeds the page hook.

## Remove

Use the plugin's **Remove** action from the Plugins page, then refresh the Dashboard. It restores the standard tile because the plugin only injects browser-side code and does not edit Unraid's core files.

The hook uses the WebGUI `Menu="Buttons"` page mechanism and the Dashboard's CPU marker. The Dashboard source exposes the power, temperature, and aggregate load values used by this plugin: [Unraid WebGUI `DashStats.page`](https://github.com/unraid/webgui/blob/master/emhttp/plugins/dynamix/DashStats.page).
