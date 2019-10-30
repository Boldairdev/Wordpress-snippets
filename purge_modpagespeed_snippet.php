/* purge_modspagespeed v1.0
 * No maintenance intended
 *
 * (c) Eric Bouquerel - 2019
 *
 * Calls the mod_pagespeed module to clear its cache
 *
 * mod_pagespeed configuration (global or by vhost) must have these defined :
 *
 * ModPagespeedEnableCachePurge on
 * ModPagespeedPurgeMethod PURGE
 *
 * Usage :
 * https://wordpress-site.com/path/to/post/?purge=1 : purges the whole cache
 * https://wordpress-site.com/path/to/post/?purge=1&page=1 : purges the cache for the current_page_only
 *
 * use in function.php or with the Code Snippets plugin
 *
 */

function bda_purge_modpagespeed() {
    // make sure you call the right protocol (http or https);
    $url = "https://".$_SERVER["HTTP_HOST"];
    if(isset($_GET['purge'])) {
        if(isset($_GET['page'])) {
            $url .= str_replace('?'.$_SERVER["QUERY_STRING"],'',$_SERVER[REQUEST_URI]);
        }
        echo "<pre>Purging $url\n";
        $ch = curl_init();
        $options = array(
                        CURLOPT_URL => $url,
                        CURLOPT_HEADER => false,
                        CURLOPT_CUSTOMREQUEST => 'PURGE',
                        CURLOPT_SSL_VERIFYPEER=> false,
                    );
        curl_setopt_array($ch, $options);
        curl_exec($ch);
        curl_close($ch);
        echo "<pre>";
        echo '<a href="'.$url.'">Back</a>';
        echo "</pre>";
        exit;
    }
}
add_action('init','bda_purge_modpagespeed');
?>
