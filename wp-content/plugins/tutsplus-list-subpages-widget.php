<?php
/*Plugin Name: List Subpages Widget
Description: 这个插件有啥功能我也不知道
Version: 0.1
Author: liusijie
Author URI: http://rachelmccollin.com
License: GPLv2
*/

function tutsplus_check_for_page_tree(){
//	首先检测当前访问的是否是一个页面
	if(is_page()){
		global $post;
//		接着检测该页面是否有父级页面
		if($post->post_parent){
			//获取父级页面名单
//			array_reverse()返回单元顺序相反的数组
			$parents = array_reverse(get_post_ancestors($post->ID));

//			获取最顶级页面
			return $parents[0];
		}
//		返回ID - 如果存在父级页面，就返回最顶级页面ID，否则返回当前页面ID
		return $post->ID;
	}
}


class Tutsplus_List_Pages_Widget extends  WP_Widget{
//__construct函数会构造一个函数。在这个函数里面你可以做出一些定义，比如WordPress小工具的ID、标题和说明
	function __construct(){
		parent::__construct(
//			小工具ID
			'tutsplus_list_pages_widget',
//			小工具名称
			__('List Relate Pages','tutsplus'),
//			小工具选项
			array(
				'description' => __('Identifies where the current page is in the site structure and displays a list of pages in the same section of the site. Only works on Pages.', 'tutsplus')
			)
		);
	}
//form函数会在WordPress小工具界面创建表单，让用户来定制或者激活它。
	function form($instance){
		$defaults = array(
			'depth' => '-1'
		);
		$depth = $instance['depth' ];

//		makeup for form?>
		<p>
			<label
				for="<?php echo $this->get_field_id('depth'); ?>">
				Depth of list:
			</label>
			<input class="widefat" type="text"
			       id="<?php echo $this->get_field_id('depth'); ?>"
			       name="<?php echo $this->get_field_name('depth'); ?>"
			       value="<?php echo esc_attr($depth); ?>">
		</p>
<?php
	}
//update函数确保WordPress能及时更新用户在WordPress小工具界面输入的任何设置
	function update($new_instance,$old_instance){
		$instance = $old_instance;
		$instance['depth'] = strip_tags($new_instance['depth']);
//		strip_tags -从字符串中去除HTML 和 PHP 标记 消毒
		return $instance;
	}
//	widget函数则定义了在网站前端通过WordPress小工具输出的内容
	function widget($args,$instance){
//		extract()从数组中将变量从数组中导入到当前的符号表
//      检查每个键名看是否可以作为一个合法的变量名，同时也检查和符号表总已有的变量名的冲突
		extract($args);
//		echo $before_widget;
//		echo $before_title.'In this section:'.$after_title;
		echo 'In this section:';
//		run a query if on a page
		if(is_page()){
//			run the tutsplus_check_for_page_tree function to fetch top level page
			$ancestor = tutsplus_check_for_page_tree();

//			set the arguments for children of the ancestor page
			$args = array(
				'child_of'=>$ancestor,
				'depth'=>$instance['depth'],
				'title_li'=>'',
			);

//			set a value for get_pages to check if it's empty
			$list_pages = get_pages($args);
//			check if $list_pages has values
			if($list_pages){
//				open a list with the ancestor page at the top
				?>
			<ul class="page-tree">
				<?php //list ancestor page ?>
				<li class="ancestor">
					<a href="<?php echo get_permalink( $ancestor ); ?>"><?php echo get_the_title($ancestor); ?></a>
				</li>
				<?php
//				use wp_list_pages to list subpages of ancestor or current page
				wp_list_pages($args);
//				close the pagfe-tree list
				?>
			</ul>
<?php
			}
		}
	}
}

function tutsplus_register_list_pages_widget(){
	register_widget("Tutsplus_List_Pages_Widget");
}
add_action('widgets_init','tutsplus_register_list_pages_widget');
