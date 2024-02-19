<?php 

if ($_REQUEST['q'] == '') {
    add_notice("You must enter at least one search term", "error");
} else if (strlen($_REQUEST['q']) < 3) {
    add_notice("Your search string was too short, please try again", "error");
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
        }
    }
        
    $unique = array_unique($res);

    foreach ($unique as $key => $val) {
        $entries[] = "<tr><td class=summary onclick=\"submitreq(${key})\">" . $val . "</td></tr>";
    }
    if (count($unique) > 0) {
        $search_summary = sprintf(
            _("Found %s songs"), 
            count($unique),
        );
        $content .= '<table border=1>';
        foreach ($entries as $song) {
            $content .= $song;
        }
        $content .= '</table>';
    } else {
        $search_summary = _( "No match found" );
    }
    $content = sprintf(
        '<div class=search-result>
            <div class=result>%s</div>
        </div>',
        $content,
    );
}