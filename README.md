# Spawner

Spawner provides an EconomyAPI shop for configured mob spawner blocks on Axolotl-PM API 5.

## Compatibility

- Minimum Axolotl-PM API: `5.0.0`.
- CI checks PHPStan at maximum level and builds against the exact Axolotl-PM API source revisions for `5.0.0` and `5.49.2`. These are the verified compatibility points; intermediate patch releases are not inferred from the matrix.
- Runtime smoke tests load the built plugin with EconomyAPI on Axolotl-PM `5.49.2`.
- Requires the `EconomyAPI` plugin.

## Current server limitation

This plugin stores the Bedrock `EntityIdentifier` on purchased spawner items, restores the type when a Silk Touch spawner is collected, and preserves the spawner tile data. Axolotl-PM `5.49.2` currently leaves the vanilla monster spawner's scheduled update unimplemented, so the server does not spawn mobs from these blocks. The shop and type-data handling are tested; mob spawning is not provided by this release.

The supported shop types are zombie, skeleton, pigman, spider, iron golem, blaze, pig, cow, chicken and squid. Their prices are in the generated `settings.yml` file in the plugin data folder.

## Build and validation

GitHub Actions uses [DevTools](https://github.com/NhanAZ/DevTools) to run PHPStan at maximum level, build the plugin PHAR and validate its contents. Workflow dependencies and server artifacts are pinned to exact revisions or checksums for reproducibility. The hosted workflow also performs a server startup smoke test with the built PHAR.

The repository preserves the historical `Ghast Noob` author credit. No `LICENSE` file is present, so the source does not grant a general reuse license.

For questions about the NhanAZ ecosystem, use [NhanAZ Discord](https://discord.gg/j2X83ujT6c).
