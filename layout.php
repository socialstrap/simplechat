<?php 

$base_url = Application_Plugin_Common::getFullBaseUrl();

$Storage = new Application_Model_Storage();
$StorageAdapter = $Storage->getAdapter();

$avatar_base = $StorageAdapter->getStoragePath('avatar');

$html = '';

if (! empty($data)) {
	foreach ($data as $line) {
		
		$time_ago = Application_Plugin_Common::getTimeElapsedString($line->timestamp);
		
		// use comments style
		$html .= '<div class="media comments"><div class="avatar small pull-left"><a href="'.$base_url.'/'.$line->username.'"><img src="'.$avatar_base.$line->avatar.'"></a></div><div class="media-body"><a href="'.$base_url.'/'.$line->username.'">'.$line->screen_name.'</a>: <span class="comment-content content">'.$zview->RenderOutput($line->text).'</span><div class="pull-right"><span class="comment-date"><small style="margin: 15px">'.$time_ago.'</small></span></div></div></div><div class="clearfix"></div>';
	}
}