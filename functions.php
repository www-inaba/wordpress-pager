<?php
// アイキャッチ画像を使用
add_theme_support('post-thumbnails');
// 幅 683px、高さ 600px、切り抜きモード
set_post_thumbnail_size(706, 500, true);
add_image_size( 'photo', 200, 140, false );
//中サイズ
the_post_thumbnail('medium');
//大サイズ
the_post_thumbnail('large');
//フルサイズ
the_post_thumbnail('full');
function load_jquery_from_local() {
    if ( !is_admin() ) { //管理者ページは除外
        wp_deregister_script('jquery');
        wp_enqueue_script('jquery', get_bloginfo('stylesheet_directory') . '/js/jquery-1.9.1.min.js', array(), '1.9.1');
    }
}
add_action('init', 'load_jquery_from_local');
?>
<?php
//固定ページの画像サイズをリサイズ
add_theme_support('post-thumbnails');
add_image_size('newsside_image', 120, 120, true);
add_image_size('singlesmall_image', 607, 890, false);
add_image_size('single_image', 1100, 645, true);
add_image_size('hottopics_image', 1890, 712, true);
add_image_size('hottopicssp_image', 940, 350, true);
add_image_size('hot_image', 630, 237, false);
add_image_size('profile_image', 1260, 1000, false);
?>
<?php
add_action( 'admin_menu', 'remove_menus' );

function remove_menus(){
    global $current_user;
    get_currentuserinfo();
    if($current_user->user_login=="passe_confirmation"){
        remove_menu_page( 'edit.php' );                   //投稿
        remove_menu_page ('edit.php?post_type=recruitment');//カスタム投稿 タクソノミー
        remove_menu_page ('edit.php?post_type=blog');//カスタム投稿 タクソノミー
        remove_menu_page ('edit.php?post_type=floor');//カスタム投稿 タクソノミー
    }
}
?>
<?php
//ファイル権限
if ( current_user_can('contributor') && !current_user_can('upload_files') ){
add_action('admin_init', 'allow_contributor_uploads');
}

function allow_contributor_uploads() {
$contributor = get_role('contributor');
$contributor->add_cap('upload_files');
}
?>
<?php
//WYSIWYG拡張
add_filter('tiny_mce_before_init', 'tinymce_init');
function tinymce_init( $init ) {
    $init['verify_html'] = false;
    return $init;
}
?>
<?php
// ユーザープロフィールの項目のカスタマイズ
function my_user_meta($wb)
{
    //項目の追加
    $wb['twitter'] = 'twitter';
    $wb['facebook'] = 'facebook';
    $wb['Instagram'] = 'Instagram';
    $wb['blog'] = 'blog';

    return $wb;
}
add_filter('user_contactmethods', 'my_user_meta', 10, 1);
?>
<?php
//パンくず
function the_breadcrumb() {
                echo '<ul id="crumbs">';
        if (!is_home()) {
                echo '<li><a href="';
                echo get_option('home');
                echo '">';
                echo 'ホーム';
                echo "</a></li>";
                if (is_category() || is_single()) {
                        echo '<li>';
                        the_category(' </li><li> ');
                        if (is_single()) {
                                echo "</li><li>";
                                the_title();
                                echo '</li>';
                        }
                } elseif (is_page()) {
                        echo '<li>';
                        echo the_title();
                        echo '</li>';
                }
        }
        elseif (is_tag()) {single_tag_title();}
        elseif (is_day()) {echo"<li>Archive for "; the_time('F jS, Y'); echo'</li>';}
        elseif (is_month()) {echo"<li>Archive for "; the_time('F, Y'); echo'</li>';}
        elseif (is_year()) {echo"<li>Archive for "; the_time('Y'); echo'</li>';}
        elseif (is_author()) {echo"<li>Author Archive"; echo'</li>';}
        elseif (isset($_GET['paged']) && !empty($_GET['paged'])) {echo "<li>Blog Archives"; echo'</li>';}
        elseif (is_search()) {echo"<li>Search Results"; echo'</li>';}
        echo '</ul>';
}
?>
<?php
add_filter('template_include','custom_search_template');
function custom_search_template($template){
  if ( is_search() ){
    $post_types = get_query_var('post_type');
    foreach ( (array) $post_types as $post_type )
      $templates[] = "search-{$post_type}.php";
    $templates[] = 'search.php';
    $template = get_query_template('search',$templates);
  }
  return $template;
}
?>
<?php
//ページャー
//Pagenation
function pagination($pages = '', $range = 2)
{
     $showitems = ($range * 2)+1;//表示するページ数（５ページを表示）

     global $paged;//現在のページ値
     if(empty($paged)) $paged = 1;//デフォルトのページ

     if($pages == '')
     {
         global $wp_query;
         $pages = $wp_query->max_num_pages;//全ページ数を取得
         if(!$pages)//全ページ数が空の場合は、１とする
         {
             $pages = 1;
         }
     }

     if(1 != $pages)//全ページが１でない場合はページネーションを表示する
     {
         echo "<div class=\"pagenation\">\n";
         echo "<ul class=\"pager\">\n";
         //Prev：現在のページ値が１より大きい場合は表示
         if($paged > 1) echo "<li class=\"prev\"><a href='".get_pagenum_link($paged - 1)."'><span class=\"icon-black_arrow_left\"></span></a></li>\n";

        $min = 0;
        $max = 0;
        if( $paged == 1 ){
            $min = 1;
            $max = ($range * 2)+1;
        } else if( $paged == $pages ){
            $min = $paged - ($range * 2);
            $max = $paged;
        } else {
            $min = $paged - $range;
            $max = $paged + $range;
        }

        for ($i=1; $i <= $pages; $i++) {
            if( $min <= $i && $i <= $max){
                echo ($paged == $i)? "<li class=\"active\">".$i."</li>\n":"<li><a href='".get_pagenum_link($i)."'>".$i."</a></li>\n";
            }
         }
        //Next：総ページ数より現在のページ値が小さい場合は表示
        if ($paged < $pages) echo "<li class=\"next\"><a href=\"".get_pagenum_link($paged + 1)."\"><span class=\"icon-pink_arrow\"></span></a></li>\n";
        echo "</ul>\n";
        echo "</div>\n";
     }
}
?>
<?php
/*-------------------------------------------*/
/*投稿一覧で他のユーザーが投稿した投稿を非表示
/*-------------------------------------------*/
function hide_other_posts($wp_query) {
    global $current_screen, $current_user;
    if($current_screen->id != "edit-passepress_blog") {
        return;
    }
    if( in_array("contributor", $current_user->roles)) {


        $wp_query->query_vars['author'] = $current_user->ID;

// var_dump($current_user->ID);

// var_dump($wp_query->query_vars->author);


        // return false;
        return;
    }
}
add_action('pre_get_posts', 'hide_other_posts');
?>