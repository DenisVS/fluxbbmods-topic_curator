<?php
// $Id$
define('PUN_ROOT', dirname(__FILE__).'/');
require PUN_ROOT.'include/common.php';

if ($pun_user['is_admmod'] && $_GET["query"] == "ajax" && isset($_POST['term'])) 
{
  //get search term
  $searchTerm = $_POST['term'];
  $query = $db->query("SELECT username, id FROM " . $db->prefix . "users WHERE username LIKE '%".$searchTerm."%' AND id <> 1 AND id <> 2");
  while ($row = $query->fetch_assoc()) 
  {
    $user['id'] = $row['id'];
    $user['value'] = $row['username'];
    $user['label'] = "{$row['username']}";
    $data[] = $user;
  }
  //return json data
  echo json_encode($data);
	die();
}

$curator_main = true;
// Load the viewtopic.php language file
require PUN_ROOT.'lang/'.$pun_user['language'].'/topic_curator.php';

// Interface for admins and moderators
if ($pun_user['is_admmod'] && isset($_GET['tid']) && $_GET['action'] == "appoint")	
{
  confirm_referrer('viewtopic.php');
	check_csrf($_GET['csrf_token']);

	//Load header
	require PUN_ROOT.'header.php';
  $tid = $_GET['tid'];
  // Fetch some info about the post, the topic and the forum
  $result = $db->query("SELECT t.id, t.subject, t.flags , t.mod_by, t.forum_id, f.id AS f_id, f.moderators, u.username AS user_name FROM " . $db->prefix . "forums AS f INNER JOIN " . $db->prefix . "topics AS t LEFT JOIN " . $db->prefix . "users AS u ON t.mod_by=u.id AND f.id=t.forum_id WHERE t.id ='".$tid."'");

  $cur_topic = $db->fetch_assoc($result);

  $mods_array = ($cur_topic['moderators'] != '') ? unserialize($cur_topic['moderators']) : array();

  if ($pun_user['g_id'] != PUN_ADMIN && (isset($mods_array) ?  !array_key_exists($pun_user["username"], $mods_array) : false))
  {
    	message($lang_common['No permission'], false, '403 Forbidden');
  }

?>
	<div id="editform" class="blockform">
	<h2><span><?php echo $lang_topic_curator['Assign and set permissions']; ?></span></h2>
	<div class="box">
		<form id="edit" method="post" action="topic_curator.php?tid=<?php echo $_GET['tid'] ?>&csrf_token=<?php echo pun_csrf_token(); ?>&action=edit" onsubmit="return process_form(this)">
			<div class="inform">
				<fieldset>
					<legend><?php echo $lang_common['Options'] ?></legend>
					<div class="infldset">
<?php

  if (isset($cur_topic["mod_by"]) ? ($cur_topic["mod_by"] != 0 ) : false )
  {
    echo $lang_topic_curator['Topic'].' <b>'.$cur_topic["subject"]. "</b> ". $lang_topic_curator['By'] ."<br />";
  } 
  else
  {
    echo $lang_topic_curator['Assign curator'] .' <b>'.$cur_topic["subject"]. "</b>  " ."<br />";
  }

?>

<link rel="stylesheet" href="//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css">
<script src="//code.jquery.com/jquery-1.10.2.js"></script>
<script src="//code.jquery.com/ui/1.11.4/jquery-ui.js"></script>
<script type="text/javascript">
$(function() {
	$( "#usernameForm" ).autocomplete({
		source: function (request, response) {
			$.ajax({
				url: "topic_curator.php?query=ajax",
				type: "POST",
				data: request,
				dataType: "json",
				success: function (data) {
					response($.map(data, function (el) {
						return {
							label:	el.label,
							value:	el.value,
							id:	el.id
						};
					}));
				}
			});
		},
		select: function(event, ui){
			$("#useridForm").val(ui.item.id);
		},
	});
});

</script>
<input type=text name="username"  id="usernameForm" value="<?php echo $cur_topic['user_name']; ?>" tabindex="<?php echo $cur_index++; ?>">
<div style="display: none"><input type=text name="userid"  id="useridForm"  type="hidden" value="<?php echo $cur_topic["mod_by"]; ?>" tabindex="<?php echo $cur_index++; ?>"></div>
<?php

  $flags =  str_split( trim (sprintf("%'.08d\n", decbin ($cur_topic["flags"]))));
  if ($flags[7] == 1) $checkbox[7] =' checked="checked" ';
  if ($flags[6] == 1) $checkbox[6] =' checked="checked" ';
  if ($flags[5] == 1) $checkbox[5] =' checked="checked" ';
  if ($flags[4] == 1) $checkbox[4] =' checked="checked" ';

  $checkboxes[] = '<label><input type="checkbox" name="allow_delete_posts" value="1" tabindex="'.($cur_index++).'" '. $checkbox[7]. ' />'.$lang_topic_curator['Allow delete other'].'<br /></label>';
  $checkboxes[] = '<label><input type="checkbox" name="allow_close_topic" value="1" tabindex="'.($cur_index++).'" '. $checkbox[6]. ' />'.$lang_topic_curator['Allow close open'].'<br /></label>';
  $checkboxes[] = '<label><input type="checkbox" name="allow_edit_self" value="1" tabindex="'.($cur_index++).'" '. $checkbox[5]. ' />'.$lang_topic_curator['Allow edit own'].'<br /></label>';
  $checkboxes[] = '<label><input type="checkbox" name="allow_edit_others" value="1" tabindex="'.($cur_index++).'" '. $checkbox[4]. ' />'.$lang_topic_curator['Allow edit other'].'<br /></label>';

?>
						<div class="rbox">
							<?php echo implode("\n\t\t\t\t\t\t\t", $checkboxes)."\n" ?>
						</div>
						<div ><i><?php echo $lang_topic_curator['Only assign'];?></i></div>
					</div>
				</fieldset>
			</div>
			<p class="buttons"><input type="submit" name="submit" value="<?php echo $lang_common['Submit'] ?>" tabindex="<?php echo $cur_index++ ?>" accesskey="s" /> 
			<?php	if (isset($cur_topic["mod_by"]) ? ($cur_topic["mod_by"] != 0 ) : false )  {	?>
			<input type="submit" name="remove_curator" value="<?php echo $lang_topic_curator['Revoke'] ?>" tabindex="<?php echo $cur_index++ ?>" accesskey="p" /> <a href="javascript:history.go(-1)"><?php } echo $lang_common['Go back'] ?></a></p>
		</form>
	</div>
</div>
<?php
  // Load footer
  require PUN_ROOT.'footer.php';
	die();
}

// Edit part (for admins and moderators)
if ($pun_user['is_admmod'] && isset($_GET['tid']) && $_GET['action'] == "edit" && isset ($_POST))	
{
  confirm_referrer('topic_curator.php');
	check_csrf($_GET['csrf_token']);
	//Load header
	require PUN_ROOT.'header.php';
	$tid = $_GET['tid'];

  if (isset($_POST["remove_curator"])) 
  {
    $result=$db->query('UPDATE '.$db->prefix.'topics SET mod_by=\'0\', flags=\'0\' WHERE id='.$_GET['tid']) or error('Unable to update post', __FILE__, __LINE__, $db->error());
  //echo $result;
    redirect('viewtopic.php?id='.$_GET['tid'], $lang_post['Edit redirect']);
    die();
  }
  $flags[] = '0';
  $flags[] = '0';
  $flags[] = '0';
  $flags[] = '0';
  $flags[] = $_POST["allow_edit_others"];
  $flags[] = $_POST["allow_edit_self"];
  $flags[] = $_POST["allow_close_topic"];
  $flags[] = $_POST["allow_delete_posts"];
  $binflags = intval($flags[0]).intval($flags[1]).intval($flags[2]).intval($flags[3]).intval($flags[4]).intval($flags[5]).intval($flags[6]).intval($flags[7]);
  $decflags = bindec($binflags);
	$result=$db->query('UPDATE '.$db->prefix.'topics SET mod_by=\''.$_POST['userid'].'\', flags=\''.$decflags.'\' WHERE id='.$_GET['tid']) or error('Unable to update post', __FILE__, __LINE__, $db->error());
	redirect('viewtopic.php?id='.$_GET['tid'], $lang_post['Edit redirect']);
	die();
}

// Open/close topic by curator
if (!$pun_user['is_guest'] && isset($_GET['csrf_token']) && (isset($_GET['open']) || isset($_GET['close'])))
{
		confirm_referrer('viewtopic.php');
		check_csrf($_GET['csrf_token']);
		if (isset($_REQUEST['open']))
		{
      $tid = $_REQUEST['open'];
    }
    else if (isset($_REQUEST['close']))
    {
      $tid = $_REQUEST['close'];
    }

	// Fetch some info about the post, the topic and the forum
	$result = $db->query("SELECT id, mod_by, flags FROM " . $db->prefix . "topics WHERE id ='".$tid."'");
	$cur_topic = $db->fetch_assoc($result);
	$flags =  str_split( trim (sprintf("%'.08d\n", decbin ($cur_topic["flags"]))));
	if ($flags[6] == 1  && $cur_topic['mod_by'] == $pun_user['id'] && isset ($tid))	
	{
		if (isset($_REQUEST['open']))
		{
			$db->query('UPDATE '.$db->prefix.'topics SET closed=0 WHERE id='.$tid) or error('Unable to close topics', __FILE__, __LINE__, $db->error());
			redirect('viewtopic.php?id='.$tid, $lang_misc['Open topics redirect']);
		}
    else if (isset($_REQUEST['close']))
		{
			$db->query('UPDATE '.$db->prefix.'topics SET closed=1 WHERE id='.$tid) or error('Unable to close topics', __FILE__, __LINE__, $db->error());
			redirect('viewtopic.php?id='.$tid, $lang_misc['Close topics redirect']);
		}
	}
}
message($lang_common['No permission'], false, '403 Forbidden');
