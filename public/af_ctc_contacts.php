<?php
/* Template Name: Contacts */
?>
<head>
<?php 
	wp_head();
	global $wpdb;
	$table_name = $wpdb->prefix . 'user_status';
?>
</head>
<body>
	<div class="contacts-page-container">
		<div id="user-list-container">
			<div id="contact-to-call">
				<h1 id="contact"></h1>
				<button id="share-screen-btn" class="btn" onclick="initiateCall(event)">Share Screen</button>
				<button id="call-btn" class="btn" onclick="initiateCall(event)">Call</button>
			</div>
			<h2>online users</h2>
			<ul id="online_users"></ul>
			<h2>offline users</h2>
			<ul id="offline_users"></ul>
		</div>
		<div id="videos">
			<h2>Video Feed</h2>
			<div id="publisher"></div>
			<div id="subscriber"></div>
		</div>
	</div>
	<button id="end-call-btn" class="end-btn" onclick="endCall()">Hang up</button>
<?php wp_footer();?>
</body>
