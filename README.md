# Spawner

EconomyAPI spawner shop for Axolotl-PM API 5; mob spawning depends on the server.

[![CI](https://github.com/NhanAZ-Plugins/Spawner/actions/workflows/build.yml/badge.svg)](https://github.com/NhanAZ-Plugins/Spawner/actions/workflows/build.yml)
[![Latest release](https://img.shields.io/github/v/release/NhanAZ-Plugins/Spawner)](https://github.com/NhanAZ-Plugins/Spawner/releases/latest)
[![Discord](https://img.shields.io/discord/986553214889517088?label=Discord&logo=discord&logoColor=white&color=5865F2)](https://discord.gg/j2X83ujT6c)


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

The repository preserves the historical `NhanAZ` author credit. No `LICENSE` file is present, so the source does not grant a general reuse license.


## Credits

Authors: NhanAZ.
