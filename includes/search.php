<?php 

if ($_REQUEST['q'] == '') {
    add_notice("You must give at least one search term.", "error");
} else if (strlen($_REQUEST['q']) < 3) {
    add_notice("Your search string was too short, please try again.", "error");
} else {
    $content = "";

    $terms = explode(' ',$_REQUEST['q']);
    $no = count($terms);
    $wherestring = '';
    if ($no == 1) {
        $wherestring = "WHERE (combined LIKE \"%" . $terms[0] . "%\")";
    } elseif ($no >= 2) {
            foreach ($terms as $i => $term) {
                if ($i == 0) {
                    $wherestring .= "WHERE ((combined LIKE \"%" . $term . "%\")";
                }
                if (($i > 0) && ($i < $no - 1)) {
                    $wherestring .= " AND (combined LIKE \"%" . $term . "%\")";
                }
                if ($i == $no - 1) {
                    $wherestring .= " AND (combined LIKE \"%" . $term . "%\") AND(artist != 'DELETED'))";
                }
            }

    // } else {
    //     echo "<li>You must enter at least one search term</li>";
    //     die();
    }

    $entries = null;
    $res = array();
    $sql = "SELECT song_id,artist,title,combined FROM songdb $wherestring ORDER BY UPPER(artist), UPPER(title)";
    foreach ($db->query($sql) as $row) {
        if ((stripos($row['combined'],'wvocal') === false) && (stripos($row['combined'],'w-vocal') === false) && (stripos($row['combined'],'vocals') === false)) {
            $res[$row['song_id']] = $row['artist'] . " - " . $row['title'];
            $details[$row['song_id']] = array(
                'artist' => $row['artist'],
                'title' => $row['title'],
            );
        }
    }
        
    $unique = array_unique($res);

    foreach ($unique as $key => $val) {
        $song = $details[$key];
        $artist = $song['artist'];
        $entries[$artist][] = sprintf(
            '<li class=song onclick="submitreq(%s)">%s</li>',
            $key,
            htmlspecialchars($song['title']),
        );
    }
    if (count($res) > 0) {
        $search_summary = sprintf(
            _("Found %s songs"), 
            count($unique),
        );
        $content .= '<ul class=artists>';
        foreach ($entries as $artist => $songs) {
            $content .= sprintf(
                '<li class=artist>%s',
                htmlspecialchars($artist),
            );
            $content .= '<ul class=songs>';
            foreach ($songs as $song) {
                $content .= $song;
            }
            $content .= '</ul></li>';
        }
        $content .= '</ul>';
    } else {
        add_notice(_("Nothing found, try to be less specific. For example, you can type a word in the title instead of the full title.") );
        $search_summary = _( "No result" );
    }
    $content = sprintf(
        '<div class=search-result>
            <div class=result>%s</div>
        </div>',
        $content,
    );
}