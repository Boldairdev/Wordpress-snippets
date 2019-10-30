# Wordpress-snippets
Some useful snippets to use in function.php or in the code snippets plugin

## filter_tags_in_a_given_category.php

  This snippet adds a shortcode [filter_by_tags_form] which outputs a form with all the tags in the current category for the post or archive where the form is located. When submitted, the snippet filters a list of post with the selected tag
  
## store_current_post_id_in_js_var

A very simple snippet that makes the current post id in Wordpress available to js, in the document <head>. Use in function.php or in the code snippets plugin
  
## purge_modpagespeed_snippet.php

Calls the mod_pagespeed module to clear its cache
 
mod_pagespeed configuration (global or by vhost) must have these defined :

* ModPagespeedEnableCachePurge on
* ModPagespeedPurgeMethod PURGE

Usage :

https://wordpress-site.com/path/to/post/?purge=1 : purges the whole cache

https://wordpress-site.com/path/to/post/?purge=1&page=1 : purges the cache for the current_page_only

use in function.php or with the Code Snippets plugin  
