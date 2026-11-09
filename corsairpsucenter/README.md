# Corsair PSU Center with Dashboard tile

This combined plugin builds on [Corsair PSU Center by racoutlaw](https://github.com/racoutlaw/corsair-psu-center) and adds a native Unraid Dashboard tile using the kernel `corsair-psu` hwmon status API.

It automatically detects supported Corsair HXi/RMi supplies and includes the full PSU Center page: rail sensors, live charts, energy history, fan control, and OCP controls. The collapsed tile shows only output power, temperature, and load. Expand it for the rail utilization rows, temperature/fan, efficiency/supply, and PSU model description. The full PSU Center page is listed under **Settings → User Utilities**. The tile gear opens the full Corsair PSU Center page.

## Install or migrate

1. In **Plugins**, remove the old **Corsair PSU Statistics** plugin first to prevent its legacy USB polling from conflicting with the new plugin.
2. In **Plugins → Install Plugin**, install:

   `https://raw.githubusercontent.com/shiftylilbastrd/Unraid-plugins/main/corsairpsucenter/corsairpsucenter.plg`

3. Reload the Dashboard. Open **Settings → User Utilities → Corsair PSU Center** for the full page.

Requires Unraid 6.12 or newer. The installer downloads the pinned upstream package and verifies its MD5 checksum. The package includes its own Python runtime. The dashboard integration is maintained in this folder.

Distributed under GPL-2.0. The application and package are by racoutlaw; the dashboard tile and integration manifest are maintained here.
