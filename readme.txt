## $Id$
##
##        Mod title:  Topic curator
##
##      Mod version:  1.0
##  Works on FluxBB:  1.5.10
##     Release date:  2017-06-18
##      Review date:  YYYY-MM-DD (Leave unedited)
##           Author:  DenisVS (deniswebcomm@gmail.com)
##
##      Description:  
##
##   Repository URL:  http://fluxbb.org/resources/mods/xxx (Leave unedited)
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












#
#---------[ 6. REPLACE WITH ]-------------------------------------------------
#





#
#---------[ 11. BEFORE, ADD ]-------------------------------------------------
#




#
#---------[ 12. SAVE/UPLOAD ]-------------------------------------------------
#
