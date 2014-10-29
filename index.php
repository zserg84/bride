<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">

<html lang="ru">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<script src="assets/js/jquery.js" type="text/javascript"></script>
	<link rel="stylesheet" type="text/css" href="assets/css/main.css" />
</head>
<body>
<div class = 'container'>
	<?php
	ini_set('display_errors', 1);
	error_reporting(E_ALL);
	require_once("idiorm-master/idiorm.php");
	require_once("connection.php");
	require_once("core/SqlDataProvider.php");
	require_once("core/Pager.php");
	require_once("core/Helper.php");

	$db = ORM::get_db();
	$db->exec("SET NAMES utf8");

	$queryParams = array();
	$tagsParam = Helper::getParam('tags', array());
	$exceptTagsParam = Helper::getParam('excepttags', array());
	$tagsStr = $exceptTagsStr = '';
	foreach($tagsParam as $k=>$tag){
		$tagsStr .= $tagsStr ? ', ' : '';
		$tagsStr .= ':tag'.$k;
		$queryParams['tag'.$k] = $tag;
	}
	$tagsStr = $tagsStr ? 'pt.tag_id IN('.$tagsStr.')' : 'TRUE';

	foreach($exceptTagsParam as $k=>$tag){
		$exceptTagsStr .= $exceptTagsStr ? ', ' : '';
		$exceptTagsStr .= ':excepttag'.$k;
		$queryParams['excepttag'.$k] = $tag;
	}
	$exceptTagsStr = $exceptTagsStr ? 'p.id NOT IN(SELECT pt.photo_id FROM photo_tag pt WHERE pt.tag_id IN('.$exceptTagsStr.'))' : 'TRUE';

	$select = "SELECT p.id, count(pl.id) as likeCnt ";
	$from = "
		FROM photo p
			LEFT JOIN photo_like pl ON pl.photo_id = p.id
			LEFT JOIN photo_tag pt ON pt.photo_id = p.id
	";
	$where = " WHERE ".$tagsStr." AND ".$exceptTagsStr;
	$group = " GROUP BY p.id";

	$asc = 0;
	$order = 'date';
	$orderBy = '';
	if(isset($_GET['order'])){
		$order = $_GET['order'];
	}
	if($order == 'like')
		$orderBy = 'likeCnt';
	elseif($order == 'date')
		$orderBy = 'created_at';
	if($orderBy){
		$orderBy = ' ORDER BY '.$orderBy;
		if(isset($_GET['asc'])){
			$asc = $_GET['asc'];
			$orderBy .= ' '.($asc ? 'asc' : 'desc');
			$asc = ($asc == 1) ? 0 : 1;
		}
	}
	$sql = $select . $from . $where . $group . $orderBy;
	$sql = "SELECT p.* FROM photo p INNER JOIN (".$sql.") as tmp ON tmp.id = p.id";

	$total = "SELECT count(1) as cnt FROM photo p INNER JOIN (".$sql.") as tmp ON tmp.id = p.id";
	$stmt = $db->prepare($total);
	$stmt->execute($queryParams);
	$total = $stmt->fetch();
	$total = $total['cnt'];

	$provider = new SqlDataProvider($sql, array(
		'totalItemCount'=>$total,
		'db'=>$db,
		'params'=>$queryParams,
	));

	$photos = $provider->getData();
	$pager = $provider->getPagination()->getPager();
	$pager->renderPager();

	$sortDateUrl = $sortLikeUrl = $_SERVER['PHP_SELF'];

	$request = Helper::urlParamsReplace($_REQUEST, array('asc'=>$asc, 'order'=>'date'));
	$sortDateUrl .= '?'.$request;
	$request = Helper::urlParamsReplace($_REQUEST, array('asc'=>$asc, 'order'=>'like'));
	$sortLikeUrl .= '?'.$request;
	?>
	<a href="<?=$sortDateUrl?>">По дате</a>
	<a href="<?=$sortLikeUrl?>">По лайкам</a>
	<?php
	$class = 'row';
	echo "<div class='$class'>";
	foreach($photos as $k=>$photo):
		if($k && $k%5 === 0)
			echo "</div><div class='$class'>";
		echo '<span>';
		echo '<image src="'.$photo['src'].'">';
		echo '</span>';

	endforeach;

	echo '</div>';
	?>
	<?php
	$tags = ORM::for_table('tag')->find_many();
	?>
	<div>
		<form method="get">
			<select name='tags[]' multiple>
			<?php
				foreach($tags as $tag){
					$selected = in_array($tag->id, $tagsParam) ? 'selected' : '';
					echo "<option value='$tag->id' $selected>$tag->name</option>";
				}
			?>
			</select>

			<select name='excepttags[]' multiple>
				<?php
				foreach($tags as $tag){
					$selected = in_array($tag->id, $exceptTagsParam) ? 'selected' : '';
					echo "<option value='$tag->id' $selected>$tag->name</option>";
				}
				?>
			</select>
			<input type="submit" value="OK"/>
		</form>
	</div>
</div>
</body>
</html>