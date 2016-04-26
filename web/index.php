<!DOCTYPE html>
<html lang="en-US">
    <head>
        <title>Basic commenting system</title>
        <meta charset="utf-8" />
        <link rel="stylesheet" href="bower_components/bootstrap/dist/css/bootstrap.css" type="text/css" />
        <link rel="stylesheet" href="css/main.css" type="text/css" />

        <!--[if IE]>
          <script src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script><![endif]-->

        <script src="bower_components/jquery/dist/jquery.js"></script>
        <script src="bower_components/bootstrap/dist/js/bootstrap.js"></script>
        <script src="bower_components/linkifyjs/linkify.min.js"></script>
        <script src="bower_components/linkifyjs/linkify-jquery.min.js"></script>
        <script src="js/main.js"></script>
    </head>

    <body id="index">
        <div class="container">
            <div class="row">
                <div class="col-sm-12 col-sm-offset-3">

                    <div id="main" tabindex="1">
                        <h1>Basic commenting system</h1>
                    </div>
                    <div class="row">
                        <div class="col-sm-5 col-sm-offset-1">
                            <form method="post" class="postform">

                                <label for="name" class="required">Your name</label>
                                <input type="text" name="name" id="name" class="form-control" value="" tabindex="1" required="required">

                                <label for="email" class="required">Your email</label>
                                <input type="text" name="email" id="email" class="form-control" value="" tabindex="1" required="required">

                                <label for="message" class="required">Your message</label>
                                <textarea name="message" id="message" class="form-control" rows="2" tabindex="4"  required="required"></textarea>

                                <div class="antSugar"></div>

                                <input name="submit" type="submit" class="btn btn-primary" value="Submit post" />

                            </form>
                        </div>
                    </div>
                    <script>
                        $(document).ready(function () {
                            submitFormAjax(".postform", "displayPost", "ajaxProcessor.php?action=create&resource=post");
                            displayHoneypot(".antSugar", "");
                        });
                    </script>
                    <br>
                    <div class="posts">
                        <?php
                        require_once __DIR__ . '/../Service/Post.php';

                        use Service\Post;

$posts = Post::getPosts();
                        foreach ($posts as $post):
                            $postId = $post["id"]
                            ?>
                            <section id="content_<?php echo($postId); ?>">


                                <div class="row">
                                    <div class="col-sm-1">
                                        <div class="thumbnail">
                                            <img class="img-responsive user-photo" src="img/avatar.png">
                                        </div><!-- /thumbnail -->
                                    </div><!-- /col-sm-1 -->

                                    <div class="col-sm-5">
                                        <div class="panel panel-default">
                                            <div class="panel-heading">
                                                <strong><a href="mailto:<?php echo($post["email"]); ?>"><?php echo($post["name"]); ?></a></strong> <span class="text-muted"><?php echo( date('d F Y H:i', strtotime($post['created'])) ); ?></span>
                                            </div>
                                            <div class="panel-body">
                                                <p class="notLinkified"><?php echo($post["message"]); ?></p>
                                            </div><!-- /panel-body -->


                                            <section class="col-sm-12" id="comments_<?php echo($postId); ?>">

                                                <?php
                                                $comments = $post["comments"];
                                                $hasComments = (bool) count($comments);
                                                ?>
                                                <ol id="posts-list_<?php echo($postId); ?>" class="list-unstyled <?php echo($hasComments ? ' has-comments' : ''); ?>">
                                                    <li class="no-comments">Be the first to add a comment.</li>
                                                    <?php foreach ($comments as $comment): ?>
                                                        <div class="panel panel-default">
                                                            <div class="panel-heading">
                                                                <strong><?php echo($comment["name"]); ?></strong> <span class="text-muted"><?php echo( date('d F Y H:i', strtotime($comment['created'])) ); ?></span>
                                                            </div>
                                                            <div class="panel-body">
                                                                <p class="notLinkified"><?php echo($comment["message"]); ?></p>
                                                            </div><!-- /panel-body -->    
                                                        </div>   

                                                    <?php endforeach; ?>
                                                </ol>

                                                <div id="respond_<?php echo($postId); ?>">

                                                    <h3>Leave a Comment</h3>

                                                    <form method="post" class="commentform" id="commentform_<?php echo($postId); ?>">

                                                        <label for="name_<?php echo($postId); ?>" class="required">Your name</label>
                                                        <input type="text" name="name_<?php echo($postId); ?>" id="name_<?php echo($postId); ?>" class="form-control" value="" tabindex="1" required="required">

                                                        <label for="message_<?php echo($postId); ?>" class="required">Your message</label>
                                                        <textarea name="message_<?php echo($postId); ?>" id="message_<?php echo($postId); ?>" class="form-control" rows="2" tabindex="4"  required="required"></textarea>

                                                        <div class="antSugar_<?php echo($postId); ?>"></div>

                                                        <input type="hidden" name="post_id" value="<?php echo($postId); ?>" />
                                                        <input name="submit_<?php echo($postId); ?>" class="btn btn-primary" type="submit" value="Submit comment" />

                                                    </form>
                                                </div>
                                                <script>
                                                    $(document).ready(function () {
                                                        submitFormAjax("#commentform_<?php echo($postId); ?>", "displayComment", "ajaxProcessor.php?action=create&resource=comment");
                                                        displayHoneypot(".antSugar_<?php echo($postId); ?>", <?php echo($postId); ?>);
                                                    });
                                                </script>
                                            </section>
                                        </div><!-- /panel panel-default -->
                                    </div><!-- /col-sm-5 -->
                                </div><!-- /row -->

                            </section>
                            <br>
                        <?php endforeach; ?>
                    </div>
                    <script>
                        $(document).ready(function () {
                            linkifyText();
                        });
                    </script>
                </div>
            </div>
        </div>
    </body>
</html>