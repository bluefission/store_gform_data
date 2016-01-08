# store_gform_data
Snippet used to store gravity form data as a server session and be used in other plugins

I totally forgot that I used my personal library to shortcut this class, but here are the classes you need.

I have a WP plugin built to autoload these if creating your own autoloader is a pain, so let me know

The class with the methods you will need is the GFormManager class

You're looking specifically for

GFormManager::load_form_data() copies data from the form and creates a label/key for each value
GFormManager::snapshot() stores a session value
GFormManager::develop() unpacks the session value into the class
GFormManager::fill_fields() repopulates the gravity form you're on

please look at "pluginsample.php" for more