<?php
// 610204679@qq.com

$nav_tab = 1;

require_once('./init.php');

$pageTitle = 'Help';
require_once(ROOT . '/mixins/header.php');
mixin_header($pageTitle, 'help', [], $data->extra_msg, $data->extra_msg_type);
?>

<body class="body-index">
	<div class="main-block container">
		<div class="col-md-12 block-page">
			<h1><a id="user-content-midi-piano-online-help" class="anchor" href="#midi-piano-online-help" aria-hidden="true"><span class="octicon octicon-link"></span></a>Midi Piano Online Help</h1>

			<h2><a id="user-content-welcome-to-midi-piano-online" class="anchor" href="#welcome-to-midi-piano-online" aria-hidden="true"><span class="octicon octicon-link"></span></a>Welcome to Midi Piano Online!</h2>

			<p>Midi Piano Online is a website where you can share, download, and even CREATE, EDIT and PLAY with the midi files online!</p>

			<h3><a id="user-content-sharing" class="anchor" href="#sharing" aria-hidden="true"><span class="octicon octicon-link"></span></a>Sharing</h3>

			<p>To create a empty midi file or upload an existing one, just click on the buttons on the navi bar.</p>

			<h3><a id="user-content-discover" class="anchor" href="#discover" aria-hidden="true"><span class="octicon octicon-link"></span></a>Discover</h3>

			<p>In the index page, you can explore the midi files uploaded by others. Just click on the one you interested in and have fun!</p>

			<h3><a id="user-content-view-the-midi-file" class="anchor" href="#view-the-midi-file" aria-hidden="true"><span class="octicon octicon-link"></span></a>View the MIDI file</h3>

			<p>In the view page, you can download the midi file. You can also edit or play the midi file by clicking the Play! button, which will lead you to the player/editor page. To modify the midi based on the current one? You just fork(which means clone) the midi file to your MIDI repository to create a duplicate and enjoy. </p>

			<h3><a id="user-content-playereditor" class="anchor" href="#playereditor" aria-hidden="true"><span class="octicon octicon-link"></span></a>Player/Editor</h3>

			<p>There is two modes: Play Mode and Edit Mode.<br>
				In play mode, you can press the key on your PC keyboard/MIDI Input Device/the screen, to play the GAME and the player will judge your performance.<br>
				In edit mode, when it is recording(click the circle icon in the controller dialog and it will turn red), you can add notes to the midi file. Also, you can drag the bars on the screen to edit, or select some bars and press 'delete' key to delete them. Last but not least, remember to SAVE your work by clicking the FLOPPY ICON in the controller dialog. If the midi file is not owned by you, we will automatically fork this to your repository and save your work!      </p>

			<p>Click on the Go Back button on the navi bar to check out the midi file info.</p>

			<h3><a id="user-content-and-more" class="anchor" href="#and-more" aria-hidden="true"><span class="octicon octicon-link"></span></a>And more...</h3>

			<p>Click the profile to check out your repository.<br>
				You can click on the username to know about others, and filling out your profile is recommended.</p>

			<h3><a id="user-content-feel-free-to-contact-me" class="anchor" href="#feel-free-to-contact-me" aria-hidden="true"><span class="octicon octicon-link"></span></a>Feel free to contact me!</h3>

			<p>My email address is <a href="mailto:garzonou@gmail.com">garzonou@gmail.com</a>. If you find any bugs or give some suggestions, it's welcome and just send am email to me :). I am looking forward to your email.</p>
		</div>
	</div>
</body>

<?php
require_once(ROOT . '/mixins/footer.php');
mixin_footer();
?>
