# Spawner

Spawner is a historical PocketMine-MP plugin for buying and placing mob spawner blocks with EconomyAPI.

## Compatibility and status

The manifest declares PocketMine-MP API `2.0.0`. The source uses legacy block IDs, `MobSpawner`, `PluginTask` and an EconomyAPI import. It has not been ported to Axolotl-PM 5. Do not install or use it with real player balances on a current server.

The source imports `onebone\economyapi\EconomyAPI`, but `plugin.yml` does not declare a dependency. The `/mualong` command and prices in `settings.yml` are historical data, not instructions for a working Axolotl-PM deployment.

## Known risks

The cow purchase path calls `reduceMoney()` before checking whether the player has enough money. Other paths do not check the return value of `reduceMoney()` before giving an item. This can charge a player incorrectly or grant an item without a confirmed charge, depending on EconomyAPI behavior. A port needs a verified purchase transaction and tests before it can be used with player data.

There is no CI workflow, DevTools PHAR, Axolotl-PM 5 runtime test or verified release for this repository. No license file is present. Preserve the `Ghast Noob` author credit from `plugin.yml`, and resolve redistribution rights with the rights holder before publishing a modified build.

For questions about the NhanAZ ecosystem, use [NhanAZ Discord](https://discord.gg/j2X83ujT6c).
