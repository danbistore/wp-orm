# WP ORM
Simple ORM framework for WordPress

```
$param = new DP_Like(array(
	'user_id' => get_current_user_id(),
	'post_id' => $_POST['pid']
	));
$like = WP_ORM::get($like);
if (empty($like)) {
	$like->item_type = $_POST['item_type'];
	WP_ORM::insert($like);
} else {
	$like->datetime = $_POST['item_type'];
	WP_ORM::update($like);
}
```
