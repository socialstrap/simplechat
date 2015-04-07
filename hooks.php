<?php
/**
 * SimpleChat add-on
 *
 * @package SocialStrap add-on
 * @author Milos Stojanovic
 * @copyright 2014 interactive32.com
 * 
 */

$this->attach('view_head', 10, function($view) {

	if (! Zend_Auth::getInstance()->hasIdentity()) return;

	// turn stream to 'Off' on page load
	$session = new Zend_Session_Namespace('Default');
	$session->addon_simplechat_init = false;
	
	echo '
		<script>
		function addonSimpleChatScroller() {
			var el = $("#simplechat-content");
		
			$(el).scrollTop($(el)[0].scrollHeight);
		
			// iOS FIX
			var iOS_hack = document.createElement("div");
			iOS_hack.style.height = "101%";
			document.body.appendChild(iOS_hack);
			setTimeout(function(){
			    document.body.removeChild(iOS_hack);
			    iOS_hack = null;
			}, 0);
		}
		
		// attach to heartbeat custom event
		$(document).on("postHeartbeat", function (e, response) {
		
			if (response["addon_simplechat"] == undefined) return;
		
			var chat_content = response["addon_simplechat"];
		
			$("#simplechat-content").html(chat_content);
		})
		
		$(document).ready(function(){
			$("#simplechat-form").submit(function() {
						
			var url = $(this).attr("action");
			var data = $(this).serialize();
	
			if (waiting_for_response == true) return false;
			startWaiting();
			
			$.post(url, data, function(response) {
				$("#simplechat-content").html(response);
				addonSimpleChatScroller();
				$("#simplechat-input").val("").focus();
				stopWaiting();
			}, "json");
			
			return false;
			});
		
			// fit to window
			$("#simplechat-modal").on("show.bs.modal", function () {
			    $("#simplechat-content").height($(window).height()/2);
		
				var url = $("#simplechat-form").attr("action");
				var data = {};
				
				// get fist load, switch stream to on
				$.post(url, data, function(response) {
					$("#simplechat-content").html(response);
					addonSimpleChatScroller();
					$("#simplechat-input").val("").focus();
				}, "json");
		
			})
		
			// scroll to bottom and focus
			$("#simplechat-modal").on("shown.bs.modal", function () {
				addonSimpleChatScroller();
				$("#simplechat-input").val("").focus();
			})

		});

		</script>
		';

});

$this->attach('view_body', 10, function($view) {

	if (! Zend_Auth::getInstance()->hasIdentity()) return;
	
	$action = Application_Plugin_Common::getFullBaseUrl().'/addons/'.basename(__DIR__).'/';
	
	echo '
	<div id="simplechat-modal" class="modal fade" role="dialog">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal">&times;</button>
					<h4 class="modal-title">'.$view->translate('GroupChat').'</h4>
				</div>
				<div class="modal-body">
						
					<div id="simplechat-content" style="max-height: 100%; overflow: scroll"></div>
						
					<br />
						
					<form id="simplechat-form" onsubmit="return false" action="'.$action.'" method="post">
	
						<div class="form-group add-comment-input comment">
							<div class="input-field">
								<input type="text" name="simplechat-input" id="simplechat-input" value="" autocomplete="off" class="form-control">
							</div>
						</div>
							
						<div class="add-comment-submit-btn">
							&nbsp;<input type="submit" name="simplechat-submit" id="simplechat-submit" value="'.$view->translate('Post').'" class="submit btn btn-default">
						</div>
					
					</form>
						
				</div>
						


			</div>
		</div>
	</div>

	<button style="position:fixed; bottom: -5px; right: 70px" class="btn btn-success" data-toggle="modal" data-target="#simplechat-modal">
		<b>'.$view->translate('GroupChat').'</b>
	</button>
	';

});


$this->attach('hook_app_heartbeat', 10, function(&$out) {

	$session = new Zend_Session_Namespace('Default');
	if ($session->addon_simplechat_init == false) return;
	
	$data = json_decode(file_get_contents(realpath(dirname(__FILE__)) . "/data.json"));

	// set view
	$zview = Zend_Layout::getMvcInstance()->getView();

	require_once 'layout.php';

	// send data via heartbeat backbone
	$out['addon_simplechat'] = $html;

});

