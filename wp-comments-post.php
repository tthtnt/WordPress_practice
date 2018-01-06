<?php
/**
 * Handles Comment Post to WordPress and prevents duplicate comment posting.
 *
 * @package WordPress
 */

if ( 'POST' != $_SERVER['REQUEST_METHOD'] ) {
	$protocol = $_SERVER['SERVER_PROTOCOL'];
	if ( ! in_array( $protocol, array( 'HTTP/1.1', 'HTTP/2', 'HTTP/2.0' ) ) ) {
		$protocol = 'HTTP/1.0';
	}

	header('Allow: POST');
	header("$protocol 405 Method Not Allowed");
	header('Content-Type: text/plain');
	exit;
}

/** Sets up the WordPress Environment. */
require( dirname(__FILE__) . '/wp-load.php' );

nocache_headers();

$comment = wp_handle_comment_submission( wp_unslash( $_POST ) );
if ( is_wp_error( $comment ) ) {
//	intval 是获取变量的整数值函数，这里的意思是获取是否有数据错误
	$data = intval( $comment->get_error_data() );
	if ( ! empty( $data ) ) {
//		empty判断为空的时候会返回true,再经过！就会变成false然后就不会执行if语句，但是empty判断不为空的时候会返回false，在经过！就会变成
//		true，所以就会执行if语句
//		如果获取的数据不为空，那么就要输出错误信息
		wp_die( '<p>' . $comment->get_error_message() . '</p>', __( 'Comment Submission Failure' ), array( 'response' => $data, 'back_link' => true ) );
	} else {
		exit;
	}
}

$user = wp_get_current_user();

/**
 * Perform other actions when comment cookies are set.
 *
 * @since 3.4.0
 *
 * @param WP_Comment $comment Comment object.
 * @param WP_User    $user    User object. The user may not exist.
 */
do_action( 'set_comment_cookies', $comment, $user );

$location = empty( $_POST['redirect_to'] ) ? get_comment_link( $comment ) : $_POST['redirect_to'] . '#comment-' . $comment->comment_ID;

/**
 * Filters the location URI to send the commenter after posting.
 *
 * @since 2.0.5
 *
 * @param string     $location The 'redirect_to' URI sent via $_POST.
 * @param WP_Comment $comment  Comment object.
 */
$location = apply_filters( 'comment_post_redirect', $location, $comment );

wp_safe_redirect( $location );
exit;
