<?php
/**
 * Template.php: template overrides and preprocess functions go here.
 */


/**
 * template_preprocess_page()
 */
function grinnell_preprocess_page(&$variables) {
  // base path
  global $base_path;
  
  // link and logo to grinnell college site
  $variables['grinnell_home'] = l(t('Grinnell College'), 'http://www.grinnell.edu/', array('attributes' => array('class' => 'grinnell-home', 'title' => t('Grinnell College'))));
  
  // check if the option is checked and if islandora_solr_search is enabled
  if (theme_get_setting('islandora_solr') == 1 AND module_exists('islandora_solr_search')) {
    $variables['islandora_solr_search_simple'] = drupal_get_form('islandora_solr_simple_search_form');
  }
  
  // check if the option is checked and if islandora_solr_search is enabled
  if (theme_get_setting('contact_form') == 1 AND module_exists('contact')) {
    $variables['contact_form'] = _grinnell_contact_form();
  }
  
  // check if custom banner is required and if banner is added
  if (theme_get_setting('use_banner') == 1 AND theme_get_setting('banner_path')) {
    $variables['body_classes'] .= ' custom-banner';
    $variables['banner'] = $base_path . theme_get_setting('banner_path');
  }
  
  // check  if banner needs to be stretched to full width AND if the banner is required
  if (theme_get_setting('stretch_banner') == 1 AND theme_get_setting('use_banner') == 1) {
    $variables['body_classes'] .= ' stretch-banner';
  }
  
  // check if the color is overridden
  if (theme_get_setting('use_color') == 1 AND theme_get_setting('color_override')) {
    $variables['color_override'] = _grinnell_color_override(theme_get_setting('color_override'));
  }
  
  // MM additions to drop necessary metatags into <head> for all page addresses 
  // ending in grinnell:* or GSPage:*
  //
  // FB::group( "Grinnell theme template.php" );
  
  $trans = array( 'grinnell:' => '::', 'grinnell%3A' => '::', 'GSPage:' => '::' );
  $uri = strtr(  request_uri( ), $trans );
  $GSpage = strstr( request_uri( ), 'GSPage' );
  list( $junk, $pid ) = split( '::', $uri, 2 );
  
  $fullPID = 'grinnell:' . $pid;
  
  if ( !is_null( $pid ))
    {
    $vars = array( );
    $found = dg_seo_fetch_key_metadata( $fullPID, &$vars );
    $tags = '';
    
    if ( $found > 0 )
      {
      // If this is a GSPage... block it from indexing with an appropriate metatag.
      //  This assumes that a 'noindex' page will still be crawled, just not indexed.
      //
      if ( $GSpage )
        $tags = '<meta name="robots" content="noindex" />' . "\n";
      
      $tags .= dg_seo_build_key_metatags( &$vars );
      $tags .= dg_seo_build_citation_metatags( $fullPID );
      drupal_set_html_head( $tags );
      $variables['head'] = drupal_get_html_head( );

      // Now add the abstract and other info to the start of the [content] portion 
      // of the $variables array as series of text divs. With any luck, Google will pick this up for 
      // display with its indexing of our object.  
      // 
      $content = $variables['content'];
      
      $noScript = "Your browser does not support JavaScript!  JavaScript " .
        "and Adobe Flash are required to properly display this content."; 
      $publicationStatement = "This work is part of the Digital Grinnell collection " . 
        "at Grinnell College, Grinnell, Iowa.";
      
      $addition  = "\n"; 
      
      // Don't add this text to GSPage pages.
      //
      if ( !$GSpage )
        {
        if ( strlen( $vars['abstract'] ) > 0 )
          $addition .= '<div class="dg_seo dg_content-1">'. $vars['abstract'] . '</div>';
        if ( strlen( $vars['author'] ) > 0 )
          $addition .= '<div class="dg_seo dg_content-2">'. $vars['author'] . '</div>';
        if ( strlen( $vars['handle'] ) > 0 )
          $addition .= '<div class="dg_seo dg_content-3">'. $vars['handle'] . '</div>';
        $addition .= '<div class="dg_seo dg_content-4">' . $publicationStatement. '</div><br />';
        
       // MM addtions (September 2013) for transcription and cover sheet buttons are now
       // held in the dg_utilities.module file and the dg_custom_controls( ) function.
        
       $addition .= dg_custom_controls( $fullPID, $vars['title'] ); 
       }
        
      // Add this <noscript> tag to all pages.
      //  
      $addition .= '<noscript><div class="dg_seo dg_script_error">' . $noScript . '</div>';
      $addition .= '</noscript>';

      /* Look for the object's <h1>title</h1> string in the content (characteristice of the generic viewer) 
      // and insert the text divs just after it rather than at the top of the content.
      //
      //
      $title = $vars['title'];
      
      $target = '<h1>' . $title . '</h1>';
      
      $parts = split( $target, $content, 2 );
      $found = ( $parts[1] ? TRUE : FALSE );
      
      if ( $found )  
        $variables['content'] = $parts[0] . $target . $addition . $parts[1];    // generic content
      else   */
      
      $variables['content'] = $addition . $content;                           
      
      // Now find the body closing tag and add a table entry (single cell)
      // patterned after http://kuscholarworks.ku.edu/dspace/handle/1808/230 to
      // emphasize the use of this object's handle.
      //
        
      $target = '</body>';
      
      list ( $before, $after ) = split( $target, $variables['content'], 2 );
      $handle = $vars['handle'];
      
      if ( strlen( $before ) > 0 && strlen( $handle ) > 0  )
        {
        $addition = "\n" . '<div class="emphasizeHandle"><table class="emphasizeHandle">' .
           '<tr><td class="emphasizeHandle-top">' .  "\n" . 
           'Please use this identifier to cite or link to this object:</td></tr>' .
           "\n" . '<tr><td class="emphasizeHandle-bottom"><code>' . $vars['handle'] . '</code>' .
           "\n" . '</td></tr></table></div><br />';

        $variables['content'] = $before . $target . $addition . $after;  
        }
      }
    }

  // print_r( $variables );
  // FB::table( "Variables Table", $variables );
  // FB::groupEnd( );
  
  //dsm($variables);
  }


/**
 *
 * @return Rendered contact form + wrappers
 */
function _grinnell_contact_form() {
  // set string for contact pull down string
  $contact_string = t('Grinnell College Contact Information');
  $contact_full_string = 'v ' . $contact_string . ' v';
  
  $output = '';

  // contact form drawer
  $output .= '<div id="contact-form-drawer">';
  // include contact form file
  module_load_include('inc', 'contact', 'contact.pages');
  // get contact form
  $output .= drupal_get_form('contact_mail_page');
  $output .= '</div>';
  
  // pull down link
  $output .= '<div class="contact-form-link-wrapper">';
  $output .= l($contact_full_string, '', array('attributes' => array('class' => 'closed')));
  $output .= '</div>';
  
  // Add the string as a js setting
  //drupal_add_js(array('grinnell-theme' => array('contact_string' => $contact_string) ), 'setting');
  
  return $output;
}



/**
 *
 * @param type $color 
 * @return Inline css string
 */
function _grinnell_color_override($color) {
  $output = '
  <style type="text/css">
    a,a.active,#secondary-menu ul li a.active,#primary-menu ul li a.active { color: ' . $color . '}
    #navigation .islandora-solr-search-simple .form-submit, #navigation .islandora-solr-search-simple .form-submit:active { background-color: ' . $color . ' }
  </style>';
  
  return $output;
}