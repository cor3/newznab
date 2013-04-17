<?php
define('FS_ROOT', realpath(dirname(__FILE__)));
require_once(FS_ROOT . "/../../www/config.php");
require_once(FS_ROOT . "/../../www/lib/framework/db.php");
require_once(FS_ROOT . "/../../www/lib/releases.php");
require_once(FS_ROOT . "/../../www/lib/category.php");

function getReleasez()
{
    $db = new DB();
    return $db->query(sprintf("SELECT * FROM `releases` WHERE `fromname` = 'HaShTaG@nzb.file' AND `name` REGEXP '^[a-fA-F0-9]{32}$' ORDER BY ID DESC LIMIT 0 , 30"));
}

function getReleaseName($md5)
{
    return file_get_contents("http://nzbx.ws/decrypt/x0/?hash=" . $md5);
}

function updaterelease($foundName, $id, $groupname)
{
    $db  = new DB();
    $rel = new Releases();
    $cat = new Category();
    
    $cleanRelName = $rel->cleanReleaseName($foundName);
    $catid        = $cat->determineCategory($groupname, $foundName);
    
    $db->query(sprintf("UPDATE releases SET name = %s,  searchname = %s, categoryID = %d WHERE ID = %d", $db->escapeString($cleanRelName), $db->escapeString($cleanRelName), $catid, $id));
    
}

$results = getReleasez();
foreach ($results as $result) {
    $x = $result['name'];
    if (!strstr($x, '.') == TRUE) {
        if (!strstr($x, ' ') == TRUE) {
            if (!strstr($x, '_') == TRUE) {
                if (!strstr($x, '(') == TRUE) {
                    if (!strstr($x, '-') == TRUE) {
                        $r = getReleaseName($result['name']);
                        if (strlen($r) > 5) {
                            if (!strstr($r, 'cloudflare') == TRUE) {
                                if (strstr($r, '-') == TRUE) {
                                    echo "Release found " . $r . "\n";
                                    updaterelease($r, $result['ID'], $result['name']);
                                }
                            }
                        }
                    }
                }
            }
        }
    }
}
?>
