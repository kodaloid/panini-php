## Version: 0.1 (Build 0005) [alpha] - 11/05/2021

I accidentally removed the Git repository thinking that I had it hosted elsewhere,
anyway the repo is back (annoyingly without the previous commits). This build
mostly includes some typo fixes and I renamed the default controller and view from
"root" to "index" as it makes more sense. Also I fixed the indentation on source
files that previously used space characters, they now use tabs.

## Version: 0.1 (Build 0004) [alpha]

After a lot of thought, I have realised a module system simplifies providing
out of the box features that can be ignored/deleted if not required. This way
I don't have to pollute the system folder with bloat. Users now load modules
into the app with an alias, accessed for example like: $this->modules->database

- Added the modules folder.
- Added new App\Modules\User_Manager module.
- Converted PaniniBasicDatabase into App\Modules\MySQL_Database.
- Converted PaniniBasicUserManagement into App\Modules\User_Manager_Eloquent.
- Tidied up procedural code by converting into static class methods, for example
  implode_path is now found at App\Helper::implode_path.
- Refactored code to remove redundant 'public' keyword as it is default.
- Added a dummy file to make sure the model folder gets created.

## Version: 0.1 (Build 0003) [alpha]

I realised this patch that users might not always want to include a large
database framework like Eloquent. So patched in a class that can handle the
basics out of the box.

- Added the PaniniBasicDatabase.
- Added a get_user function to the PaniniBasicUserManagement class.
- Updated the composer.json file with a title and description.

## Version: 0.1 (Build 0002) [alpha]

Made some crucial decisions in this build that I expect will remain permanent.
These decisions include renaming web to app, and removal of the choice of naming
conventions.

- Path defines now use \_\_ROOT\_\_ for primary, and \_\_APP\_\_ for user code.
- Configured settings to my local environment/wamp for now. When cloning, remember to setup config.php.
- Removed session_start() from index.php as sessions should be implemented by user if needed.
- Renamed the web folder to app to unify concept of "app" code.
- Renamed the web/web.php file to app/app.php file to further unify the "app" idea.
- Refactored code in several areas to clean things up, functions all use same naming convention now.
- Documented a lot of code to explain what things do, and why.
- Documented the app/app.php to explain what it's for, and how to set-up a database connection.
- Added the present_json() function as an alternative to present_view() for JSON JSend output.
- Added the PaniniBasicUserManager class utility for enabling basic user features via Illuminate.
- Added clauses to stop system files being accessed directly.

## Version: 0.1 (Build 0001) [alpha]

- Uploaded initial project files.