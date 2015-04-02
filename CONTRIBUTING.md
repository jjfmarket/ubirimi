# How to contribute

Community made patches, bug reports and contributions are always welcome and are crucial to ensure *Ubirimi* remains
a useful and up today productivity platform. This should be as easy as possible for you but there are few things to consider when contributing. The following guidelines for contribution should be followed if you want to submit a Pull Request.

## How to prepare

* You need a [GitHub account](https://github.com/signup/free)
* Submit an [issue ticket](https://github.com/ubirimi/ubirimi/issues) for your issue, assuming one does not already
exist.
	* Clearly Describe the issue including steps to reproduce when it's a bug.
	* Include your server type and version
	* Include your php version
* Fork the repository on GitHub

## Make Changes

* In your forked repository, create a topic branch for your upcoming patch.
	* Usually this is based on the master branch.
	* Create a branch based on master; `git checkout -b
	fix/master/my_contribution master`.  Please avoid working directly on the `master` branch.
* Make commits of logical units and describe them properly.
* Check for unnecessary whitespace with `git diff --check` before committing.
* If possible, submit tests to your patch/new feature so it can be tested easily.
* Assure nothing is broken by running all the tests.

## Submit Changes

* Push your changes to a topic branch in your fork of the repository.
* **SQUASH COMMITS INTO A SINGLE COMMIT**
* Open a pull request to the original repository and choose the right original branch you want to patch.
* If not done in commit messages (which you really should do) please reference and update your issue with the code changes.
* Even if you have write access to the repository, do not directly push or merge pull-requests. Let another team member review your pull request and approve.

# Additional Resources

* [General GitHub documentation](http://help.github.com/)
* [GitHub pull request documentation](http://help.github.com/send-pull-requests/)
