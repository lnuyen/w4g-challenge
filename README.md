## Synopsis

A WordPress plugin that allows you to run and manage a challenge directly from your website.

## Installation

1. First, you need to download the plugin (which will be a zip file)
2. Next, you need to go to WordPress admin area and visit Plugins » Add New page
3. After that, click on the Upload Plugin button on top of the page
4. This will bring you to the plugin upload page. Here you need to click on the choose file button and select the plugin file you downloaded earlier to your computer
5. After you have selected the file, you need to click on the install now button
6. Once installed, you need to click on the Activate Plugin link to start using the plugin

## Settings

### Challenge Status
The challenge has three different states: Pre-Challenge, Active and Post-Challenge. When you activate the plugin, your challenge is in the Pre-Challenge state. Change the Challenge Status to "Active" when you're ready to begin accepting applications. Change the Challenge Status to "Post-Challenge" when the challenge is over.

### User Registration
In order for people to be able to submit applications while the challenge is Active, you have to turn on user registration.
By default, it is turned off but you can easily turn it on:

1. Head over to Settings » General page in your WordPress admin area
2. Scroll down to the ‘Membership’ section and check the box next to ‘Anyone can register’ option.
3. Next you need to select the default user role 'Author'
4. Save Changes

### Application Settings

*Add Fields to the Application*

When you install the Challenge plugin, the application will have only one field: Project Name. To add fields to your application you need to create a Field Group in Advanced Custom Fields:

1. Go to WordPress admin area and visit Custom Fields
2. After that, click on the Add New button on top of the page
3. Enter a title for your Field Group (i.e. Application Fields)
4. Find the Location section, under "Show this field group if" select "Post Type" "is equal to" "challenge_app"
5. Find the Options section, set the Style to "Seamless (no metabox)"
4. Click the + Add Field button to begin adding fields
4. Accepted field types include: Text, Text Area, Number, Email, Wysisyg Editor, File, Select, Checkbox, Radio Button, True /False (when using the "File" field type, set Return Value to File Object)
5. When the Field Group is ready, click the Publish button (the Field Group can be edited at any time by returning to this page)
6. Next, you need to get the Field Group ID. Look at the URL and find the number preceded by "?post=" -- this is the Field Group ID
7. Return to the Challenge Settings page and add the Field Group ID number to the field: ACF Field Group ID, then click the button Save Changes
8. Your application will now include all fields from your Field Group

*Editing Capabilities*

Choose whether or not to allow applicants to edit their own applications after submitting and while challenge is active.

### Application Page

When you activate the plugin, the Application page is created automatically. There are two ways to add content to the Application page:

1. *via the Page Editor*
   
   Add content above and below the shortcode [w4g_application_form]. This content shows at all times.
2. *via the Setting Page*
   
   Add conditional messaging to the Application page via the Settings page. This messaging shows directly above the Application and varies depending on the Challenge Status. i.e. Pre-Challenge messaging appears above the application while challenge status is Pre-Challenge, etc.

## Includes

This plugin includes [Advanced Custom Fields](https://www.advancedcustomfields.com/)

## License

[https://www.gnu.org/licenses/gpl-2.0.html](https://www.gnu.org/licenses/gpl-2.0.html)
