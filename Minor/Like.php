$connect = mysqli_connect("localhost", "root", "", "minor");
 $query = "
      SELECT articles.id, articles.title,
      COUNT(post_likes.id) as likes,
      GROUP_CONCAT(user.name separator '|') as liked
      FROM
      articles
      LEFT JOIN post_likes
      ON post_likes.article = articles.id
      LEFT JOIN user
      ON post_likes.user = user.id
      GROUP BY articles.id
 ";
 $result = mysqli_query($connect, $query);
 while($row = mysqli_fetch_array($result))
 {
      echo '<h3>'.$row["title"].'</h3>';
      echo '<a href="index.php?type=article&id='.$row["id"].'">Like</a>';
      echo '<p>'.$row["likes"].' People like this</p>';
      if(count($row["liked"]))
      {
           $liked = explode("|", $row["liked"]);
           echo '<ul>';
           foreach($liked as $like)
           {
                echo '<li>'.$like.'</li>';
           }
           echo '</ul>';
      }
 }
 if(isset($_GET["type"], $_GET["id"]))
 {
      $type = $_GET["type"];
      $id = (int)$_GET["id"];
      if($type == "article")
      {
           $query = "
           INSERT INTO post_likes (user, article)
           SELECT {$_SESSION['user_id']}, {$id} FROM articles
                WHERE EXISTS(
                     SELECT id FROM articles WHERE id = {$id}) AND
                     NOT EXISTS(
                          SELECT id FROM post_likes WHERE user = {$_SESSION['user_id']} AND article = {$id})
                          LIMIT 1
           ";
           mysqli_query($connect, $query);
           header("location:index.php");
      }
 }
 ?>