# Cosmetics v2.0 Plugin For PocketMine-MP
[Download Latest .phar Here](https://poggit.pmmp.io/ci/Vqnic/Cosmetics/~)

**This is a plugin for MCBE servers that will allow your players to equip fun cosmetics without some of the limitations of other cosmetics plugins.**
- Cosmetics can have custom 3D models and textures.
- Multiple cosmetics can be equipped at once. (Your players can have many combinations of hats, wings, tails and shoes at the same time.)
- Capes can be equipped by your players as well with textures you provide.
- Comes with default models and capes for easy use.
- Each cosmetic has its own permission node, which could be used for VIP perks.
- UI and commands are designed to be easy for players.

> [!NOTE]
> **The only command is  `/cosmetics`, and it is available by default to all players.**
> To restrict *individual cosmetics* to certain players or groups, establish a permission node in that cosmetic's configuration and use an external permission/group management plugin to determine who has that permission.


## What is the difference between v2.0 and the previous version?
This is a complete rewrite of the previous version because I noticed people will still referencing it years later and the code was, quite honestly, bad.

This version has **multiple cosmetic types** that can be on the body at once (it was just capes and one model before), and **easier configuration**. There are also a lot of **performance changes**.

## Future Things For This Project

**Adding more default cosmetics...**
My goal is to make configuration as easy as possible, and this includes usable examples.

**Bringing animations back to this plugin...**
I want server owners to be able to specify animations for each individual cosmetic. I don't think the old animation system was good enough.

**Support for 128x128 player skins...**
Currently, this plugin only supports 64x64 player skins. If a player logs in with a higher resolution skin and tries to equip a cosmetic, it will not use their skin and instead substitute it with Steve.
