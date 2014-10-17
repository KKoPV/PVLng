# PVLng

PhotoVoltaic Logger new generation

Please see http://pvlng.com for details

# Installation

If you want to use git für installing PVLng (recommended), you can simply execute from a shell
in the directory to install PVLng into:

```
wget -qO - https://raw.github.com/KKoPV/PVLng/master/tools/install | bash
```

The installer will ask for your MySQL credentials, install the basic SQL script and create
a basic configuration file with this credentials.

If you are interested in the latest development version, you can also direct install the development
branch with:

```
wget -qO - https://raw.github.com/KKoPV/PVLng/develop/tools/install | bash
```

## Upgrade

If a new master (or development) release is out, you can upgrade your repository and database with

```
tools/upgrade.git master
```

from your installation directory. This will checkout the latest changes from GitHub and
apply all outstanding database patches. (The script will work also with the develop branch.)

## Git hook

To make sure to clear the temp. directory after each `git pull`, put this
[Gist](https://gist.github.com/K-Ko/e7c01e0c7490ee4352fb) into `.git/hooks/post-merge` and make executable.

# Scripts for data acquisition

The scripts for data acquisition resides in its own repository: [PVLng-scripts](https://github.com/KKoPV/PVLng-scripts)

# Issues

Please use appropriately tagged github [issues](https://github.com/KKoPV/PVLng/issues) to request features or report bugs.

# Contributing

All code contributions and bug reports are much appreciated.

 - The project is managed with the [Git Flow branching model](http://nvie.com/posts/a-successful-git-branching-model/) and [tools](https://github.com/nvie/gitflow), so all pull requests **must** target the `develop` branch (not `master`)
 - Please use soft tabs (four spaces) instead of hard tabs
 - Include commenting where appropriate and add a descriptive pull request message
