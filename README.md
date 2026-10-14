# Spawner

Spawner is a historical PocketMine-MP plugin for buying and placing mob spawner blocks with EconomyAPI.

## Compatibility and status

The manifest declares PocketMine-MP API `2.0.0`. The source uses legacy block IDs, `MobSpawner`, `PluginTask` and EconomyAPI. It has not been ported to Axolotl-PM 5. Do not install it on a current Axolotl-PM server or use it with real player balances.

The manifest now declares `EconomyAPI` as a required plugin. `/mualong` purchases require a positive configured price, a sufficient balance, a successful EconomyAPI transaction result and inventory capacity before the spawner is delivered. If an inventory change during the synchronous transaction prevents delivery, the plugin attempts a checked refund and drops the item if the refund is rejected. Spawner entity IDs are mapped consistently when placing and recovering a spawner.

## Maintenance limits

There is no CI workflow, DevTools PHAR, Axolotl-PM 5 runtime test or verified release for this repository. No license file is present. Preserve the `Ghast Noob` author credit from `plugin.yml`, and resolve redistribution rights before publishing a modified build.

For questions about the NhanAZ ecosystem, use [NhanAZ Discord](https://discord.gg/j2X83ujT6c).
