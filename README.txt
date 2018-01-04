INTRODUCTION
------------

This module adds captions to images in two ways, depending on the user's
choice: using JQuery to dynamically add them, and using an Input Filter that
looks for img tags with the specified classes, and adds captions (without using
 Javascript). The image title attribute is used to create the caption text.
 
  * For a full description of the module, visit the project page:
   https://drupal.org/project/image_caption

 * To submit bug reports and feature suggestions, or to track changes:
   https://drupal.org/project/issues/image_caption
  
REQUIREMENTS
------------

No special requirements.

RECOMMENDED MODULES
-------------------

 * TinyMCE 'advanced image' plugin (https://www.tinymce.com/) - The creator of
 this module recommends it to select the caption class and set the image
  title more easily when creating content.
  
INSTALLATION
------------
 
 * Install as you would normally install a contributed Drupal module. Visit:
   https://drupal.org/documentation/install/modules-themes/modules-7
   for further information.

 * You may want to disable Caption Image filter, as it may clash with this 
   module.

CONFIGURATION
-------------

Go to yoursite.com/admin/config/content/formats/manage/full_html and scroll
down until you see the "Image Caption" filter. Enable it, and choose how you
to want to create and display the captions.

USAGE INSTRUCTIONS
------------------

 * If you choose the "Using Javascript" option, captions will only be added to
   images that have the "caption" class through JQuery. This means that in
   order for your site's visitors will need to have Javascript enabled. You
   will also not be able to add captions to images that do not have the
   "caption" class. To style your captions, you can add the empty css
   definition:

   .caption{} 
   
   to the stylesheet used by your WYSIWYG editor (to enable it to appear in
   the style select box on the editor toolbar, or class select box). You might
   be able to add it to your theme's style.css to get it to appear in the class
   dropdown box in your WYSIWYG editor.
   
 * If you choose the "Without Using Javascript" option, captions will be added
   wihout using Javascript. You can also choose your own classes to which you
   want the captions, so you could add different styling to
   captions having different classes (using the same styling method as
   described above.). "caption" is the default value for the "Classes to be
   searched for image captions" field, so as to prevent any issues if you are
   switching from the 'Using Javascript' method. Feel free to remove it if the
   latter isn't the case for you.
   
MAINTAINERS
-----------

Current maintainers:

 * Kifah Meeran (Kifah Meeran) - https://drupal.org/user/3509455
 * David Thomas (davidwhthomas) - https://www.drupal.org/u/davidwhthomas

This project has been partly ported by a Google Code-In student.

 * Google Code-In is an international contest for high-school students that
   allows them to contribute to open source projects while earning cool
   prizes. See more here: http://g.co/gci
