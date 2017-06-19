## $Id$
##
##        Mod title:  Topic curator
##
##      Mod version:  1.0
##  Works on FluxBB:  1.5.10
##     Release date:  2017-06-18
##           Author:  DenisVS (deniswebcomm@gmail.com)
##
##      Description:  This mod allows the administrator and moderators to 
##                    assign a curator of a topic chosen from regular users
##                    who can manage a particular topic, except users ban.
##
##
##   Affected files:  delete.php
##                    edit.php
##                    footer.php
##                    viewtopic.php
##                    
##
##       Affects DB:  Yes
##
##            Notes:  This is just a template. Don't try to install it! Rows
##                    in this header should be no longer than 78 characters
##                    wide. Edit this file and save it as readme.txt. Include
##                    the file in the archive of your mod. The mod disclaimer
##                    below this paragraph must be left intact. This space
##                    would otherwise be a good space to brag about your mad
##                    modding skills :)
##
##       DISCLAIMER:  Please note that "mods" are not officially supported by
##                    FluxBB. Installation of this modification is done at 
##                    your own risk. Backup your forum database and any and
##                    all applicable files before proceeding.
##
##


#
#---------[ 1. UPLOAD ]-------------------------------------------------------
#

install_mod.php
lang/English/topic_curator.php
topic_curator.php

#
#---------[ 2. RUN ]----------------------------------------------------------
#

install_mod.php


#
#---------[ 3. DELETE ]-------------------------------------------------------
#

install_mod.php


#
#---------[ 4. OPEN ]---------------------------------------------------------
#

delete.php

#
#---------[ 5. FIND (line: 22) ]---------------------------------------------
#

$result = $db->query('SELECT f.id AS fid, f.forum_name, f.moderators, f.redirect_url, fp.post_replies, fp.post_topics, t.id AS tid, t.subject, t.first_post_id, t.closed, p.posted, p.poster, p.poster_id, p.message, p.hide_smilies FROM '.$db->prefix.'posts AS p INNER JOIN '.$db->prefix.'topics AS t ON t.id=p.topic_id INNER JOIN '.$db->prefix.'forums AS f ON f.id=t.forum_id LEFT JOIN '.$db->prefix.'forum_perms AS fp ON (fp.forum_id=f.id AND fp.group_id='.$pun_user['g_id'].') WHERE (fp.read_forum IS NULL OR fp.read_forum=1) AND p.id='.$id) or error('Unable to fetch post info', __FILE__, __LINE__, $db->error());

#
#---------[ 6. REPLACE WITH ]-------------------------------------------------
#

$result = $db->query('SELECT f.id AS fid, f.forum_name, f.moderators, f.redirect_url, fp.post_replies, fp.post_topics, t.id AS tid, t.subject, t.first_post_id, t.closed, t.mod_by, t.flags, p.posted, p.poster, p.poster_id, p.message, p.hide_smilies FROM '.$db->prefix.'posts AS p INNER JOIN '.$db->prefix.'topics AS t ON t.id=p.topic_id INNER JOIN '.$db->prefix.'forums AS f ON f.id=t.forum_id LEFT JOIN '.$db->prefix.'forum_perms AS fp ON (fp.forum_id=f.id AND fp.group_id='.$pun_user['g_id'].') WHERE (fp.read_forum IS NULL OR fp.read_forum=1) AND p.id='.$id) or error('Unable to fetch post info', __FILE__, __LINE__, $db->error());


#---------[ 7. FIND (line: 35) ]---------------------------------------------

$is_topic_post = ($id == $cur_post['first_post_id']) ? true : false;

#
#---------[ 8. AFTER, ADD ]---------------------------------------------------
#

// Are we the curator of our own topic?
if ($cur_post['mod_by'] == $pun_user['id'])
{
	$curator_rights["allow_delete_posts"] = false;
	if ($cur_post['mod_by'] == $pun_user['id'] )
	{	
		$flags =  str_split( trim (sprintf("%'.08d\n", decbin ($cur_post["flags"]))));
		if ($flags[7] == 1)	$curator_rights["allow_delete_posts"] = true;
	}

	if ($curator_rights["allow_delete_posts"])
	{
		// Fetch some info about poster group
		$result = $db->query('SELECT group_id FROM '.$db->prefix.'users WHERE id='.$cur_post['poster_id']) or error('Unable to fetch user info', __FILE__, __LINE__, $db->error());
		if (!$db->num_rows($result))
			message($lang_common['Bad request'], false, '404 Not Found');

		$poster_group = $db->fetch_assoc($result)["group_id"];

		if ($poster_group != PUN_ADMIN && (isset($mods_array) ?  !array_key_exists($cur_post["poster"], $mods_array) : false))
		{	
			// This dirty trick to simplify editing.
      $pun_user['g_deledit_interval'] = 0;  // For Timelimit by Visman			
			$pun_user['g_delete_posts'] = '1';
			$pun_user['g_delete_topics'] = '1';
			$cur_post['poster_id'] = $pun_user['id'];
		}
	}
}

#
#---------[ 9. OPEN ]---------------------------------------------------------
#

edit.php

#
#---------[ 10. FIND (line: 22) ]---------------------------------------------
#

$result = $db->query('SELECT f.id AS fid, f.forum_name, f.moderators, f.redirect_url, fp.post_replies, fp.post_topics, t.id AS tid, t.subject, t.posted, t.first_post_id, t.sticky, t.closed, p.poster, p.poster_id, p.message, p.hide_smilies FROM '.$db->prefix.'posts AS p INNER JOIN '.$db->prefix.'topics AS t ON t.id=p.topic_id INNER JOIN '.$db->prefix.'forums AS f ON f.id=t.forum_id LEFT JOIN '.$db->prefix.'forum_perms AS fp ON (fp.forum_id=f.id AND fp.group_id='.$pun_user['g_id'].') WHERE (fp.read_forum IS NULL OR fp.read_forum=1) AND p.id='.$id) or error('Unable to fetch post info', __FILE__, __LINE__, $db->error());

#
#---------[ 11. REPLACE WITH ]-------------------------------------------------
#

$result = $db->query('SELECT f.id AS fid, f.forum_name, f.moderators, f.redirect_url, fp.post_replies, fp.post_topics, t.id AS tid, t.subject, t.posted, t.first_post_id, t.sticky, t.closed, t.mod_by, t.flags, p.poster, p.poster_id, p.message, p.hide_smilies FROM '.$db->prefix.'posts AS p INNER JOIN '.$db->prefix.'topics AS t ON t.id=p.topic_id INNER JOIN '.$db->prefix.'forums AS f ON f.id=t.forum_id LEFT JOIN '.$db->prefix.'forum_perms AS fp ON (fp.forum_id=f.id AND fp.group_id='.$pun_user['g_id'].') WHERE (fp.read_forum IS NULL OR fp.read_forum=1) AND p.id='.$id) or error('Unable to fetch post info', __FILE__, __LINE__, $db->error());

#
#---------[ 12. FIND (line: 32) ]--------------------------------------------
#

$can_edit_subject = $id == $cur_post['first_post_id'];

#
#---------[ 13. AFTER, ADD ]---------------------------------------------------
#

// Are we the curator of our own topic?
if ($cur_post['mod_by'] == $pun_user['id'])
{
	$curator_rights["allow_post replies"] =	$curator_rights["allow_delete_posts"] = $curator_rights["allow_close_topic"] = $curator_rights["allow_edit_self"] = $curator_rights["allow_edit_others"] = false;
	if ($cur_post['mod_by'] == $pun_user['id'] )
	{	
		$flags =  str_split( trim (sprintf("%'.08d\n", decbin ($cur_post["flags"]))));
		if ($flags[7] == 1)	$curator_rights["allow_delete_posts"] = true;
		if ($flags[6] == 1)	$curator_rights["allow_close_topic"] = true;
		if ($flags[5] == 1)	$curator_rights["allow_edit_self"] = true;
		if ($flags[4] == 1)	$curator_rights["allow_edit_others"] = true;
	}

	if ($curator_rights["allow_edit_self"] || $curator_rights["allow_edit_others"])
	{
		// Fetch some info about poster group
		$result = $db->query('SELECT group_id FROM '.$db->prefix.'users WHERE id='.$cur_post['poster_id']) or error('Unable to fetch user info', __FILE__, __LINE__, $db->error());
		if (!$db->num_rows($result))
			message($lang_common['Bad request'], false, '404 Not Found');

		$poster_group = $db->fetch_assoc($result)["group_id"];

		if ($poster_group != PUN_ADMIN && (isset($mods_array) ?  !array_key_exists($cur_post["poster"], $mods_array) : false))
		{	
			// This dirty trick to simplify editing.
      $pun_user['g_deledit_interval'] = 0;  // For Timelimit by Visman
			$pun_user['g_edit_posts'] = '1';
			if ($curator_rights["allow_edit_others"])	{
				$cur_post['poster_id'] = $pun_user['id'];
			}
		}
	}
}

#
#---------[ 14. OPEN ]---------------------------------------------------------
#

footer.php

#
#---------[ 15. FIND (line: 58) ]---------------------------------------------
#

		echo "\t\t\t".'</dl>'."\n";
	}

	echo "\t\t\t".'<div class="clearer"></div>'."\n\t\t".'</div>'."\n";
}

#
#---------[ 16. REPLACE WITH ]-------------------------------------------------
#

		echo "\t\t\t\t".'<dd><span><a href="topic_curator.php?tid='.$id.'&amp;action=appoint'.$token_url.'">'.$lang_topic_curator_viewtopic['Assign curator'].'</a></span></dd>'."\n";
		echo "\t\t\t".'</dl>'."\n";
	}

	echo "\t\t\t".'<div class="clearer"></div>'."\n\t\t".'</div>'."\n";
}

if ($curator_rights["allow_close_topic"])
{
	echo "\t\t".'<div id="modcontrols" class="inbox">'."\n";
	echo "\t\t\t".'<dl>'."\n";
	echo "\t\t\t\t".'<dt><strong>'.$lang_topic['Mod controls'].'</strong></dt>'."\n";
	if ($cur_topic['closed'] == '1')
		echo "\t\t\t\t".'<dd><span><a href="topic_curator.php?open='.$id.$token_url.'">'.$lang_common['Open topic'].'</a></span></dd>'."\n";
	else
		echo "\t\t\t\t".'<dd><span><a href="topic_curator.php?close='.$id.$token_url.'">'.$lang_common['Close topic'].'</a></span></dd>'."\n";
	
	echo "\t\t\t".'</dl>'."\n";
	echo "\t\t\t".'<div class="clearer"></div>'."\n\t\t".'</div>'."\n";
}

#
#---------[ 17. OPEN ]---------------------------------------------------------
#

viewtopic.php

#
#---------[ 18. FIND (line: 84) ]---------------------------------------------
#


// Fetch some info about the topic
if (!$pun_user['is_guest'])
	$result = $db->query('SELECT t.subject, t.closed, t.num_replies, t.sticky, t.first_post_id, f.id AS forum_id, f.forum_name, f.moderators, fp.post_replies, s.user_id AS is_subscribed FROM '.$db->prefix.'topics AS t INNER JOIN '.$db->prefix.'forums AS f ON f.id=t.forum_id LEFT JOIN '.$db->prefix.'topic_subscriptions AS s ON (t.id=s.topic_id AND s.user_id='.$pun_user['id'].') LEFT JOIN '.$db->prefix.'forum_perms AS fp ON (fp.forum_id=f.id AND fp.group_id='.$pun_user['g_id'].') WHERE (fp.read_forum IS NULL OR fp.read_forum=1) AND t.id='.$id.' AND t.moved_to IS NULL') or error('Unable to fetch topic info', __FILE__, __LINE__, $db->error());
else
	$result = $db->query('SELECT t.subject, t.closed, t.num_replies, t.sticky, t.first_post_id, f.id AS forum_id, f.forum_name, f.moderators, fp.post_replies, 0 AS is_subscribed FROM '.$db->prefix.'topics AS t INNER JOIN '.$db->prefix.'forums AS f ON f.id=t.forum_id LEFT JOIN '.$db->prefix.'forum_perms AS fp ON (fp.forum_id=f.id AND fp.group_id='.$pun_user['g_id'].') WHERE (fp.read_forum IS NULL OR fp.read_forum=1) AND t.id='.$id.' AND t.moved_to IS NULL') or error('Unable to fetch topic info', __FILE__, __LINE__, $db->error());

#
#---------[ 19. REPLACE WITH ]-------------------------------------------------
#

// Fetch some info about the topic
if (!$pun_user['is_guest'])
	$result = $db->query('SELECT t.subject, t.closed, t.num_replies, t.sticky, t.first_post_id, t.mod_by, t.flags, f.id AS forum_id, f.forum_name, f.moderators, fp.post_replies, s.user_id AS is_subscribed FROM '.$db->prefix.'topics AS t INNER JOIN '.$db->prefix.'forums AS f ON f.id=t.forum_id LEFT JOIN '.$db->prefix.'topic_subscriptions AS s ON (t.id=s.topic_id AND s.user_id='.$pun_user['id'].') LEFT JOIN '.$db->prefix.'forum_perms AS fp ON (fp.forum_id=f.id AND fp.group_id='.$pun_user['g_id'].') WHERE (fp.read_forum IS NULL OR fp.read_forum=1) AND t.id='.$id.' AND t.moved_to IS NULL') or error('Unable to fetch topic info', __FILE__, __LINE__, $db->error());
else
	$result = $db->query('SELECT t.subject, t.closed, t.num_replies, t.sticky, t.first_post_id, t.mod_by, f.id AS forum_id, f.forum_name, f.moderators, fp.post_replies, 0 AS is_subscribed FROM '.$db->prefix.'topics AS t INNER JOIN '.$db->prefix.'forums AS f ON f.id=t.forum_id LEFT JOIN '.$db->prefix.'forum_perms AS fp ON (fp.forum_id=f.id AND fp.group_id='.$pun_user['g_id'].') WHERE (fp.read_forum IS NULL OR fp.read_forum=1) AND t.id='.$id.' AND t.moved_to IS NULL') or error('Unable to fetch topic info', __FILE__, __LINE__, $db->error());

#
#---------[ 20. FIND (line: 91) ]---------------------------------------------
#

$cur_topic = $db->fetch_assoc($result);

#
#---------[ 21. AFTER, ADD ]---------------------------------------------------
#


if (file_exists(PUN_ROOT.'lang/'.$pun_user['language'].'/topic_curator.php'))
	require PUN_ROOT.'lang/'.$pun_user['language'].'/topic_curator.php';
else
	require PUN_ROOT.'lang/English/topic_curator.php';

if (!$pun_user['is_guest'])
{
	$curator_rights["allow_post replies"] =	$curator_rights["allow_delete_posts"] = $curator_rights["allow_close_topic"] = $curator_rights["allow_edit_self"] = $curator_rights["allow_edit_others"] = false;
	if ($cur_topic['mod_by'] == $pun_user['id'] && $cur_topic["flags"])
	{	
		$curator_rights["allow_post replies"] = true;
		$flags =  str_split( trim (sprintf("%'.08d\n", decbin ($cur_topic["flags"]))));
		if ($flags[7] == 1)	$curator_rights["allow_delete_posts"] = true;
		if ($flags[6] == 1)	$curator_rights["allow_close_topic"] = true;
		if ($flags[5] == 1)	$curator_rights["allow_edit_self"] = true;
		if ($flags[4] == 1)	$curator_rights["allow_edit_others"] = true;
	}
}

#
#---------[ 22. FIND (line: 102) ]---------------------------------------------
#

	if (($cur_topic['post_replies'] == '' && $pun_user['g_post_replies'] == '1') || $cur_topic['post_replies'] == '1' || $is_admmod)

#
#---------[ 23. REPLACE WITH ]-------------------------------------------------
#

	if (($cur_topic['post_replies'] == '' && $pun_user['g_post_replies'] == '1') || $cur_topic['post_replies'] == '1' || $is_admmod || $curator_rights['allow_post replies'] )

#
#---------[ 24. FIND (line: 111) ]---------------------------------------------
#

	if ($is_admmod)
		$post_link .= ' / <a href="post.php?tid='.$id.'">'.$lang_topic['Post reply'].'</a>';

#
#---------[ 25. REPLACE WITH ]-------------------------------------------------
#

	if ($is_admmod || $curator_rights["allow_post replies"])
		$post_link .= ' / <a href="post.php?tid='.$id.'">'.$lang_topic['Post reply'].'</a>';

#
#---------[ 26. FIND (line: 144) ]---------------------------------------------
#

	($cur_topic['closed'] == '0' || $is_admmod))

#
#---------[ 27. REPLACE WITH ]-------------------------------------------------
#

	($cur_topic['closed'] == '0' || $is_admmod || $curator_rights["allow_post replies"]))

#
#---------[ 28. FIND (line: 325) ]---------------------------------------------
#

	if (!$is_admmod)
	{
		if (!$pun_user['is_guest'])
			$post_actions[] = '<li class="postreport"><span><a href="misc.php?report='.$cur_post['id'].'">'.$lang_topic['Report'].'</a></span></li>';
 
		if ($cur_topic['closed'] == '0')

#
#---------[ 29. REPLACE WITH ]-------------------------------------------------
#

	if (!$is_admmod)
	{
		if (!$pun_user['is_guest'])
		{
			$action_links['delete'] = false;
			$action_links['edit'] = false;
			$post_actions[] = '<li class="postreport"><span><a href="misc.php?report='.$cur_post['id'].'">'.$lang_topic['Report'].'</a></span></li>';
		}

		if ($cur_topic['closed'] == '0')


#
#---------[ 30. FIND (line: 330) ]---------------------------------------------
#

			{
				if ((($start_from + $post_count) == 1 && $pun_user['g_delete_topics'] == '1') || (($start_from + $post_count) > 1 && $pun_user['g_delete_posts'] == '1'))
					$post_actions[] = '<li class="postdelete"><span><a href="delete.php?id='.$cur_post['id'].'">'.$lang_topic['Delete'].'</a></span></li>';
				if ($pun_user['g_edit_posts'] == '1')
					$post_actions[] = '<li class="postedit"><span><a href="edit.php?id='.$cur_post['id'].'">'.$lang_topic['Edit'].'</a></span></li>';
			}

#
#---------[ 31. REPLACE WITH ]-------------------------------------------------
#

			{
				if ((($start_from + $post_count) == 1 && $pun_user['g_delete_topics'] == '1') || (($start_from + $post_count) > 1 && $pun_user['g_delete_posts'] == '1'))
				{
					$post_actions[] = '<li class="postdelete"><span><a href="delete.php?id='.$cur_post['id'].'">'.$lang_topic['Delete'].'</a></span></li>';
					$action_links['delete'] = true;
				}
				if ($pun_user['g_edit_posts'] == '1')
				{
					$post_actions[] = '<li class="postedit"><span><a href="edit.php?id='.$cur_post['id'].'">'.$lang_topic['Edit'].'</a></span></li>';
					$action_links['edit'] = true;
				}
			}


#
#---------[ 32. FIND (line: 348) ]---------------------------------------------
#

		}
		$post_actions[] = '<li class="postquote"><span><a href="post.php?tid='.$id.'&amp;qid='.$cur_post['id'].'">'.$lang_topic['Quote'].'</a></span></li>';
	}

#
#---------[ 33. REPLACE WITH ]-------------------------------------------------
#

			$action_links['delete'] = true;
			$action_links['edit'] = true;
		}
		$post_actions[] = '<li class="postquote"><span><a href="post.php?tid='.$id.'&amp;qid='.$cur_post['id'].'">'.$lang_topic['Quote'].'</a></span></li>';
	}

if ($curator_rights["allow_post replies"])
{	
	if ($cur_post["g_id"] != PUN_ADMIN && (isset($mods_array) ?  !array_key_exists($cur_post["username"], $mods_array) : false))
	{
		if ($curator_rights["allow_delete_posts"] && !$action_links['delete'])
		{
			$post_actions[] = '<li class="postdelete"><span><a href="delete.php?id='.$cur_post['id'].'">'.$lang_topic['Delete'].'</a></span></li>';
		}
		if ($curator_rights["allow_edit_self"] && !$action_links['edit'] && $cur_post['id'] == $pun_user['id'])
		{
			$post_actions[] = '<li class="postedit"><span><a href="edit.php?id='.$cur_post['id'].'">'.$lang_topic['Edit'].'</a></span></li>';
		}
		if ($curator_rights["allow_edit_others"] && !$action_links['edit'] )
		{
			$post_actions[] = '<li class="postedit"><span><a href="edit.php?id='.$cur_post['id'].'">'.$lang_topic['Edit'].'</a></span></li>';
		}
	}
}


#
#---------[ 34. FIND (line: 376) ]---------------------------------------------
#

						<dd class="usertitle"><strong><?php echo $user_title ?></strong></dd>

#
#---------[ 35. AFTER, ADD ]---------------------------------------------------
#

<?php if ($cur_topic['mod_by'] == $cur_post['poster_id']) {echo "\t\t\t\t\t\t".'<dd class="curator"><strong>'.$lang_topic_curator_viewtopic['Topic curator'].'</strong></dd>'."\n";} ?>

#
#---------[ 36. SAVE/UPLOAD ]-------------------------------------------------
#
